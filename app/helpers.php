<?php

use Illuminate\Support\Facades\Auth;

function active_class($path, $active = 'active')
{
    return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function is_active_route($path)
{
    return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

function show_class($path)
{
    return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}

function processBusinessName($name)
{
    $name = str_replace(" ", "_", $name);
    $name = strtolower($name);
    return $name;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function is_serialized($data)
{
    // if it isn't a string, it isn't serialized
    if (!is_string($data))
        return false;
    $data = trim($data);
    if ('N;' == $data)
        return true;
    if (!preg_match('/^([adObis]):/', $data, $badions))
        return false;
    switch ($badions[1]) {
        case 'a' :
        case 'O' :
        case 's' :
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                return true;
            break;
    }
    return false;
}

function getModelName(\Illuminate\Database\Eloquent\Model $object)
{
    return lcfirst(explode('\\', get_class($object))[2]);
}

function success($message, $data = null)
{
    $responseData = [
        'success' => true,
        'message' => $message,
    ];

    if (!is_null($data)) {
        $responseData['data'] = $data;
    }

    return response()->json($responseData, 200);
}

function failure($message, $statusCode = 500, $data = null)
{
    $responseData = [
        'success' => false,
        'message' => $message,
    ];
    if (null != $data) {
        $responseData['data'] = $data;
    }
    return response()->json($responseData, $statusCode);
}

function getFcmTokensFromNotifiables($notifiables)
{
    $fcm_tokens = [];
    if (count($notifiables) > 0) {
        foreach ($notifiables as $notifiable) {
            $active_devices = $notifiable->activeDevices;

            if ($active_devices->count() > 0) {
                foreach ($active_devices as $active_device) {
                    $fcm_tokens[] = $active_device->fcm_token;
                }
            }
        }
    }
    return $fcm_tokens;
}
