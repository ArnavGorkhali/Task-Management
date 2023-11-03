<?php


namespace App\Services\Notification;

use Illuminate\Support\Facades\Log;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\OptionsPriorities;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Psy\Util\Json;

class FcmNotification
{
    private $details;
    private $data;
    private $fcm_token;
    private $priority;

    public function __construct($details, $data, $fcm_token, $priority)
    {
        $this->details = $details;
        $this->data = $data;
        $this->fcm_token = $fcm_token;
        $this->priority = $priority;
    }

    public function sendNotification()
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setPriority($this->priority ?? 'normal');
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($this->details['title']);
        $notificationBuilder->setBody($this->details['body'])
            ->setIcon('ic_notification')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($this->details['data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = $this->fcm_token;

        if($token){
            Log::info("trying FCM with tokens : ");
            $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
            Log::info(print_r($downstreamResponse,true) );
        } else {
            Log::info("No token fcm");
        }
    }
}
