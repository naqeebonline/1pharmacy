<?php

namespace App\Actions;

use Kreait\Firebase\Factory;
use Modules\Settings\Entities\FirebaseNotification;
use Kreait\Firebase\Messaging\CloudMessage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FirebaseNotificationAction
{

    public static function initialize($projectConfigPath)
    {
        return (new Factory)
            ->withServiceAccount($projectConfigPath);
    }

    public function sendSinglePushNotification($envFileName = null, $data = [], $token = null)
    {
        $firebase = $this->initialize(base_path() . env($envFileName));
        // Get a reference to the Cloud Messaging service
        $messaging = $firebase->createMessaging();
        // Construct the FCM message
        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => $data['title'] ?? '',
                'body' => $data['body'] ?? '',
            ],
            'data' => [
                ...$data['payload'] ?? [],
            ],
            'token' => $token ?? '',
        ]);

        // Send the FCM message
        try {
            $messaging->send($message);

            // Notification sent successfully
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Exception $e) {
            // Handle the exception if the notification fails to send
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function sendMultiplePushNotification($envFileName = null, $tokens = [], $data)
    {
        // Initialize Firebase for each project
        $firebase = $this->initialize(base_path() . env($envFileName));
        // Get a reference to the Cloud Messaging service
        $messaging = $firebase->createMessaging();
        // Construct the FCM message
        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => $data['title'] ?? '',
                'body' => $data['body'] ?? '',
            ],

            'android' => [
                'priority' => $data['priority'] ?? 'high',
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => $data['apns-priority'] ?? '10',
                ],
            ],
        ]);

        // Send the FCM message
        try {
            $messaging->sendMulticast($message, $tokens ?? []);
            // Notification sent successfully
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Exception $e) {
            // Handle the exception if the notification fails to send
            \Log::error('Notification failed: ' . $e->getMessage());
            // Retry after a delay (e.g., 1 minute)
            sleep(60);
            // Retry the notification
            $this->sendMultiplePushNotification($envFileName = null, $tokens = [], $data);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function saveFcmToken($data)
    {
        return new FirebaseNotification($data);
    }


    public function deleteFcmToken($data)
    {
        return FirebaseNotification::query()->where('morphable_id', $data['morphable_id'] ?? 0)
            ->where('app_id', $data['app_id'] ?? 0)->where('morphable_type', 'App\Models\User')->delete();
    }
}
