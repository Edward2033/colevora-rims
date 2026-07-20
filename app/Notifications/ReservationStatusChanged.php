<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = ucfirst($this->reservation->status);

        return [
            'title' => "Reservation {$status}",
            'message' => "Your reservation for {$this->reservation->date->format('M d, Y')} at {$this->reservation->time} has been {$this->reservation->status}.",
            'url' => route('reservation'),
            'type' => 'reservation',
            'reservation_id' => $this->reservation->id,
        ];
    }
}
