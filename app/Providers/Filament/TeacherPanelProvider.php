<?php

namespace App\Providers\Filament;

use App\Filament\Auth\TeacherCustomLogin;
use App\Filament\Teacher\Widgets\AdvancedTeacherStatsOverviewWidget;
use App\Filament\Widgets\AdvancedAdminStatsOverviewWidget;
use App\Http\Middleware\VeriftIsTeacher;
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
use Filament\Navigation\MenuItem;

class TeacherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teacher')
            ->path('teacher')
            ->brandName('Student Information Management')
            ->brandLogo(asset('Images/Logo/Logo.jpg'))
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('Images/Icons/favicon.jpg'))
            ->login(TeacherCustomLogin::class)
            ->profile()
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->font('Poppins')
            ->userMenuItems([
                'logout' => MenuItem::make()->label('Logout')
            ])
            ->discoverResources(in: app_path('Filament/Resources/Teacher'), for: 'App\\Filament\\Resources\\Teacher')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Teacher/Widgets'), for: 'App\\Filament\\Teacher\\Widgets')
            ->widgets([
                AdvancedTeacherStatsOverviewWidget::class,
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
                VeriftIsTeacher::class,
            ]);
    }
}
