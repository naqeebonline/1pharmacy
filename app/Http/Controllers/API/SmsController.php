<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SendSmsJob;
use App\Models\Configuration\ServiceType;
use App\Models\Patient\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SmsController extends Controller
{

    public function updatePatient()
    {
        $data = Patient::limit(10)->get();


        $items = [];
        if (!is_string(request()->items)) {
            $items = request()->items;
        }else{
            $items = json_decode(request()->items);
        }
     //   dd($items);
       foreach ($items as $key => $value){
           $updateData = collect($value)->except('id')->toArray();
           Patient::updateOrCreate(
               ["id"=>$value->id],
               $updateData
           );
           Patient::where("id",$value->id)->update(["regdate"=>date("Y-m-d H:i:s",strtotime($value->regdate))]);

       }
       return response()->json(["status"=>true,"message"=>"Record updated successfully"],200);
    }

    public function sendSms()
    {

        $api_key = request()->header('X-API-KEY');
        $user = User::select("id")->where(["username"=>request()->header('username'),"password"=>request()->header('token')])->first();
        if(!$user){
            return response()->json(["error"=> true,"message"=>"Unauthorized Access"],401);
        }

        if($api_key != "13051988"){
            return response()->json(["error"=> true,"message"=>"Invalid X-Api Key"],401);
        }

        $requestValidator = Validator::make(request()->all(), [
            'phone_numbers' => 'required',
            'network_type' => 'required',
            'message' => 'required',
        ]);

        if ($requestValidator->fails()) {
            return response()->json(['error' => true, 'message' => implode(' ', $requestValidator->errors()->all())],500);

        }//..... end if() .....//
        if(!in_array(strtolower(request()->network_type),['jazz','zong','telenor','ufone','warid'])){
            return response()->json(['error' => true, 'message' => "Invalid Network operator. Operator must be (Zong,Jazz,Warid,Ufone or Telenor)"]);
        }


        $sms_id = DB::table("sms_info")->insertGetId([
            "user_id"       => $user->id,
            "message"      =>request()->message,
            "network_type"      =>request()->network_type,
            "phone_numbers"      =>request()->phone_numbers,
            "sms_api_response"      =>"",
            "is_sent"      =>0,
        ]);

        SendSmsJob::dispatch($sms_id,request()->phone_numbers, request()->message,request()->network_type);
        return response()->json(["error"=>false,"response"=>"Sms sent successfully and will receive shortly."],200);
    }

    public function jazzGatway()
    {
        $username = '03024064263'; // replace with your actual username
        $password = '03024064263@jazz'; // replace with your actual API password
        $from = 'Agri Dep.'; // replace with your desired sender ID

        $url = "https://connect.jazzcmt.com/sendsms_url.html";
        $queryParams = [
            'Username' => $username,
            'Password' => $password,
            'From' => $from,
            'To' => request()->phone_numbers,
            'Message' => request()->message,
        ];

        $response = Http::withOptions(['verify' => false])->get($url, $queryParams);
        if(Str::contains($response->body(), "Insufficient")){
            return ['error' => true, 'message' => "Insufficient Balance please recharge zong."];
        }else if(Str::contains($response->body(), "Invalid")){
            return ['error' => true, 'message' => "Invalid Credentials."];
        }else{
            return ['error' => false, 'message' => $response->body()];
        }
    }

    public function zongGatway()
    {
        $messages = [
            /*'LoginId' => '923165616844',
            'LoginPassword' => 'Zong@123',
            'Mask' => 'KPITB',*/
            'LoginId' => '923119562385',
            'LoginPassword' => 'Cfc@sms1234',
            'Mask' => 'KP CFC',
            'Message' => request()->message,
            'UniCode' => '0',
            'CampaignName' => uniqid(),
            'CampaignDate' => now()->format('m/d/Y h:i:s a'),
            'ShortCodePrefered' => 'n',
            'NumberCsv' => request()->phone_numbers,
        ];

        ini_set('soap.wsdl_cache_enabled', 0);
        $url = 'http://cbs.zong.com.pk/ReachCWSv2/CorporateSmsV2.svc?wsdl';
        $client = new \SoapClient($url, ['trace' => 1, 'exception' => 0]);
        $res = $client->BulkSmsv2(['objBulkSms' => $messages]);
        if(Str::contains($res->BulkSmsv2Result, "Insufficient")){
            return ['error' => true, 'message' => "Insufficient Balance please recharge zong."];
        }else if(Str::contains($res->BulkSmsv2Result, "Invalid")){
            return ['error' => true, 'message' => "Invalid Credentials."];
        }else{
            return ['error' => false, 'message' => $res->BulkSmsv2Result];
        }

    }

    public function ufonGatway()
    {
        $url = "https://bsms.ufone.com/bsms_v8_api/sendapi-0.3.jsp";
        $queryParams = [
            'id' => "03348970425",
            'message' => request()->message,
            'shortcode' => "BNP-CERD",
            'lang' => "English",
            'mobilenum' => request()->phone_numbers,//"923149465659,923149465659"
            'password' => "Kpitb@1234",
            'groupname' => "",
            'messagetype' => "Nontransactional",
        ];

        $response = Http::withOptions(['verify' => false])->get($url, $queryParams);

       $res =  $this->xmlResponseToJson($response->body());
       if(($res->original) && $res->original['response_id'] == 1){
           return ['error' => true, 'message' => $res->original['response_text']];
       }else{
           return ['error' => false, 'message' => $res->original['response_text']];
       }


    }

    public function xmlResponseToJson($xml)
    {
        $xmlResponse = $xml;

        // Convert the XML string to a SimpleXMLElement object
        try {
            $xmlObject = simplexml_load_string($xmlResponse, 'SimpleXMLElement', LIBXML_NOCDATA);

            // Check if the XML was successfully parsed
            if ($xmlObject === false) {
                return response()->json(['error' => 'Failed to parse XML'], 500);
            }

            // Convert the SimpleXMLElement object to JSON
            $jsonString = json_encode($xmlObject);

            // Decode the JSON string to an associative array
            $jsonArray = json_decode($jsonString, true);

            // Return the JSON response
            return response()->json($jsonArray);
        } catch (\Exception $e) {

            return response()->json(['error' => 'An error occurred during XML to JSON conversion.'], 500);
        }
    }

    public function telenorGatway()
    {
       $login = $this->telenorLogin();
       if(!$login['error']){
            //https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp?session_id=xxxx&to=923xxxxxxxxx,923xx
           $url = "https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp";
           $queryParams = [
               'session_id' => $login['authKey'],
               'to' => '923149465659',
               'text' => 'hellow this is test message from naqeeb telenor',
               'mask' => 'TelenorTest',
               //'test_mode' => '0',
           ];

           $response = Http::withOptions(['verify' => false])->get($url, $queryParams);
           $res =  $this->xmlResponseToJson($response->body());

           if(($res->original['response']) && $res->original['response'] == "Error"){
               return ['error' => true, 'message' => $res->original['data']];
           }else{
               return ['error' => false,'message'=>$res->original['data']];
           }

       }else{
           echo "invalid telenor credentials";
       }

    }


    public function telenorLogin()
    {
        $url = "https://telenorcsms.com.pk:27677/corporate_sms2/api/auth.jsp";
        $queryParams = [
            'msisdn' => "923454023557",
            'password' => 'Telenorpakistan@3553!!',
        ];

        $response = Http::withOptions(['verify' => false])->get($url, $queryParams);

        $res =  $this->xmlResponseToJson($response->body());

        if(($res->original['response']) && $res->original['response'] != "OK"){
            return ['error' => true, 'message' => $res->original['response']];
        }else{
            return ['error' => false,'message'=>"Successfully Login", 'authKey' => $res->original['data']];
        }
    }


}
