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

    private function getGoogleAccessToken()
    {

        $credentialsFilePath = public_path('json/send-notification-63cf4-firebase-adminsdk-gd2oq-aefd6b3715.json');
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return isset($token['access_token']) ? $token['access_token'] : null ;
   }

    private function sendFcm($request, $title, $content)
    {
        /** Google URL with which notifications will be pushed */
        $url = "https://fcm.googleapis.com/v1/projects/send-notification-63cf4/messages:send"; 

        $allDevices = DB::table('token')->get();
        $allDevices = json_decode(json_encode($allDevices), true);
        $allDevices = array_column($allDevices, 'value');
   
        $tokenDevice = $request->session()->get('token_device');
        $request_headers = array(
            "Authorization: Bearer " . $this->getGoogleAccessToken(),
            "Content-Type: application/json"
        );

        /** Data that will be shown when push notifications get triggered */
        $postRequest = [
            "message" => [
                "token" => $tokenDevice,
                "notification" => [
                    "title" =>  $title,
                    "body" =>  $content,
                ]
            ]
        ];

        /** CURL POST code */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));

        $season_data = curl_exec($ch);

        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        // Show me the result
        curl_close($ch);
       return $season_data;
    }
}
