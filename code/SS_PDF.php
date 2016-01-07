<?php
use mikehaertl\wkhtmlto\Pdf;

class SS_PDF {

  private $globalOptions;
  private $pdf;
  private $folder = ASSETS_PATH . '/';
  private $folderID;

  function __construct() {
    $this->setGlobalOptions();
    $this->pdf = new Pdf($this->globalOptions);
  }

  public function setGlobalOptions($options = null) {
    if(!$options) {
      $css = BASE_PATH . '/themes/' . SSViewer::current_theme() . '/css/pdf.css';
      $header = BASE_PATH . '/mysite/templates/Pdfs/header.html';
      $footer = BASE_PATH . '/mysite/templates/Pdfs/footer.html';

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
        'footer-html'       => $footer
      );
    }
    
    $this->globalOptions = $options;
  }

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

  public function setFolderName($folder = null) {
    if($folder) {
      $folder = Folder::find_or_make($folder);
      $this->folderID = $folder->ID;
      $folder = str_replace('/assets/', '', $folder->Url);
      $this->folder = rtrim($this->folder . $folder, '/') . '/';
    }
  }

  static function getHtml($obj, $variables = null, $template = null) {
    Requirements::clear();
    
    if(!$template) {
      $template = sprintf("%s_pdf", $obj->ClassName);
    }

    $viewer = new SSViewer($template);
    $html = $viewer->process($obj, $variables);
    return $html;
  }

  public function add($content, $type = 'Page', $options = array()) {
    if($type == 'Page') {
      $this->pdf->addPage($content, $options);
    } else if($type == 'Cover') {
      $this->pdf->addCover($content, $options);
    }
  }

  public function save($filename) {
    if($this->pdf->toString() === false) {
      throw new Exception('Could not create PDF: '.$this->pdf->getError());
    } else {
      $filename = rtrim(File::create()->setName($filename), '.pdf') . '.pdf';
      $this->pdf->saveAs($this->folder . $filename);
      return $this->createFile($filename);
    }
  }

  protected function createFile($filename) {
    $filename = trim($filename);
    $file = File::create();
    $file->setName($filename);
    $file->Filename = $this->folder . $filename;
    $file->ParentID = $this->folderID;
    $file->write();
    return $file;
  }

  public function preview() {
    if($this->pdf->toString() === false) {
      throw new Exception('Could not create PDF: '.$this->pdf->getError());
    } else {
      $this->pdf->send();
    }
  }

  public function download($filename) {
    if($this->pdf->toString() === false) {
      throw new Exception('Could not create PDF: '.$this->pdf->getError());
    } else {
      $filename = rtrim(File::create()->setName($filename), '.pdf') . '.pdf';
      $this->pdf->send($filename);
    }
  }
}