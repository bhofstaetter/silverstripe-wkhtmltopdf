## Create PDFs in SilverStripe with the power of WKHTMLTOPDF

This module adds the possibility to simply create PDFs from every DataObject you have. Based on WKHTMLTOPDF and [mikehaertl's php wrapper](https://github.com/mikehaertl/phpwkhtmltopdf).

## Installation

``` sh
$ composer require creativesynergy/sivlerstripe-wkhtmltopdf
```

## Usage

# Basics
``` php
$trainer = Trainer::get()->first();
$pdf = new SS_PDF();
$html = $pdf::generateHtml($trainer);
$pdf->add($html);
```

# Preview in browser
``` php
$pdf->preview();
```

# Save
``` php
$pdf->save('trainer.pdf');  // This will also return an file instance to work with
```

# Force download of the file
``` php
$pdf->download('trainer.pdf');
```
