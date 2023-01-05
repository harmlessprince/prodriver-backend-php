<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Notifications\VerifyEmailNotification;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendEmailVerificationNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(private readonly UserService $userService)
    {

    }

    /**
     * Handle the event.
     *
     * @param UserRegisteredEvent $event
     * @return void
     */
    public function handle(UserRegisteredEvent $event): void
    {
        $this->userService->sendEmailVerificationNotification($event->user, $event->token);
    }
}
