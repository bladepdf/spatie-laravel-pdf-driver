<?php

declare(strict_types=1);

namespace BladePDF\SpatieLaravelPdf\Tests;

use BladePDF\Contracts\RenderClient;
use BladePDF\Laravel\BladePdfServiceProvider;
use BladePDF\RenderRequest;
use BladePDF\RenderResult;
use BladePDF\RenderSubmission;
use BladePDF\SpatieLaravelPdf\BladePdfDriverServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelPdf\PdfServiceProvider;

abstract class TestCase extends Orchestra
{
    protected CapturingRenderClient $bladePdfClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bladePdfClient = new CapturingRenderClient();
        $this->app->instance(RenderClient::class, $this->bladePdfClient);
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

final class CapturingRenderClient implements RenderClient
{
    /** @var list<RenderRequest> */
    public array $renders = [];

    public function render(RenderRequest $request): RenderResult
    {
        $this->renders[] = $request;

        return new RenderResult('%PDF-1.7 bladepdf-driver-test');
    }

    public function renderAsync(RenderRequest $request): RenderSubmission
    {
        $this->renders[] = $request;

        return new RenderSubmission('request-async-test');
    }
}
