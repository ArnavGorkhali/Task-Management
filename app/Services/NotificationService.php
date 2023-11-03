<?php


namespace App\Services;

use App\Services\Notification\FcmNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{

    /**
     * Send FCM or DB or both notifications to given notifiables.
     * @param $notifiables
     * @param $payload
     * @param string $type
     */
    public function sendNotifications($notifiables, $payload, $sender_id = null, $type = "both")
    {
//        if($type == "db" || $type == "both"){
//            $this->sendDbNotification($notifiables, $payload, $sender_id);
//        }
        if($type == "fcm" || $type == "both"){
            $this->sendFcmNotification($notifiables, $payload);
        }
    }

    private function sendFcmNotification($notifiables, $payload)
    {
        $fcm_tokens = getFcmTokensFromNotifiables($notifiables);
        $fcm_notification = new FcmNotification($payload, [], $fcm_tokens, 'high');
        $fcm_notification->sendNotification();
    }

//    private function sendDbNotification($notifiables, $payload, $sender_id)
//    {
//        $db_notification = new DatabaseNotification($payload);
//        $db_notification->sender_id = $sender_id;
//        Notification::send($notifiables, $db_notification);
//    }
}
