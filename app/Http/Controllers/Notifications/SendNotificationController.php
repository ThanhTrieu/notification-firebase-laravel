<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SendNotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function saveToken(Request $request)
    {
        $token = $request->token;
        if(!empty($token)){
            $find = DB::table('token')->where(['value'=> $token])->first();
            if(empty($find)){
                $insert = DB::table('token')->insert([
                    'value' => $token
                ]);
                if($insert){
                    $request->session()->put('token_device', $token);
                    return response()->json([
                        'cod' => 200,
                        'message' => 'save token success'
                    ]);
                }
                return response()->json([
                    'cod' => 500,
                    'message' => 'save token failure'
                ]);
            } else {
                $request->session()->put('token_device', $token);
                return response()->json([
                    'cod' => 200,
                    'message' => 'token already exist'
                ]);
            }
        }
        return response()->json(['cod' => 404, 'message' => 'not found token']);
    }

    public function sendNotification(Request $request)
    {
        $title   = $request->title;
        $content = $request->content;
        $data = $this->sendFcm($request, $title, $content);
        return $data;
    }

    public function testClickAction()
    {
        return "success";
    }

    private function sendFcm($request, $title, $content)
    {
        /** Google URL with which notifications will be pushed */
        $url = "https://fcm.googleapis.com/fcm/send";
        $urlAction = route('notifications.test');
        $tokenDevice = $request->session()->get('token_device');
        /** 
         * Firebase Console -> Select Projects From Top Naviagation 
         *      -> Left Side bar -> Project Overview -> Project Settings
         *      -> General -> Scroll Down and you will be able to see KEYS
         */
        $subscription_key =  env("SUBSCRIPTION_KEY", "key=AAAAnRE9JQc:APA91bGWCXUifGiMorOwP5-NYjACts6Ig8ZveGuEZE6SaeTptaOl4cBFyfqkDQO1_qSzv7xY8fPK-xDHmUH7rnodiZDXgtDnOZt8ALgw04EEcoY2bRBYBPq-2OiZBau8NXXi_v3BUyP6");
        /** We will need to set the following header to make request work */
        $request_headers = array(
            "Authorization:" . $subscription_key,
            "Content-Type: application/json"
        );

        /** Data that will be shown when push notifications get triggered */
        $postRequest = [
            "notification" => [
                "title" =>  $title,
                "body" =>  $content,
                "icon" =>  "https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg",
                "click_action" =>  $urlAction
            ],
            /** Customer Token, As of now I got from console. You might need to pull from database */
            "to" =>  $tokenDevice
        ];

        /** CURL POST code */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $season_data = curl_exec($ch);

        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        // Show me the result
        curl_close($ch);
       return $season_data;
    }
}
