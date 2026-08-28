<?php

declare(strict_types=1);

namespace BladePDF\SpatieLaravelPdf\Tests;

use BladePDF\SpatieLaravelPdf\BladePdfDriver;
use Spatie\LaravelPdf\Drivers\PdfDriver;
use Spatie\LaravelPdf\Facades\Pdf;

final class ServiceProviderTest extends TestCase
{
    public function test_it_registers_the_named_bladepdf_driver(): void
    {
        $driver = $this->app->make(BladePdfDriver::class);

        $this->assertSame(
            $driver,
            $this->app->make('laravel-pdf.driver.bladepdf'),
        );
    }

    public function test_it_registers_bladepdf_as_the_configured_default_driver(): void
    {
        $this->assertInstanceOf(
            BladePdfDriver::class,
            $this->app->make(PdfDriver::class),
        );
    }

    public function test_spatie_facade_can_use_bladepdf_as_a_named_driver(): void
    {
        $pdf = Pdf::html('<h1>Named driver</h1>')
            ->driver('bladepdf')
            ->format('a4')
            ->generatePdfContent();

        $this->assertSame('%PDF-1.7 bladepdf-driver-test', $pdf);
        $this->assertSame(
            '<h1>Named driver</h1>',
            $this->bladePdfClient->renders[0]->html,
        );
    }

    public function test_spatie_facade_uses_bladepdf_as_the_default_driver(): void
    {
        $pdf = Pdf::html('<h1>Default driver</h1>')
            ->generatePdfContent();

        $this->assertSame('%PDF-1.7 bladepdf-driver-test', $pdf);
        $this->assertSame(
            '<h1>Default driver</h1>',
            $this->bladePdfClient->renders[0]->html,
        );
    }
}
