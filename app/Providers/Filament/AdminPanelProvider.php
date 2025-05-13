<?php

namespace App\Providers\Filament;

use App\Http\Middleware\VerifyIsAdmin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->profile()
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit profile')->color('primary')->icon(asset('images/avatars/default.png')),
                MenuItem::make()
                    ->label('Dashboard')
                    ->url('/app')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Stone,
                'info' => Color::Blue,
                'primary' => Color::Violet,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('inter')
            ->brandName('Filament')
            ->brandLogo(asset('images/admin-logo.png'))
            ->brandLogoHeight('6rem')
            ->favicon(asset('images/admin-logo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->topNavigation()
            ->widgets([
                Widgets\AccountWidget::class,
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
                VerifyIsAdmin::class, // Custom middleware to check if the user is an admin
            ])
            ->navigationGroups([ // Optional: create and controll of order in grouping in the navigation
                'Employees Managment',
                'System Managment',
                'User Managment',
            ])
        ;
    }
}
