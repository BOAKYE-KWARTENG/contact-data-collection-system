<?php

namespace App\Notifications;

use App\Models\Reminder;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification
{
    use Queueable;

    protected Reminder $reminder;
    protected string $type;

    public function __construct(Reminder $reminder, string $type)
    {
        $this->reminder = $reminder;
        $this->type = $type; // 'overdue', 'today', or 'upcoming'
    }

    // ✅ Tell Laravel to store this in the database channel
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * ✅ Formats the payload to match exactly what Filament's bell icon expects
     */
    public function toDatabase(object $notifiable): array
    {
        // Set dynamic titles and status colors based on reminder timeline type
        [$title, $body, $color] = match ($this->type) {
            'overdue'  => [
                'Overdue Reminder: ' . $this->reminder->title,
                "Action needed! This was due on " . $this->reminder->due_date->format('M d, Y'),
                'danger' // Red
            ],
            'today'    => [
                'Reminder Due Today: ' . $this->reminder->title,
                "This reminder is scheduled for today.",
                'warning' // Yellow
            ],
            'upcoming' => [
                'Upcoming Reminder: ' . $this->reminder->title,
                "Due in a few days (" . $this->reminder->due_date->format('M d, Y') . ").",
                'info' // Blue
            ],
        };

        // Convert into Filament's native array structure
        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->icon('heroicon-o-bell')
            ->iconColor($color)
            ->getDatabaseMessage(); // 🌟 Crucial: This formats the array payload for Filament
    }
}