<?php
use mikehaertl\wkhtmlto\Pdf;

/**
 * @author Benedikt Hofstaetter <info@csy.io>
 */
class SS_PDF {

  private $globalOptions;
  private $pdf;
  private $folder = ASSETS_PATH;
  private $folderID;

  function __construct() {
    $this->setGlobalOptions();
    $this->folder .= '/';
    $this->pdf = new Pdf($this->globalOptions);
  }

  /**
  * Set the global options for all pdfs
  * @param array $options A list with all possible options can be found here http://wkhtmltopdf.org/usage/wkhtmltopdf.txt
  */
  public function setGlobalOptions($options = null) {
    if(!$options) {
      $css = BASE_PATH . '/themes/' . SSViewer::current_theme() . '/css/pdf.css';
      $header = BASE_PATH . '/mysite/templates/Pdf/header.html';
      $footer = BASE_PATH . '/mysite/templates/Pdf/footer.html';

      $options = array(
        'no-outline',
        'enable-javascript',
        'enable-smart-shrinking',
        'encoding'          => 'UTF-8',
        'dpi'               => 150,
        'image-dpi'         => 150,
        'image-quality'     => 100,
        'user-style-sheet'  => $css,
        'orientation'       => 'Portrait',
        'page-height'       => '297mm',
        'page-width'        => '210mm',
        'page-size'         => 'A4',
        'margin-bottom'     => 30,
        'margin-left'       => 10,
        'margin-right'      => 10,
        'margin-top'        => 30,
        'header-html'       => $header,
        'footer-html'       => $footer,
        'binary'            => '/usr/local/bin/wkhtmltopdf'
      );
    }
    
    $this->globalOptions = $options;
    $this->pdf = new Pdf($this->globalOptions);
  }

  /**
  * Set a specific option for the pdf you are creating
  * 
  * @param string $key The name of the option you want to set.
  *                    A list with all possible options can be found here http://wkhtmltopdf.org/usage/wkhtmltopdf.txt
  * @param string|array $value The value of the option you want to set. Can be left blank.
  */

  public function setOption($key, $value = null) {
    $globalOptions = $this->globalOptions;
    
    if($value) {
      $globalOptions[$key] = $value;
    } else {
      $globalOptions[] = $key;
    }

    $this->setGlobalOptions($globalOptions);
    $this->pdf = new Pdf($this->globalOptions);
  }

  /**
  * Remove a specific option for the pdf you are creating
  * 
  * @param string $key The name of the option you want to set.
  */
  public function removeOption($key) {
    $globalOptions = $this->globalOptions;
    
    if(array_key_exists($key, $globalOptions)) {
      unset($globalOptions[$key]);
    } else if($key = array_search($key, $globalOptions)) {
      unset($globalOptions[$key]);
    }

    $this->setGlobalOptions($globalOptions);
    $this->pdf = new Pdf($this->globalOptions);
  }

  /**
  * Specify the pdf location
  * 
  * @param string $folder The name of the desired folder. Creates a new one if folder doesn't exist.
  */
  public function setFolderName($folder = null) {
    if($folder) {
      $folder = Folder::find_or_make($folder);
      $this->folderID = $folder->ID;
      $folder = str_replace('assets/', '', $folder->Filename);
      $this->folder = rtrim($this->folder . $folder, '/') . '/';
    }
  }

  /**
  * Generates the html code you need for the pdf
  * 
  * @param DataObject $obj The base DataObject for your pdf
  * @param array $variables Array with customisation for your data.
  * @param string $template If submitted the name of the template used to generate the html.
  *                         If not, the script will look for a template based on your DataObject class e.g. Trainer_pdf
  * @return string The html code for your pdf
  */
  static function getHtml($obj, $variables = null, $template = null) {
    Requirements::clear();
    
    if(!$template) {
      $template = sprintf("%s_pdf", $obj->ClassName);
    }

    $viewer = new SSViewer($template);
    $html = $viewer->process($obj, $variables);
    return $html;
  }

  /**
  * Adds a normale page or cover to your pdf
  * 
  * @param string $content The html code from your DataObject, a pdf file or any website url
  * @param string $type "Page" for a normal page or if you want to add an cover "Cover"
  * @param array $options Specific options only for that page
  *                       A list with all possible options can be found here http://wkhtmltopdf.org/usage/wkhtmltopdf.txt
  */
  public function add($content, $type = 'Page', $options = array()) {
    if($type == 'Page') {
      $this->pdf->addPage($content, $options);
    } else if($type == 'Cover') {
      $this->pdf->addCover($content, $options);
    }
  }

  /**
  * Saves the pdf file
  * 
  * @param string $filename The desired name of the pdf file
  * @param string $class Dataobject class of file
  * @return DataObject The new created pdf file
  */
  public function save($filename, $class = 'File') {
    if(!empty($this->pdf->getError())) {
      throw new Exception('Could not create PDF: ' . $this->pdf->getError());
    } else {
      $filename = rtrim($class::create()->setName($filename), '.pdf') . '.pdf';
      $this->pdf->saveAs($this->folder . $filename);
      return $this->createFile($filename, $class);
    }
  }

  /**
  * Creates an File DataObject from the pdf
  * 
  * @param string $filename The desired name of the pdf file
  * @param string $class Dataobject class of file
  * @return DataObject Pdf file
  */
  protected function createFile($filename, $class) {
    $filename = trim($filename);
    $file = $class::create();
    $file->setName($filename);
    $file->Filename = $this->folder . $filename;
    $file->ParentID = $this->folderID;
    $file->write();
    return $file;
  }

  /**
  * Streams the pdf to your browser to preview it
  */
  public function preview() {
    if(!empty($this->pdf->getError())) {
      throw new Exception('Could not create PDF: ' . $this->pdf->getError());
    } else {
      $this->pdf->send();
    }
    
    die();
  }

  /**
  * Forces the download of the pdf
  */
  public function download($filename) {
    if(!empty($this->pdf->getError())) {
      throw new Exception('Could not create PDF: ' . $this->pdf->getError());
    } else {
      $filename = rtrim(File::create()->setName($filename), '.pdf') . '.pdf';
      $this->pdf->send($filename);
    }
  }
}
