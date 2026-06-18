<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Notifications\ReminderNotification;
use Illuminate\Console\Command;
use App\Models\User;


class SendReminderNotifications extends Command
{
    protected $signature   = 'reminders:notify';
    protected $description = 'Send notifications for overdue, due today and upcoming reminders to owners and admins';

    public function handle(): void
    {
        $this->info('Checking reminders...');

        // 1. Get current date baselines for our calculations
        $todayStr    = today()->toDateString();
        $threeDaysOut = today()->addDays(3)->toDateString();

        // 2. Query all uncompleted reminders within the relevant timeline in ONE database hit
        $reminders = Reminder::query()
            ->where('completed', false)
            ->where('due_date', '<=', $threeDaysOut)
            ->with(['contact.user'])
            ->get();

        if ($reminders->isEmpty()) {
            $this->info('No pending reminders require notification action.');
            return;
        }

        // 3. Initialize separate counters for terminal logging
        $counts = ['overdue' => 0, 'today' => 0, 'upcoming' => 0];

        // Fetch all users with the Spatie 'admin' role once to optimize queries outside the loop
        $admins = User::role('admin')->get();

        // 4. Iterate through records and dynamically check status tags
        foreach ($reminders as $reminder) {
            $contactOwner = $reminder->contact?->user;
            
            // Safety fallback: if a contact or owner doesn't exist, skip it
            if (!$contactOwner) {
                continue;
            }

            $dueDateStr = $reminder->due_date->toDateString();

            // Determine status metrics, formatting, and classifications dynamically
            if ($dueDateStr < $todayStr) {
                $title = 'Overdue Reminder: ' . $reminder->title;
                $body  = "Action needed! This was due on " . $reminder->due_date->format('M d, Y') . " (Contact: {$reminder->contact->name})";
                $color = 'danger';
                $type  = 'overdue';
            } elseif ($dueDateStr === $todayStr) {
                $title = 'Reminder Due Today: ' . $reminder->title;
                $body  = "Scheduled for today. (Contact: {$reminder->contact->name})";
                $color = 'warning';
                $type  = 'today';
            } else {
                $title = 'Upcoming Reminder: ' . $reminder->title;
                $body  = "Due in a few days (" . $reminder->due_date->format('M d, Y') . "). (Contact: {$reminder->contact->name})";
                $color = 'info';
                $type  = 'upcoming';
            }

            // Group recipients: Combine the contact owner and all system admins into one unique collection
            $recipients = collect([$contactOwner])->merge($admins)->unique('id');

            // Dispatch using Filament's native notification mechanism to guarantee panel rendering
            foreach ($recipients as $recipient) {
                \Filament\Notifications\Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon('heroicon-o-bell')
                    ->iconColor($color)
                    ->sendToDatabase($recipient);
            }

            $counts[$type]++;
        }

        // 5. Output metrics to console
        $this->info(" {$counts['overdue']} overdue notifications sent.");
        $this->info(" {$counts['today']} due today notifications sent.");
        $this->info(" {$counts['upcoming']} upcoming notifications sent.");
        $this->info('All reminder notifications handled successfully.');
    }
}