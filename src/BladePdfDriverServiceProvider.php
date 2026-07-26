<?php

declare(strict_types=1);

namespace BladePDF\SpatieLaravelPdf;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPdf\Drivers\PdfDriver;

final class BladePdfDriverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BladePdfDriver::class);
        $this->app->alias(BladePdfDriver::class, 'laravel-pdf.driver.bladepdf');
    }

    public function boot(): void
    {
        if ($this->app['config']->get('laravel-pdf.driver') !== 'bladepdf') {
            return;
        }

        $this->app->singleton(
            PdfDriver::class,
            fn ($app): BladePdfDriver => $app->make(BladePdfDriver::class),
        );
    }
}
