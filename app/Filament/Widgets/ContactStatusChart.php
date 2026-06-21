<?php

// app/Filament/Widgets/ContactStatusChart.php

namespace App\Filament\Widgets;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Schema;

class ContactStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Contacts by Status';
    protected static ?string $description = 'A breakdown of all contacts across each status category.';
    protected static ?int $sort = 3; // position on dashboard, adjust as needed
    protected int|string|array $columnSpan = 'full'; // or 1 or 2

    protected function getData(): array
    {
        $statuses = [
            ContactStatus::Lead,
            ContactStatus::Prospect,
            ContactStatus::Customer,
            ContactStatus::Inactive,
        ];

        $user = auth()->user();
        $query = Contact::whereNull('deleted_at'); // start with base query, exclude soft-deleted

        // Scope to user's own contacts unless they have permissions to view all
        if ($user && !(
            $user->hasRole('admin') ||
            $user->hasRole('super_admin') ||
            $user->hasRole('team_lead')
        )) {
            $query->where('user_id', $user->id);
        }

        $counts = collect($statuses)->map(
            fn($status) => (clone $query)->where('status', $status)->count()
        );

        return [
            'datasets' => [
                [
                    'label'            => 'Contacts',
                    'data'             => $counts->values()->toArray(),
                    'backgroundColor'  => [
                        'rgba(59, 130, 246, 0.8)',  // Lead      → blue
                        'rgba(234, 179, 8, 0.8)',   // Prospect  → yellow
                        'rgba(34, 197, 94, 0.8)',   // Customer  → green
                        'rgba(156, 163, 175, 0.8)', // Inactive  → gray
                    ],
                    'borderColor'      => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(234, 179, 8, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(156, 163, 175, 1)',
                    ],
                    'borderWidth'      => 2,
                    'borderRadius'     => 6,      // rounded bar tops
                    'hoverOffset'      => 6,
                ],
            ],
            'labels' => collect($statuses)
                ->map(fn($status) => $status->getLabel())
                ->toArray(),
        ];
    }

    protected function isAdmin($user): bool
    {
        return ($user->is_admin ?? false)
            || ($user->admin ?? false)
            || (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasRole') && $user->hasRole('admin'));
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false, // labels already on X-axis
                ],
                'tooltip' => [
                    'callbacks' => [], // default tooltip is fine
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => [
                        'stepSize'  => 1,       // whole numbers only
                        'precision' => 0,
                    ],
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,     // cleaner look, no vertical gridlines
                    ],
                ],
            ],
            'responsive'          => true,
            'maintainAspectRatio' => false,
        ];
    }
}