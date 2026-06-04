<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EmergencyAlert;
use App\Models\Hospital;
use App\Models\Notification;
use App\Models\PoliceMobile;
use App\Models\PoliceStation;
use App\Models\User;
use App\Traits\CommonMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\EIdentity\Entities\Designations;

/**
 *
 *This controller will be using for common APIs which can be use
 * within or outside system, all things will be kept here
 *
 */
class CommonActionController extends Controller
{
    use CommonMethods;
    public function getDesignationsList(Request $request){

        $designations = Designations::pluck('title','id');

        return $this->apiJsonResponse(1,'Egov Designations list',$designations);

    }

    public function saveMobileUserToken()
    {
        $user = Auth::user();
        if(request()->type == "ios"){
            User::whereId($user->id)->update(["ios_token"=>request()->token]);
        }else{
            User::whereId($user->id)->update(["android_token"=>request()->token]);
        }
        return response()->json(['error' => false, 'message' => "Token Saved successfully","data"=>User::whereId($user->id)->first()],200);
    }

    public function findNearest()
    {
        /*
         * using eloquent approach, make sure to replace the "Restaurant" with your actual model name
         * replace 6371000 with 6371 for kilometer and 3956 for miles
         */
        $user = auth()->user();
        $role = auth()->user()->roles->pluck('name')[0];
        if($role == "Super Admin"){
            $district_id = "";
        }else{
            $district_id = $user->district_id;
        }

        $latitude = request()->lat;
        $longitude = request()->lng;
        $radius = 40;
        if(request()->radius){
         $radius = request()->radius;
        }
        $police_station = PoliceStation::selectRaw("id,title,ps_contact_number,latitude,longitude,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( latitude ) )
                           * cos( radians( longitude ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( latitude ) ) )
                         ) AS distance", [$latitude, $longitude, $latitude])
            ->whereNotNull("latitude")
            ->whereNotNull("longitude")
            ->when($district_id,function ($q) use ($district_id){
                return $q->where("district_id",$district_id);
            })
            ->having("distance", "<", $radius)->orderBy("distance",'asc')->offset(0)->limit(20)->get();

        /*$police_mobile = PoliceMobile::selectRaw("id,contact_number,rank,lat,lng,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lng ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance", [$latitude, $longitude, $latitude])
            ->whereNotNull("lat")
            ->whereNotNull("lng")
            ->having("distance", "<", $radius)->orderBy("distance",'asc')->offset(0)->limit(20)->get();*/

        $hospitals = Hospital::selectRaw("id,name,contact_number,lat,lng,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lng ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance", [$latitude, $longitude, $latitude])
            ->whereNotNull("lat")
            ->whereNotNull("lng")
            ->when($district_id,function ($q) use ($district_id){
                return $q->where("district_id",$district_id);
            })
            ->having("distance", "<", $radius)->orderBy("distance",'asc')->offset(0)->limit(60)->get();

        return response()->json(['error' => false, 'message' => "data found",
            "police_station"=>$police_station,
            //"police_mobile"=>$police_mobile,
            "hospitals"=>$hospitals,

        ],200);
    }


    public function findNearestMobile()
    {
        /*
         * using eloquent approach, make sure to replace the "Restaurant" with your actual model name
         * replace 6371000 with 6371 for kilometer and 3956 for miles
         */
        $user = auth()->user();
        $role = auth()->user()->roles->pluck('name')[0];
        if($role == "Super Admin"){
            $district_id = "";
        }else{
            $district_id = $user->district_id;
        }
        $latitude = request()->lat;
        $longitude = request()->lng;
        $radius = 40;
        if(request()->radius){
         $radius = request()->radius;
        }


        $police_mobile = PoliceMobile::selectRaw("id,registration_number,contact_number,lat,lng,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lng ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance", [$latitude, $longitude, $latitude])
        ->whereNotNull("lat")
        ->whereNotNull("lng")
        ->when($district_id,function ($q) use ($district_id){
                return $q->where("district_id",$district_id);
        })
        ->having("distance", "<", $radius)->orderBy("distance",'asc')->offset(0)->limit(10)->get();

        return response()->json(['error' => false, 'message' => "data found",

            "police_mobile"=>$police_mobile,


        ],200);
    }

    public function sendMobileNotification()
    {
        $user = auth()->user();
        $requestValidator = Validator::make(request()->all(), [
            'title' => 'required',
        ]);

        if ($requestValidator->fails()) {
            return response()->json(['error' => true, 'message' => implode(' ', $requestValidator->errors()->all())],500);

        }//..... end if() .....//

        $data['title'] = request()->title;
        $data['notification_type'] = "notification";

        if(request()->has("attachment") && request()->attachment){
            /*$image_resize = Image::make(request()->attachment->getRealPath());
            $image_resize->resize(300, 300);
            $filename = request()->attachment->hashName();
            $image_resize->save(public_path('media/notifications/' .$filename));*/
            $path = request()->file('attachment')->store('attachment', 'public');
            $data['attachment'] = $path;
        }
        if(request()->has("audio") && request()->audio){
            $path = request()->file('audio')->store('audio', 'public');
            $data['audio']=$path;

        }

        if(request()->has("video") && request()->video){
            $path = request()->file('video')->store('video', 'public');
            $data['video']=$path;

        }
        $data['created_by'] = $user->id;
        Notification::create($data);
        $url = 'https://fcm.googleapis.com/fcm/send';

        $FcmToken = User::whereNotNull('android_token')->pluck('android_token')->all();


        $serverKey = 'AAAAU8XTJY0:APA91bH7TE5CJSLwW00Kjv0qg93d5yA8Ite6Pz1nHI2CVJi8VtYzsrbmYYsXR_hEnxoEJp_bv-uXNFeKA-JZi82yj_vY4AgWC78vqMqJb25FRp4UF_RVHMm9frRnUp-paO3iFfDafoKO'; // ADD SERVER KEY HERE PROVIDED BY FCM

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => "Notification From Web Admin",
                "body" => "Hi this is test notification from admin side",
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response
        return response()->json(['error' => false, 'message' => "Notification sent successfully"],200);
    }

    public function emergencyAlert()
    {
        $user = auth()->user();
        $requestValidator = Validator::make(request()->all(), [
            'message' => 'required',
        ]);

        if ($requestValidator->fails()) {
            return response()->json(['error' => true, 'message' => implode(' ', $requestValidator->errors()->all())],500);

        }//..... end if() .....//

        $data['message'] = request()->message;
        $data['type'] = request()->type;
        $data['district_id'] = $user->district_id;
        $data['user_id'] = $user->id;

        if(request()->has("attachment") && request()->attachment){
            /*$image_resize = Image::make(request()->attachment->getRealPath());
            $image_resize->resize(300, 300);
            $filename = request()->attachment->hashName();
            $image_resize->save(public_path('media/notifications/' .$filename));*/
            $path = request()->file('attachment')->store('attachment', 'public');
            $data['attachment'] = $path;
        }
        if(request()->has("audio") && request()->audio){
            $path = request()->file('audio')->store('audio', 'public');
            $data['audio']=$path;

        }

        if(request()->has("video") && request()->video){
            $path = request()->file('video')->store('video', 'public');
            $data['video']=$path;

        }
        $data['created_by'] = $user->id;

        EmergencyAlert::create($data);



        return response()->json(['error' => false, 'message' => "Emergency Alert sent successfully"],200);
    }

    public function getNotifications()
    {
        $data = Notification::orderBy("created_at","desc")->get();
         foreach ($data as $key => $value){
             if($value->attachment){
                 $value->attachment = URL::to('storage/')."/".$value->attachment;
             }
             if($value->audio){
                 $value->audio = URL::to('storage/')."/".$value->audio;
             }
             if($value->video){
                 $value->video = URL::to('storage/')."/".$value->video;
             }
         }
        return response()->json(['error' => false, 'message' => "Data found","data"=>$data],200);

    }

    public function getEmergencyAlerts()
    {
        $data = EmergencyAlert::get();
        foreach ($data as $key => $value){
            if($value->attachment){
                $value->attachment = URL::to('storage/')."/".$value->attachment;
            }
            if($value->audio){
                $value->audio = URL::to('storage/')."/".$value->audio;
            }
            if($value->video){
                $value->video = URL::to('storage/')."/".$value->video;
            }

        }
        return response()->json(['error' => false, 'message' => "Data found","data"=>$data],200);

    }


}
