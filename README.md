# Create PDFs in SilverStripe with the power of WKhtmlTOpdf

This module adds the possibility to simply create PDFs from every DataObject you have. Based on [WKhtmlTOpdf](http://wkhtmltopdf.org/) and [mikehaertl's php wrapper](https://github.com/mikehaertl/phpwkhtmltopdf).

## Installation

``` sh
$ composer require creativesynergy/sivlerstripe-wkhtmltopdf
```

## Usage

#### Basics
``` php
$trainer = Trainer::get()->first();
$pdf = new SS_PDF();
$html = $pdf::generateHtml($trainer);
$pdf->add($html);
```

#### Add a cover
``` php
$pdf->add('.../path/to/cover.html', 'Cover');     // You could use the same inputs as listed under "Add pages"
```

#### Add pages
``` php
$pdf->add('<html>...</html>');                    // Html code
$pdf->add('.../path/to/page.html');               // Html files
$pdf->add('https://www.csy.io');                  // Websites
$pdf->add($pdf::generateHtml($dataObject));       // DataObjects
```

#### Preview in browser
``` php
$pdf->preview();
```

#### Save
``` php
$pdf->save('trainer.pdf');                        // This will also return an file instance to work with
```

#### Download
``` php
$pdf->download('trainer.pdf');
```
