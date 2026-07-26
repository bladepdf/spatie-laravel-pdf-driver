<?php

declare(strict_types=1);

namespace BladePDF\SpatieLaravelPdf;

use BladePDF\Laravel\BladePdfFactory;
use BladePDF\Laravel\PendingRender;
use BladePDF\SpatieLaravelPdf\Exceptions\UnsupportedReadinessTimeoutException;
use Spatie\LaravelPdf\Drivers\PdfDriver;
use Spatie\LaravelPdf\Drivers\SupportsReadiness;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\LaravelPdf\PdfOptions;

final readonly class BladePdfDriver implements PdfDriver, SupportsReadiness
{
    public function __construct(private BladePdfFactory $bladePdf)
    {
    }

    public function generatePdf(
        string $html,
        ?string $headerHtml,
        ?string $footerHtml,
        PdfOptions $options,
    ): string {
        return $this->pendingRender($html, $headerHtml, $footerHtml, $options)
            ->render()
            ->pdf();
    }

    public function savePdf(
        string $html,
        ?string $headerHtml,
        ?string $footerHtml,
        PdfOptions $options,
        string $path,
    ): void {
        $this->pendingRender($html, $headerHtml, $footerHtml, $options)
            ->render()
            ->save($path);
    }

    private function pendingRender(
        string $html,
        ?string $headerHtml,
        ?string $footerHtml,
        PdfOptions $options,
    ): PendingRender {
        if ($options->waitForReadyTimeout !== null) {
            throw UnsupportedReadinessTimeoutException::forTimeout($options->waitForReadyTimeout);
        }

        $render = $this->bladePdf
            ->fromHtml($html)
            ->showBackground();

        if ($headerHtml !== null) {
            $render->withHeaderHtml($headerHtml);
        }

        if ($footerHtml !== null) {
            $render->withFooterHtml($footerHtml);
        }

        if ($options->paperSize !== null) {
            $render->paperSize(
                $options->paperSize['width'],
                $options->paperSize['height'],
                $options->paperSize['unit'] ?? 'mm',
            );
        } elseif ($options->format !== null) {
            $render->format($options->format);
        }

        if ($options->margins !== null) {
            $render->margins(
                $options->margins['top'],
                $options->margins['right'],
                $options->margins['bottom'],
                $options->margins['left'],
                $options->margins['unit'] ?? 'mm',
            );
        }

        if ($options->orientation === Orientation::Landscape->value) {
            $render->landscape();
        } elseif ($options->orientation !== null) {
            $render->portrait();
        }

        if ($options->scale !== null) {
            $render->scale($options->scale);
        }

        if ($options->pageRanges !== null) {
            $render->pageRanges($options->pageRanges);
        }

        if ($options->tagged) {
            $render->taggedPdf();
        }

        if ($options->waitForReady !== null) {
            $render->waitFunction($options->waitForReady);
        }

        return $render;
    }
}
