<?php

namespace App\Traits;

trait CommonMethods{

    /**
     * return unique name for files
     * @return string
     */
    public function uniqueName(){

        $uniqueness =   uniqid(microtime(),true);
        $replacement=  str_replace(' ','_',$uniqueness);
        return uniqid($replacement,true);
    }

    public function bearerToken($token)
    {
        //$header     = $request->header('Authorization', '');
        $token    =   str_replace('Bearer','',$token);
        $token    =   str_replace(' ','',$token);

        return $token;
//        if (\Str::startsWith($header, 'Bearer ')) {
//            return \Str::substr($header, 7);
//        }
    }

    /**
     * verify is it json
     * @param $string
     * @return bool
     */
    function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * 200 //success
     * 401 //un-authorized
     * @param int $success
     * @param null $message
     * @param array $data
     * @param int $exception_error_code
     * @return \Illuminate\Http\JsonResponse
     */

    public function apiJsonResponse($success = 1,  $message=null, $data= [], $exception_error_code=200){

        $response['success']         =   $success;
        $response['message']        =    $message;
        $response['data']           =    $data;

        return response()->json($response,$exception_error_code,[],JSON_NUMERIC_CHECK);
    }


}//end

