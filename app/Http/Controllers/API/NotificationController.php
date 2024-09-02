<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationController extends Controller
{
    protected $notification;
    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }

    public function notification()
    {
        $FcmToken = Auth::user()->fcm_token;
        $message = CloudMessage::fromArray([
            'token' => $FcmToken,
            'notification' => [
                'title' => "test",
                'body' => "test",
            ],
        ]);

        $this->notification->send($message);
        return response()->json(['message' => 'Notification sent successfully', 'data' => $message]);
    }
}
