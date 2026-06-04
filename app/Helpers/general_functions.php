<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Fmc\Entities\Notification;

function createFormElements($object, $selected_form_input_old_val = null)
{

    $html   =   '';
    $input_type =   $object->input_type;
    $input_label = $object->title;
    $input_value = $object->value;
    $input_id   = $object->id;
    $input_name = !is_null($object->input_name) ? $object->input_name : $input_type . $input_id;

    switch ($input_type) {

        case 'text':
            $html .= '<div class="row">
                            <div class="col-md-12">
                             <div class="form-group">
                                <label class="control-label col-sm-4 bold" for="' . $input_name . '">' . $input_label . ':</label>';
            if (session()->has('errors')) {
                $html .= '<span class="help">' . session()->get('errors')->first($input_name) . '</span>';
            }
            $html .= '<div class="col-sm-8">
                                        <input type="text" class="form-control" id="' . $input_name . '" value="' . old($input_name) . '" placeholder="" name="' . $input_name . '">
                                    </div>
                                 </div>
                            </div>
                            </div>';
            break;

        case 'radio':

            $checked = old('condition_selection') == $input_value ? 'checked="checked"' : "";
            //echo $checked;
            $html .= '<div class="col-md-12">
                            <div class="form-group">
                              <div class="col-sm-10">';

            if (session()->has('errors')) {
                $html .= '<span class="help">' . session()->get('errors')->first($input_name) . '</span>';
            }



            $html .= '<label>
                                    <input type="radio" name="condition_selection" value="' . $input_value . '"
                                    ' . $checked . ' data-input-pid="' . $input_id . '" required > ' . $input_label . '
                                  </label>
                              </div>
                            </div>
                          </div>';
            break;

        case 'checkbox':
            $html .= '<div class="col-md-12">
                            <div class="form-group">
                              <div class="col-sm-10">
                                  <label>
                                    <input type="checkbox" name="condition_selection[' . $input_id . ']" value="' . $input_value . '"
                                            data-input-pid="' . $input_id . '" > ' . $input_label . '
                                  </label>
                              </div>
                            </div>
                          </div>';
            break;

        case 'dropdown':
            break;

        case 'textarea':
            break;
    }

    return $html;
}

/**
 * print array
 * @param $array
 * @param bool $die
 */
function pr($array, $die = false)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";

    if ($die == true) die('head');
}


/**
 * create unique file name for file upload etc
 * @param string $param
 * @return string
 */
function unique_name($param = "")
{

    $param = !empty($param) ? "-" . $param . "-" : "";
    //U if for current timestamp
    $uni = uniqid(date('Y-m-d-h-i-s-U') . $param, true);
    return str_replace(".", "_", $uni);
}

function convertNumberIntoWords(&$value, $allowPostFix = false)
{

    $len = strlen($value);
    if ($len > 4 && $len <= 5) {
        $counter = round($value / 1000, 2);
        $value = ($allowPostFix) ? $counter . 'K' : $counter;
    } else if ($len >= 6) { // lacs
        $counter = round($value / 1000000, 2);
        $value = ($allowPostFix) ? $counter . 'M' : $counter;
    }
    return $value;
}

/**
 * encrypt primary_key
 * @param $primary_key
 * @return string
 */
function enc_key($id)
{
    return encrypt($id);
}

function dec_key($id)
{
    $id  =   decrypt($id);
    return $id;
}

function convertToCNIC($cnic)
{
    $cnic = substr_replace($cnic, '-', 5, 0);
    $cnic = substr_replace($cnic, '-', -1, 0);
    return $cnic;
}

function convertToMobileNumber($contact)
{
    $contact = str_replace(" ", "", $contact);
    $contact = str_replace("-", "", $contact);
    $contact = str_replace("+92", "0", $contact);
    $contact = strlen($contact) > 0 ? substr_replace($contact, ' ', 4, 0) : $contact;
    return $contact;
}

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function endsWith($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}


function getDifferencePercentage($num1, $num2)
{

    if ($num1 > 0 || $num2 > 0) {
        $dif = (int) $num1 - (int) $num2;
        $avg = ($num2 + $num1) / 2;
        $div = abs($dif) / $avg;
        $result = $div * 100;
        return number_format($result);
    } else {
        return 0;
    }
}

/** FTS: Encrypted/Decrypted String */
function encodeRemarks($remarks = "", $secretKey = "PMRU2021")
{

    // Store the cipher method
    $method = "AES-128-CTR";

    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';

    $encrypted = openssl_encrypt($remarks, $method, $secretKey, OPENSSL_RAW_DATA, $encryption_iv);
    return $encrypted;
}

function decodeRemarks($remarks = "", $secretKey = "PMRU2021")
{

    $method = "AES-128-CTR";

    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';

    $decrypted = openssl_decrypt($remarks, $method, $secretKey, OPENSSL_RAW_DATA, $encryption_iv);
    return $decrypted;
}

function encodeKey($remarks = "", $secretKey = "PMRU2021")
{

    // Store the cipher method
    $method = "AES-128-CTR";

    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';

    $encrypted = openssl_encrypt($remarks, $method, $secretKey, OPENSSL_RAW_DATA, $encryption_iv);
    return $encrypted;
}

function decodeKey($remarks = "", $secretKey = "PMRU2021")
{

    $method = "AES-128-CTR";

    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';

    $decrypted = openssl_decrypt($remarks, $method, $secretKey, OPENSSL_RAW_DATA, $encryption_iv);
    return $decrypted;
}

function encrypt_decrypt_remarks($string, $action = 'encrypt', $dk = 'PMRU2022')
{

    $encrypt_method = "AES-256-CBC";
    $pk = env('REMARKS_ENC_KEY');

    $key = hash('sha256', $pk);
    $iv = substr(hash('sha256', $dk), 0, 16);

    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function randomString($size = 10)
{
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($permitted_chars), 0, $size);
}

function makeIntVerificationCode()
{
    return '12345';
    // return rand(11111,99999);
}


function checkNullAndEmpty($value)
{
    return ((is_null($value) || empty($value)) ? true : false);
}

function numberToRoman($number)
{
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if ($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}

function str_lreplace($search, $replace, $str)
{
    if (($pos = strrpos($str, $search)) !== false) {
        $search_length  = strlen($search);
        $str    = substr_replace($str, $replace, $pos, $search_length);
    }
    return $str;
}

function saveCronLog($content)
{
    $storage_path = storage_path('logs/pushnotification.log');
    $current_content = file_get_contents($storage_path);
    $append = $current_content . "\r\n==================================================== \r\n" . print_r($content, true);
    $append .= "\r\n==================================================== \r\n";
    file_put_contents($storage_path, $append);
}

function trim_str_dotdot($str, $len = 60)
{
    return strlen($str) > $len ? substr($str, 0, $len) : $str;
}

// this will replace all words by
function replaceWordsByStar($remarks)
{
    return preg_replace('/(?!^)\S/', '*',  $remarks);
}

function createCountBlock($title = 'Total', $count = 0, $percentage = '95', $color = 'blue')
{

    return '<a href="#">
            <div class="col-md-3 col-sm-6 spacing-bottom-sm spacing-bottom">
                <div class="tiles ' . $color . ' added-margin">
                    <div class="tiles-body">
                        <div class="tiles-title"></div>
                        <div class="heading">
                            ' . $title . ' <span class="">' . $count . '</span>
                        </div>
                        <div class="progress transparent progress-small no-radius">
                            <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="' . $percentage . '%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>';
}




function getPert($total, $current)
{
    if ($total == 0) {
        return 0;
    }
    return ($current / $total) * 100;
}

function colorGradient($rand)
{
    $colorGradient = ['bg-gradient-success', 'bg-gradient-secondary', 'bg-gradient-warning', 'bg-gradient-danger'];
    if (count($colorGradient) < $rand) {
        $rand = 0;
    }
    return $colorGradient[$rand];
}



function sendResponse($result, $message = null)
{
    $response = [
        'success' => true,
        'data'    => $result,
    ];

    if (!empty($message)) {
        $response['message'] = $message;
    }
    return response()->json($response, 200);
}


function sendError($message, $errors = [], $code = 401)
{
    $response = ['response' => false, 'message' => $message];
    if (!empty($errors)) {
        $response['data'] = $errors;
    }
    throw new HttpResponseException(response()->json($response, $code));
}



function customButton($model, $permission, $route = null, $isShowView = false)
{
    $editPermission = Auth::User()->can($permission . '-edit');
    $showPermission = Auth::User()->can($permission . '-list');
    $deletePermission =  Auth::User()->can($permission . '-delete');
    $editorDeletePermission = Auth::User()->canany([$permission . '-edit', $permission . '-delete']);
    $showPermissionView = '';
    if ($isShowView == true) {
        $showPermissionView =  $showPermission == 1 ? '<a class="dropdown-item" href="' .  route($route . '.show', $model->id) . '">show</a>' : '';
    }


    return $editorDeletePermission == 1 ? '<div class="dropdown custom-dropdown">
    <a class="dropdown-toggle font-20 text-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
<i class="las la-cog"></i>
</a>
<div class="dropdown-menu" aria-labelledby="dropdownMenuLink1" style="will-change: transform;">
 ' . $showPermissionView .  ($editPermission == 1 ? '<a class="dropdown-item" href="' .  route($route . '.edit', $model->id) . '">Edit</a>' : null)
        . ($deletePermission == 1 ?  '<a class="dropdown-item destory" href="javascript:void(0);"  onclick="distory(\'' .  route($route . ".destroy", $model->id) . '\')">Delete</a>' : null) .
        '</div>
</div>' : null;
}




function getLastWordAfterHyphen($inputString)
{
    $parts = explode('-', $inputString);
    if (count($parts) > 1) {
        return end($parts);
    }
    return $inputString; // Return the input string if no hyphen is found
}

function getAllMediaFromModel($model)
{
    $allMediaUrls = [];

    $registeredCollections = $model->getRegisteredMediaCollections();
    foreach ($registeredCollections as $collectionName => $collection) {
        $mediaItems = $model->getMedia($collection->name);

        $mediaUrls = $mediaItems->map(function ($media) {
            return [
                'url' => $media->getUrl(),
                'name' => $media->collection_name,
            ];
        });

        $allMediaUrls = array_merge($allMediaUrls, $mediaUrls->toArray());
    }

    return $allMediaUrls;
}


//  Fnc Find Changes Between to Objects Just For Activity Logs
function findObjectChanges($object)
{


    $oldData = [];
    $changes = [];
    $newData = [];
    foreach ($oldData as $key => $value) {
        if (array_key_exists($key, $newData)) {
            if ($value !== $newData[$key]) {
                $changes[$key] = [
                    'old' => $value,
                    'new' => $newData[$key]
                ];
            }
        } else {
            $changes[$key] = [
                'old' => $value,
                'new' => null
            ];
        }
    }

    // Check for new keys in $newData
    foreach ($newData as $key => $value) {
        if (!array_key_exists($key, $oldData)) {
            $changes[$key] = [
                'old' => null,
                'new' => $value
            ];
        }
    }

    return $changes;
}


function getObjectPropertyDifference($oldObject, $attributesObject)
{
    $oldArray = json_decode(json_encode($oldObject), true);
    $attributesArray = json_decode(json_encode($attributesObject), true);

    $difference = [];

    foreach ($oldArray as $key => $value) {
        if (array_key_exists($key, $attributesArray) && $attributesArray[$key] !== $value) {
            $difference[$key] = [
                'old' => $value,
                'attributes' => $attributesArray[$key]
            ];
        }
    }

    return $difference;
}



function getFMCUserNotifictions()
{
    $notifications = Notification::with('seller', 'complaint')->where('user_id', auth()->user()->id ?? 0)
        ->where('status', 1)
        ->limit(8)
        ->get();

    $count = Notification::with('seller', 'complaint')->where('user_id', auth()->user()->id ?? 0)
        ->where('status', 1)
        ->count();

    return [
        'notifications' => $notifications ?? [],
        'count' => $count ?? 0,
    ];
}
function numberToWords($num)
{
    $ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen',
        'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
    ];

    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    $digits = ['', 'Thousand', 'Lakh', 'Crore'];

    if ($num == 0) return 'Zero only';

    $result = '';

    // Split number into groups
    $number = str_pad($num, 9, '0', STR_PAD_LEFT);
    $crore = (int)substr($number, 0, 2);
    $lakh = (int)substr($number, 2, 2);
    $thousand = (int)substr($number, 4, 2);
    $hundred = (int)substr($number, 6, 1);
    $remainder = (int)substr($number, 7, 2);

    if ($crore > 0) {
        $result .= convert2Digit($crore, $ones, $tens) . ' Crore ';
    }
    if ($lakh > 0) {
        $result .= convert2Digit($lakh, $ones, $tens) . ' Lakh ';
    }
    if ($thousand > 0) {
        $result .= convert2Digit($thousand, $ones, $tens) . ' Thousand ';
    }
    if ($hundred > 0) {
        $result .= $ones[$hundred] . ' Hundred ';
    }
    if ($remainder > 0) {
        if ($result != '') {
            $result .= 'and ';
        }
        $result .= convert2Digit($remainder, $ones, $tens) . ' ';
    }

    return trim($result) . ' only/-';
}

function convert2Digit($num, $ones, $tens)
{
    if ($num < 20) {
        return $ones[$num];
    } else {
        return $tens[intval($num / 10)] . (($num % 10 != 0) ? ' ' . $ones[$num % 10] : '');
    }
}

function getUserRole(){
    return auth()->user()->roles->pluck('name')[0];
}

function financeHeadId($code){
    return \App\Models\Finance\FinanceHead::where('description', $code)->value('id');
}

function financeHeadCode($id){
    return \App\Models\Finance\FinanceHead::whereId($id)->value('description');
}

function financeHeadName($id){
    return \App\Models\Finance\FinanceHead::whereId($id)->value('name');
}

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

function generateVoucherNumber(string $type, int $userId): string
{
    // Normalize and format components
    $typeSlug = strtoupper(Str::slug($type)); // e.g. SALE or PURCHASE
    $userCode = str_pad($userId, 3, '0', STR_PAD_LEFT); // e.g. 001

    // Count existing vouchers of this type
    $lastVoucher = DB::table('finance_vouchers')
        ->where('voucher_type', $type)
        ->orderByDesc('id')
        ->value('voucher_number'); // e.g. SALE-001-004

    // Extract last number
    $lastNumber = 0;
    if ($lastVoucher && preg_match('/-(\d+)$/', $lastVoucher, $matches)) {
        $lastNumber = (int) $matches[1];
    }

    // Increment
    $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // e.g. 005

    // Combine into final voucher number
    return "{$typeSlug}-{$userCode}-{$nextNumber}";
}

function user_advance($user_id,$start_date,$end_date){
    // $total_advance = DB::table("in_patient_payments")
    //     ->when($start_date, function ($query) use ($start_date) {
    //     $query->whereDate('created_at',">=", $start_date);
    // })
    //     ->when($end_date, function ($query) use ($end_date) {
    //         $query->whereDate('created_at',"<=", $end_date);
    //     })
    //     ->where('created_by', $user_id)
    //     ->where('is_active', 1)
    //     ->sum("amount");
    return 0;
}

function userRole($user_id=''){
    if($user_id){
     $user = \App\Models\User::whereId($user_id)->first();
     return $user->roles->pluck('name')[0];
    }else{
        return auth()->user()->roles->pluck('name')[0];
    }


}

function make_entry($voucher_id=0,$head_id=0,$debit=0,$credit=0,$reference_type=0,$reference_id=0,$user_id=0,$remarks=0){
    $data = [

        'voucher_id' => $voucher_id,
        'head_id' => $head_id,
        'debit' => ($debit),
        'credit' => ($credit),
        'reference_type' => $reference_type,
        'reference_id' => $reference_id,
        'user_id' => $user_id,
        'created_by' => auth()->user()->id,
        'remarks' => $remarks,
        'created_at' => now(),
        'transaction_date' => today(),
    ];
    \App\Models\Finance\FinanceTransaction::insert($data);
    return true;
}


