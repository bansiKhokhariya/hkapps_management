<?php

namespace App\Notifications;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class RemoveAppNotification extends Notification
{
    use Queueable, Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($app_details,$auth_user)
    {
        $this->app_details = $app_details;
        $this->auth_user = $auth_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */

    public function toArray($notifiable)
    {

        return [
            'Package Name' => $this->app_details['Package Name'],
            'App Name'=>$this->app_details['App Name'],
            'Icon' => $this->app_details['Icon'],
            'message' => 'This App is Removed',
        ];
    }


    public function toBroadcast($notifiable)
    {
        $notification = [
            "data" => [
                'Package Name' => $this->app_details['Package Name'],
                'App Name'=>$this->app_details['App Name'],
                'Icon' => $this->app_details['Icon'],
                'message' => 'This App is Removed',
            ]
        ];
        return new BroadcastMessage([
            'notification' => $notification
        ]);
    }
}
