<?php

declare(strict_types=1);

namespace BladePDF\SpatieLaravelPdf\Tests;

use BladePDF\Laravel\BladePdfClient;
use BladePDF\Laravel\BladePdfServiceProvider;
use BladePDF\Laravel\RenderResult;
use BladePDF\SpatieLaravelPdf\BladePdfDriverServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelPdf\PdfServiceProvider;

abstract class TestCase extends Orchestra
{
    protected CapturingBladePdfClient $bladePdfClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bladePdfClient = new CapturingBladePdfClient();
        $this->app->instance(BladePdfClient::class, $this->bladePdfClient);
    }

    protected function getPackageProviders($app): array
    {
        return [
            BladePdfServiceProvider::class,
            PdfServiceProvider::class,
            BladePdfDriverServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.url', 'http://localhost');
        $app['config']->set('bladepdf.api_key', 'test-api-key');
        $app['config']->set('bladepdf.auto_resolve_assets', false);
        $app['config']->set('laravel-pdf.driver', 'bladepdf');
    }
}

final class CapturingBladePdfClient extends BladePdfClient
{
    /**
     * @var list<array{fields:array<string, mixed>,assets:array<int, mixed>}>
     */
    public array $renders = [];

    public function __construct()
    {
    }

    public function render(array $fields, array $assets = []): RenderResult
    {
        $this->renders[] = [
            'fields' => array_filter(
                $fields,
                static fn (mixed $value): bool => $value !== null,
            ),
            'assets' => $assets,
        ];

        return new RenderResult('%PDF-1.7 bladepdf-driver-test');
    }
}
