<h1 align="center">BladePDF driver for Spatie Laravel PDF</h1>

<p align="center">
  Use BladePDF's managed Chromium rendering without changing the familiar <code>Spatie\LaravelPdf\Facades\Pdf</code> API.
</p>

<p align="center">
  <a href="https://packagist.org/packages/bladepdf/spatie-laravel-pdf-driver"><img src="https://img.shields.io/packagist/v/bladepdf/spatie-laravel-pdf-driver?style=flat-square&label=version" alt="Latest Packagist version"></a>
  <a href="https://github.com/bladepdf/spatie-laravel-pdf-driver/actions/workflows/ci.yml"><img src="https://github.com/bladepdf/spatie-laravel-pdf-driver/actions/workflows/ci.yml/badge.svg" alt="CI status"></a>
  <a href="LICENSE"><img src="https://img.shields.io/packagist/l/bladepdf/spatie-laravel-pdf-driver?style=flat-square" alt="MIT license"></a>
</p>

This package registers BladePDF as a custom driver for [Spatie Laravel PDF](https://spatie.be/docs/laravel-pdf/v2/introduction). Existing Blade views are rendered by Spatie and sent through the BladePDF Laravel asset pipeline to managed Chromium.

## Requirements

- PHP 8.2 or newer
- Laravel 11, 12, or 13
- Spatie Laravel PDF 2.10 or newer
- A BladePDF API key

## Installation

```bash
composer require bladepdf/spatie-laravel-pdf-driver:^2.0
```

Add your BladePDF API key:

```env
BLADEPDF_API_KEY=blpdf_xxxxxxxxxxxxxxxxxxxxxxxx
```

Laravel package auto-discovery registers both the BladePDF client and the Spatie driver.

## Use BladePDF for one PDF

```php
use Spatie\LaravelPdf\Facades\Pdf;

Pdf::view('pdf.invoice', ['invoice' => $invoice])
    ->driver('bladepdf')
    ->format('a4')
    ->save(storage_path('app/invoice.pdf'));
```

## Make BladePDF the default driver

```env
LARAVEL_PDF_DRIVER=bladepdf
```

Existing Spatie Laravel PDF calls then use BladePDF without calling `driver()`:

```php
return Pdf::view('pdf.invoice', ['invoice' => $invoice])
    ->name("invoice-{$invoice->number}.pdf");
```

## Supported Spatie features

| Spatie Laravel PDF feature | BladePDF behavior |
| --- | --- |
| `view()` and `html()` | Rendered HTML is sent through the BladePDF asset pipeline |
| `headerView()` / `headerHtml()` | Forwarded as Chromium header HTML |
| `footerView()` / `footerHtml()` | Forwarded as Chromium footer HTML |
| `format()` | Forwarded to BladePDF |
| `paperSize()` | Forwarded; custom dimensions take precedence over `format()` |
| `margins()` | Forwarded with the selected unit |
| `landscape()` / `portrait()` | Forwarded |
| `scale()` | Forwarded |
| `pageRanges()` | Forwarded |
| `tagged()` | Forwarded |
| `waitUntilReady()` | The readiness expression is forwarded |
| `meta()` | Applied by Spatie after BladePDF returns the PDF |
| `encrypt()` | Applied by Spatie after BladePDF returns the PDF |
| `cache()` | Uses Spatie's cache layer |
| `disk()` and `saveQueued()` | Uses Spatie's storage and queue flow |

BladePDF always enables printed backgrounds through this driver, matching Spatie's Chromium drivers.

### Readiness timeout

BladePDF supports the JavaScript expression passed to `waitUntilReady()`, but its API does not accept Spatie's optional per-expression timeout. This works:

```php
Pdf::view('pdf.report')
    ->driver('bladepdf')
    ->waitUntilReady('window.reportReady === true')
    ->save('report.pdf');
```

Passing the second timeout argument throws `UnsupportedReadinessTimeoutException` instead of silently ignoring it. Configure BladePDF's HTTP timeout and your plan's render timeout when a longer render window is required.

## Native BladePDF features

The Spatie driver is a compatibility layer. Use the native `BladePDF::` facade when you need cloud templates, request-scoped asset overrides, BladePDF references, stored PDFs, webhooks, or BladePDF's native asynchronous render API.

See the [full integration guide](https://docs.bladepdf.com/integrations/spatie-laravel-pdf).

## Testing

Spatie's `Pdf::fake()` continues to work because it fakes the builder before a driver is invoked. For driver-level tests, bind a fake implementation of `BladePDF\Contracts\RenderClient`; the immutable `BladePDF\RenderRequest` then exposes the exact core request produced by the integration.

Run this package's test suite with:

```bash
composer test
```

## Upgrading from 1.x

Driver 2.x requires `bladepdf/laravel:^2.0`; driver 1.x remains paired with the Laravel package's 1.x line. Normal Spatie calls, the `bladepdf` driver name, option mapping, and readiness behavior do not change.

```bash
composer require bladepdf/spatie-laravel-pdf-driver:^2.0 bladepdf/laravel:^2.0 --with-all-dependencies
```

The driver still depends only on `BladePdfFactory`. It does not construct the core client or asset resolver, so published Laravel asset roots apply equally to native facade and Spatie renders.

## License

The MIT License. See [LICENSE](LICENSE).
