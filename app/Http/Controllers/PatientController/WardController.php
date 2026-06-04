<?php

namespace App\Http\Controllers\PatientController;

use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Controller;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Configuration\Relation;
use App\Models\GrnDetails;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use App\Models\Patient\PatientInvestigation;
use App\Models\Patient\PatientLocation;
use App\Models\Product;
use App\Models\WardRequest;
use App\Models\WardRequestDetails;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Modules\Settings\Entities\District;
use Yajra\DataTables\Facades\DataTables;

class WardController extends Controller
{
    public function ward_request()
    {
        $data["title"] = "Patient Investigation";
        $data['investigation'] = [];
        $data['service_type'] = [];
        $data["relations"] = Relation::get();
        $data["district"] = District::get();
        $data["locations"] = PatientLocation::get();
        $data['admitted_patients'] = PatientAdmission::where(["admission_status" => "Admit","is_active"=>1])
           // ->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with(["patient"])->get();
        $data['products'] = Product::with("generic_name")->orderBy("ProductName", "ASC")
            ->where("IsActive",1)
            ->get();
        foreach ($data['products'] as $key => $value){
            //$value->avaliable_qty = GrnDetails::where(["ProductID"=> $value->ProductID])->sum('RemainingQuantity');

        }
        return view("ward.ward_request_for_medicine",$data);
    }

    public function save_ward_request()
    {
        $patient = PatientAdmission::where(["id"=>request()->admission_id])->first();
        $patient_id = $patient->patient_id;
        $invoice_no = $this->generateInvoiceNumber();
        $data = [
            "invoice_no"=>$invoice_no,
            "store_id"=> env('SEHAT_CARD_PHARMACY_STORE_ID'),
            "patient_id" => $patient_id,
            "admission_id" => request()->admission_id,
            "requested_by" => auth()->user()->id,
            "requested_at" => date("Y-m-d H:i:s")
        ];

        $res =  WardRequest::updateOrCreate(
            ["id" => 0],
            $data
        );
        $wr_id = $res->id;
        $list_products = json_decode(request()->list_products);

        if(count($list_products) == 0){
            return response()->json([
                "status" => "empty",
                "message" => "Please Add Some Medicine to complete request."
            ]);
        }

        foreach ($list_products as $key => $value){
            $data = [
                "wr_id" => $wr_id,
                "store_id"=> env('SEHAT_CARD_PHARMACY_STORE_ID'),
                "product_id"    => $value->product_id,
                "quantity"    => $value->quanity,

            ];
            WardRequestDetails::create($data);
        }

        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);

    }

    public function get_list_ward_request()
    {
        $patients = WardRequest::with('patient')->with('user')->where("is_active", 1)

            ->when(request()->from_date, function ($query) {
                $query->whereDate('requested_at','>=',date("Y-m-d",strtotime(request()->from_date)));
            })
            ->when(request()->to_date, function ($query) {
                $query->whereDate('requested_at','<=',date("Y-m-d",strtotime(request()->to_date)));
            })
            ->when(!in_array(auth()->user()->roles->pluck('name')[0],["Super Admin"]),function ($q){
                $q->where(["requested_by"=> auth()->user()->id]);
            })
            ->orderBy("id","desc")
            ;
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                $button = '';
                if ($patient->status == 0) {

                    $button = $button . '<button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>';
                }
                $button = $button . '<a target="_blank" href="' . route('pos.view_ward_request', [$patient->id]) . '"  class="btn btn-sm btn-success"><i class="tf-icons bx bx-pencil"></i></a>';
                return $button;
            })
            ->addColumn("received_amount", function ($patient) {
                return (($patient->sale_price) - ($patient->discount_amount)) * $patient->frequency;

            })
            ->addColumn("print_invoice_number", function ($patient) {
                return '<a target="_blank" href="' . route('pos.print_lab_invoice', [$patient->invoice_no]) . '">' . $patient->invoice_no . '</a>';
            })
            ->rawColumns(["print_invoice_number","received_amount", "actions"])
            ->make(true);
    }

    public function view_ward_request($id)
    {
        /*if(isset($_GET['type'])){

            $datas = Product::orderBy("ProductName", "ASC")->where("IsActive",1)->get();
            foreach ($datas as $key => $cert){
                $cert->newAvaliableQty = GrnDetails::where(["ProductID"=> $cert->ProductID])->sum('RemainingQuantity');
                $this->total_product_purchase_price($cert->ProductID);
                $this->total_product_price($cert->ProductID);
                Product::where(["ProductID"=>$cert->ProductID])->update(["avaliable_quantity"=>$cert->newAvaliableQty]);
            }
        }*/
        $data['wr_id'] = $id;
        $data['ward_request'] = WardRequest::where("id",$id)->first();
        $data['items'] = WardRequestDetails::with(['products'])->where(["wr_id"=>$id])
            ->orderBy("id","desc")
            ->get();
        foreach ($data['items'] as $key => $value){

            $value->avaliable_quantity = (new StockController())->avaliableQuantity($value->product_id);
        }
        $product_ids = $data['items']->pluck("product_id");


        $data['data'] = Product::with(["generic_name"])->orderBy('avaliable_quantity',"desc")
            ->when((count($product_ids) > 0),function ($q) use($product_ids){
                $q->whereNotIn('ProductID',$product_ids);
            })
            ->where("IsActive",1)->get();
        /*foreach ($data['data'] as $key => $value){

            $value->avaliable_quantity = (new StockController())->avaliableQuantity($value->ProductID);
        }*/



        return view("ward.view_ward_request", $data);
    }

    public function print_ward_request($inv_id)
    {
        $data["ward_request"] = WardRequest::with('user')->where("id", $inv_id)->first();
        $data["patient"] = Patient::where("id",$data["ward_request"]->patient_id)->first();
        $data["ward_request_details"] = WardRequestDetails::with("products")->where("wr_id", $inv_id)->get();
      //  dd($data["ward_request_details"]);
        return view("reports.print_ward_request", $data);
    }

    public function add_product_to_ward_request()
    {
        $data = request()->except(["id","_token"]);
        if(request()->quantity == '' || request()->quantity == 0){
            return redirect()->back();
        }
        $exist = WardRequestDetails::where(["wr_id"=>request()->wr_id,"product_id"=>request()->product_id])->exists();
        if($exist){
            return redirect()->back();
        }
        WardRequestDetails::create($data);
        return redirect()->back();
    }

    public function update_ward_request_items()
    {
        WardRequestDetails::whereId(request()->id)->update(["quantity"=>request()->quantity]);
        return redirect()->back();
    }

    public function delete_ward_request_item($id)
    {
        WardRequestDetails::whereId($id)->delete();
        return redirect()->back();
    }

    public function get_list_ward_request_in_pharmacy()
    {
        $patients = WardRequest::with('patient')->with('user')
            ->where("is_active", 1)
            ->where('store_id',env('SEHAT_CARD_PHARMACY_STORE_ID'))
            ->where("status", 0)
            ->where('store_id',env('SEHAT_CARD_PHARMACY_STORE_ID'))
           /* ->when(request()->from_date, function ($query) {
                $query->whereDate('requested_at','>=',date("Y-m-d",strtotime(request()->from_date)));
            })
            ->when(request()->to_date, function ($query) {
                $query->whereDate('requested_at','<=',date("Y-m-d",strtotime(request()->to_date)));
            })*/
            ->orderBy("id","desc")
        ;
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                $button = '';
                if ($patient->status == 0) {
                    $button = $button . '<a  href="javascript:void(0)" data_ward_request_id="'.$patient->id.'"  class="btn btn-sm btn-success view_ward_request">View</a>';

                }
                return $button;
            })

            ->rawColumns(["actions"])
            ->make(true);
    }



    public function generateInvoiceNumber() {

        $appointment = WardRequest::orderBy("id","desc")->first();
        $number = $appointment ? $appointment->id : 0;
        $number = ($number + 1);
        // Calculate the required length (based on the number of digits)
        $currentLength = strlen((string) $number); // Length of the current number
        $paddingLength = max(4, $currentLength + 1); // Start with 4 digits, increase dynamically

        // Pad the number to the calculated length
        $paddedNumber = str_pad($number, $paddingLength, '0', STR_PAD_LEFT);

        // Combine prefix and padded number
        return $paddedNumber;
    }

    public function sync_patient_data()
    {
        $apiUrl = 'http://sehatcard.amch.org.pk/api/v1/updatePatient';

        DB::table('patients')->chunk(20, function ($patients) use ($apiUrl) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($apiUrl, [
                'items' => $patients->toJson(),
            ]);

            // Handle the response as needed
            if ($response->successful()) {
                echo $response->body();
            } else {
                echo "Error Occured";
            }
        });
        /*Patient::chunk(20, function ($patients) use ($apiUrl) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($apiUrl, [
                'items' => $patients->toJson(),
            ]);

            // Handle the response as needed
            if ($response->successful()) {
                 echo $response->body();
            } else {
                echo "Error Occured";
            }
        });*/
    }
   
   
}
