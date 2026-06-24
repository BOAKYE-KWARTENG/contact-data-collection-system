<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;  // Import the SpotlightPlugin
use Filament\Notifications\Livewire\DatabaseNotifications; // Import the DatabaseNotifications widget
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;


class BkwindfredPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            // Panel Customization
            ->brandName('Contact Manager')
            ->id('bkwindfred')
            ->path('bkwindfred')
            ->favicon('images/home-button.png')
            ->login(\App\Filament\Pages\Auth\Login::class) //added custom login page
            //->registration(\App\Filament\Pages\Auth\Register::class) // added custom registration page
            //->passwordReset() // added custom password reset page
            //->emailVerification() // added custom email verification page
            ->colors([
                'primary' => Color::Blue,
            ])
            
            // Spotlight Plugin
            ->plugins([
                SpotlightPlugin::make(),
                 FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm'      => 2,
                    ]),
            ])
            ->databaseNotifications()              // Enable database notifications

            // Edit to 12hours for testing purposes (default is 60s)
            ->databaseNotificationsPolling('30s')  // optional: set polling interval for real-time updates
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\RecentContacts::class,
                \App\Filament\Widgets\ContactStatusChart::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ;
    }
}
