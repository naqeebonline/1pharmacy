<?php

namespace App\Http\Controllers\API;

use App\Models\Districts;
use App\Models\Hospital;
use App\Models\PoliceStation;
use App\Models\PollingStation;
use App\Models\Tehsils;
use Hash;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Guardian\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Guardian\Entities\UserJWT;
use Illuminate\Database\QueryException;
use Modules\Guardian\Entities\Department;
use Modules\Settings\Entities\MyApp;

class LoginController extends Controller
{
    public function __construct(){
        //$this->middleware('assign.guard:eGovUser', ['except' => ['eGovUserlogin']]);
    }

    public function eGovUserlogin(Request $request) {
        if(request()->has("cnic")){
            $requestValidator = Validator::make(request()->all(), [
                'cnic' => 'required',
                'password' => 'required'
            ]);
        }else{
            $requestValidator = Validator::make(request()->all(), [
                'username' => 'required',
                'password' => 'required'
            ]);
        }


        if ($requestValidator->fails()) {
            //return ['error' => true, 'message' => implode(' ', $requestValidator->errors()->all())];
            return response()->json(['error' => true, 'messages' => implode(' ', $requestValidator->errors()->all())],500);
        }//..... end if() .....//

        if(request()->has("cnic") && Auth::attempt(['cnic' => $request->cnic, 'password' => $request->password])) {
            $user = Auth::user();
            $token = [
                'type' => "Bearer",
                'token' =>  $user->createToken('election_survilance_app')->plainTextToken
            ];

           // Auth::user()->roles->pluck('name')[0]

            return response()->json([
                'error'     =>  false,
                'message'   =>  'User Login Successfully',
                'data'      =>  [
                    'user'  =>  $user,
                    'district'=> Districts::select(["id","title"])->whereId($user->district_id)->first() ?? "",
                    'tehsil'=> Tehsils::select(["id","title"])->whereId($user->tehsil_id)->first() ?? "",
                    'police_station'=>   PoliceStation::select(["id","title"])->whereId($user->police_station_id)->first() ?? "",
                    'hospital'=> Hospital::select(["id","name as title"])->whereId($user->hospital_id)->first() ?? "",
                    'polling_station'=> PollingStation::select(["id","polling_station_name as title"])->whereId($user->polling_station_id)->first() ?? "",

                    'token' =>  $token
                ]
            ],200);

        } else if(Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            $token = [
                'type' => "Bearer",
                'token' =>  $user->createToken('election_survilance_app')->plainTextToken
            ];

            return response()->json([
                'error'     =>  false,
                'message'   =>  'User Login Successfully',
                'data'      =>  [
                    'user'  =>  $user,
                    'district'=> Districts::select(["id","title"])->whereId($user->district_id)->first() ?? "",
                    'tehsil'=>   Tehsils::select(["id","title"])->whereId($user->tehsil_id)->first() ?? "",
                    'police_station'=>   PoliceStation::select(["id","title"])->whereId($user->police_station_id)->first() ?? "",
                    'token' =>  $token
                ]
            ],200);

        } else {

            return response()->json([
                'error'     =>  true,
                'message'   =>  'Invalid Credentials',
                'data'      =>  null
            ],500);
        }

        return $response;

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token) {
        return response()->json([
            '_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * env('JWT_TTL', 60)
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard() {
        return Auth::guard('eGovUser');
    }

}
