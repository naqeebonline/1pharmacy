<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $service = "";
    private $phoneNumber = "";
    private $message = "";
    private $operator = "";
    private $sms_id = "";
    public $tries = 3; // Retry 3 times if failed
    public $retryAfter = 2; // Delay between retries in  2 seconds
    public function __construct($sms_id,$phoneNumber, $message,$operator)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->operator = $operator;
        $this->sms_id = $sms_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if(strtolower($this->operator) == "zong"){
                $result = $this->zongGatway();
                Log::info('zong', ['zong_result' => $result]);
            }else if(strtolower($this->operator) == "jazz" || strtolower($this->operator) == "warid"){
                $result = $this->jazzGatway();
                Log::info('jazz', ['jazz_result' => $result]);
            }
            else if(strtolower($this->operator) == "telenor"){
                $result = $this->telenorGatway();
                Log::info('jazz', ['jazz_result' => $result]);
            }else{
                $result = $this->ufonGatway();
                Log::info('ufone', ['ufone' => $result]);
            }

            if(!$result['error']){
                //Log::info('ufone', ['result' => json_encode($result)]);
                DB::table("sms_info")->whereId($this->sms_id)->update([
                    "sms_api_response"      =>json_encode($result),
                    "is_sent"      =>1,
                ]);
                // return $result;
            }else{
                //Log::info('ufone', ['result' => json_encode($result)]);
                DB::table("sms_info")->whereId($this->sms_id)->update([
                    "sms_api_response"      =>json_encode($result),
                    "is_sent"      =>2,
                ]);
                // return $result;
            }
        } catch (\Exception $e) {
            // Retry the job if it fails
            $this->release(2); // Release job after 10 seconds
            $this->retry(); // Retry the job
        }




    }

    public function jazzGatway()
    {
        $username = '03024064263'; // replace with your actual username
        $password = '03024064263@jazz'; // replace with your actual API password
        $from = 'Agri Dep..'; // replace with your desired sender ID

        $url = "https://connect.jazzcmt.com/sendsms_url.html";
        $queryParams = [
            'Username' => $username,
            'Password' => $password,
            'From' => $from,
            'To' => $this->phoneNumber,
            'Message' => $this->message,
        ];

        $response = Http::withOptions(['verify' => false])->get($url, $queryParams);
        if(Str::contains($response->body(), "Insufficient")){
            return ['error' => true, 'message' => "Insufficient Balance please recharge zong."];
        }else if(Str::contains($response->body(), "Invalid")){
            return ['error' => true, 'message' => "Invalid Credentials."];
        }else if(Str::contains($response->body(), "Mask not allowed!")){
            return ['error' => true, 'message' => "Mask not allowed!"];
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
            'Message' => $this->message,
            'UniCode' => '0',
            'CampaignName' => uniqid(),
            'CampaignDate' => now()->format('m/d/Y h:i:s a'),
            'ShortCodePrefered' => 'n',
            'NumberCsv' => $this->phoneNumber,
        ];



        ini_set('soap.wsdl_cache_enabled', 0);
        $url = 'http://cbs.zong.com.pk/ReachCWSv2/CorporateSmsV2.svc?wsdl';
        $client = new \SoapClient($url, ['trace' => 1, 'exception' => 0]);
        $res = $client->BulkSmsv2(['objBulkSms' => $messages]);
        Log::info('final_result', ['result' => $res]);
        if(Str::contains($res->BulkSmsv2Result, "Insufficient")){
            return ['error' => true, 'message' => "Insufficient Balance please recharge zong."];
        }else if(Str::contains($res->BulkSmsv2Result, "Invalid")){
            return ['error' => true, 'message' => "Invalid Credentials."];
        }else if(Str::contains($res->BulkSmsv2Result, "Number list is Empty")){
            return ['error' => true, 'message' => "Number list is Empty."];
        }else{
            return ['error' => false, 'message' => $res->BulkSmsv2Result];
        }

    }

    public function ufonGatway()
    {
        $url = "https://bsms.ufone.com/bsms_v8_api/sendapi-0.3.jsp";
        $queryParams = [
            'id' => "03348970425",
            'message' => $this->message,
            'shortcode' => "BNP-CERD",
            'lang' => "English",
            'mobilenum' => $this->phoneNumber,//"923149465659,923149465659"
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


    public function telenorGatway()
    {
        $login = $this->telenorLogin();
        if(!$login['error']){
            //https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp?session_id=xxxx&to=923xxxxxxxxx,923xx
            $url = "https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp";
            $queryParams = [
                'session_id' => $login['authKey'],
                'to' => $this->phoneNumber,
                'text' => $this->message,
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
}
