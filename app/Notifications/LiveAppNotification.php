<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiveAppNotification extends Notification
{
    use Queueable;

    public function __construct($app_details, $auth_user)
    {
        $this->app_details = $app_details;
        $this->auth_user = $auth_user;
    }


    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'Package Name' => $this->app_details['Package Name'],
            'App Name' => $this->app_details['App Name'],
            'Icon' => $this->app_details['Icon'],
            'message' => 'This App is Removed',
        ];
    }

    public function toBroadcast($notifiable)
    {
        $notification = [
            "data" => [
                'Package Name' => $this->app_details['Package Name'],
                'App Name' => $this->app_details['App Name'],
                'Icon' => $this->app_details['Icon'],
                'message' => 'This App is Live',
            ]
        ];
        return new BroadcastMessage([
            'notification' => $notification
        ]);
    }

}
