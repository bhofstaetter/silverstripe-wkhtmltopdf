# Create PDFs in SilverStripe with the power of WKhtmlTOpdf

This module adds the possibility to simply create PDFs from every DataObject you have. Based on [WKhtmlTOpdf](http://wkhtmltopdf.org/) and [mikehaertl's php wrapper](https://github.com/mikehaertl/phpwkhtmltopdf).

## Installation

``` sh
$ composer require creativesynergy/silverstripe-wkhtmltopdf
```

## Getting started
1. [WKhtmlTOpdf](http://wkhtmltopdf.org/) must be installed on your server to use this module
2. You'll need to copy the footer.html and header.html files from the module templates folder to ``mysite/templates/Pdf/``
3. Create a css file called 'pdf.css' located under ``themes/your-theme/css/``

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
$pdf->add('https://www.google.com');              // Website
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

## Templates, Header & Footer and Styling

#### Page template
If your page ist based on an DataObject and you generate the html with the $pdf::getHtml() function, you'll be able to set a specific template to use for this page by passing it as third parameter to the function (without the .ss ending!). By default, the module will search for a template called "Classname_pdf".

#### Header & Footer
WKhtmlTOpdf let you specify seperate files for your pdf's header and footer section. By default they are located under ``mysite/templates/Pdf/header.html`` and ``mysite/templates/Pdf/footer.html``.

You can change the location of those files or remove the header and/or footer completely by changing the global or page specific options.

``` php
$pdf->setOption('header-html', '/path/to/header.html');
$pdf->removeOption('footer-html');
```

Demo Pdf.ss, header.html and footer.html files are included to get you started

#### Styling
Thanks to WKhtmlTOpdf you have full CSS3 and HTML5 support and will be able to do fancy things "without" the limitations you'll have to face while using other tools like dompdf or tcpdf. You can even use javascript to modify your pages.

By default the module requires a pdf.css in under themes/your-theme/css/pdf.css
You can change this by setting the global or page specific option

``` php
$pdf->setOption('user-style-sheet', '/path/to/pdf.css');
```
