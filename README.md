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
$pdf->save('trainer.pdf');
```

#### Add a cover
``` php
$pdf->add('.../path/to/cover.html', 'Cover');     // You could use the same inputs as listed under "Add pages"
```

#### Add pages
``` php
$pdf->add('<html>...</html>');                    // Html code
$pdf->add('.../path/to/page.html');               // Html file
$pdf->add('https://www.csy.io');                  // Website
$pdf->add($pdf::generateHtml($dataObject));       // DataObject
```

#### Add specific options for one page
``` php
$options = array(
  'image-quality'     => 100,
  'margin-bottom'     => 0,
  'margin-left'       => 0,
  'margin-right'      => 0,
  'margin-top'        => 0,
  'header-html'       => null,
  'footer-html'       => null
);

$pdf->add('.../path/to/cover.html', 'Cover', $options);
```
All available options can be found [here](http://wkhtmltopdf.org/usage/wkhtmltopdf.txt)

#### Add a page and pass some variables to it
``` php
$variables = array(
  'Title'             => 'My New Title',
  'MyFreakyWhatever'  => DataObject::get()->byID(123)->WhatEver()
);

$html = $pdf::generateHtml($trainer, $variables);

$pdf->add($html);
```

#### Change the page template
``` php
$html = $pdf::generateHtml($trainer, $variables, 'NewPDFTemplate');
$pdf->add($html);
```
By default the module will look for a template called "ClassName_pdf"

#### Preview in browser
``` php
$pdf->preview();
```

#### Save
``` php
$pdf->save('trainer.pdf');                        // This will also return an file instance to work with
```

#### Specify the folder to save in
``` php
$pdf->setFolderName('trainers/pdfs');
```

#### Download
``` php
$pdf->download('trainer.pdf');
```

## Global options

#### Set the global options used for all pages in this pdf
``` php
$css = BASE_PATH . '/themes/' . SSViewer::current_theme() . '/css/pdf.css';
$header = BASE_PATH . '/mysite/templates/Pdfs/header.html';
$footer = BASE_PATH . '/mysite/templates/Pdfs/footer.html';

$options = array(
  'enable-javascript',
  'dpi'               => 150,
  'image-dpi'         => 150,
  'image-quality'     => 100,
  'user-style-sheet'  => $css,
  'header-html'       => $header,
  'footer-html'       => $footer,
  ...
  ...
);

$pdf->setGlobalOptions($options);
```

#### Set or modifiy only one global option
``` php
$pdf->setOption('enable-javascript');

$pdf->setOption('dpi', '300');

$pdf->setOption('run-script', array(
  '../path/to/local/script1.js'
));

$pdf->setOption('replace', array(
  '{whatever}' => 'something new'
));
```

#### Remove one global option
``` php
$pdf->removeOption('replace');
```

## Templates

#### Page template

