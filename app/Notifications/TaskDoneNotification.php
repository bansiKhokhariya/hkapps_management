<?php

namespace App\Notifications;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class TaskDoneNotification extends Notification implements ShouldBroadcast
{
    use Queueable, Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($task_details,$auth_user)
    {
        $this->task_details = $task_details;
        $this->auth_user = $auth_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task_details['task_id'],
            'app_no'=>$this->task_details['app_no'],
            'task_title' => $this->task_details['task_title'],
            'user_id' => $this->auth_user->id,
            'user_name' => $this->auth_user->name,
            'profile_image' => $this->auth_user->profile_image,
            'message' => $this->task_details['app_no'].' - '.$this->task_details['task_title'].' done by '.$this->auth_user->name.'.',
        ];
    }
    public function toBroadcast($notifiable)
    {
        $notification = [
            "data" => [
                'task_id' => $this->task_details['task_id'],
                'app_no'=>$this->task_details['app_no'],
                'task_title' => $this->task_details['task_title'],
                'user_id' => $this->auth_user->id,
                'user_name' => $this->auth_user->name,
                'profile_image' => $this->auth_user->profile_image,
                'message' => $this->task_details['app_no'].' - '.$this->task_details['task_title'].' done by '.$this->auth_user->name.'.',
            ]
        ];
        return new BroadcastMessage([
            'notification' => $notification
        ]);
    }
}
