<?php

declare(strict_types=1);

namespace BladePDF\SpatieLaravelPdf\Tests;

use BladePDF\SpatieLaravelPdf\BladePdfDriver;
use BladePDF\SpatieLaravelPdf\Exceptions\UnsupportedReadinessTimeoutException;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\LaravelPdf\PdfOptions;

final class BladePdfDriverTest extends TestCase
{
    public function test_it_generates_pdf_bytes_and_maps_spatie_options(): void
    {
        $options = new PdfOptions();
        $options->format = 'a4';
        $options->margins = [
            'top' => 10.0,
            'right' => 12.0,
            'bottom' => 14.0,
            'left' => 16.0,
            'unit' => 'mm',
        ];
        $options->orientation = Orientation::Landscape->value;
        $options->scale = 0.75;
        $options->pageRanges = '1-3, 5';
        $options->tagged = true;
        $options->waitForReady = 'window.invoiceReady === true';

        $pdf = $this->driver()->generatePdf(
            '<main>Invoice</main>',
            '<header>Header</header>',
            '<footer>Page <span class="pageNumber"></span></footer>',
            $options,
        );

        $this->assertSame('%PDF-1.7 bladepdf-driver-test', $pdf);

        $fields = $this->bladePdfClient->renders[0]['fields'];

        $this->assertSame(['type' => 'html'], $fields['source']);
        $this->assertSame('<main>Invoice</main>', $fields['html']);
        $this->assertSame('<header>Header</header>', $fields['header_html']);
        $this->assertSame(
            '<footer>Page <span class="pageNumber"></span></footer>',
            $fields['footer_html'],
        );
        $this->assertSame('window.invoiceReady === true', $fields['wait_function']);
        $this->assertSame([
            'printBackground' => true,
            'format' => 'a4',
            'margin' => [
                'top' => '10mm',
                'right' => '12mm',
                'bottom' => '14mm',
                'left' => '16mm',
            ],
            'landscape' => true,
            'scale' => 0.75,
            'pageRanges' => '1-3, 5',
            'tagged' => true,
        ], $fields['pdf_options']);
    }

    public function test_custom_paper_size_takes_precedence_over_format(): void
    {
        $options = new PdfOptions();
        $options->format = 'letter';
        $options->paperSize = [
            'width' => 210.0,
            'height' => 297.0,
            'unit' => 'mm',
        ];

        $this->driver()->generatePdf('<p>Custom paper</p>', null, null, $options);

        $pdfOptions = $this->bladePdfClient->renders[0]['fields']['pdf_options'];

        $this->assertSame('210mm', $pdfOptions['width']);
        $this->assertSame('297mm', $pdfOptions['height']);
        $this->assertArrayNotHasKey('format', $pdfOptions);
    }

    public function test_portrait_orientation_is_forwarded_explicitly(): void
    {
        $options = new PdfOptions();
        $options->orientation = Orientation::Portrait->value;

        $this->driver()->generatePdf('<p>Portrait</p>', null, null, $options);

        $this->assertFalse(
            $this->bladePdfClient->renders[0]['fields']['pdf_options']['landscape'],
        );
    }

    public function test_it_saves_generated_pdf_bytes_to_a_local_path(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bladepdf-spatie-driver-');

        try {
            $this->driver()->savePdf(
                '<p>Save me</p>',
                null,
                null,
                new PdfOptions(),
                $path,
            );

            $this->assertSame('%PDF-1.7 bladepdf-driver-test', file_get_contents($path));
        } finally {
            if (is_string($path) && file_exists($path)) {
                unlink($path);
            }
        }
    }

    public function test_custom_readiness_timeout_is_rejected_instead_of_ignored(): void
    {
        $options = new PdfOptions();
        $options->waitForReady = 'window.pdfReady === true';
        $options->waitForReadyTimeout = 5000;

        $this->expectException(UnsupportedReadinessTimeoutException::class);
        $this->expectExceptionMessage('per-render timeout of 5000 ms');

        $this->driver()->generatePdf('<p>Ready</p>', null, null, $options);
    }

    private function driver(): BladePdfDriver
    {
        return $this->app->make(BladePdfDriver::class);
    }
}
