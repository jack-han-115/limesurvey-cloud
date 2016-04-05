<?php
/**
 * PDF Notification Copyright by Kai Ravesloot
 * User: kairavesloot
 * Date: 18.03.14
 * Time: 19:11
 */

class pdfNotify_createPDF extends tcpdf {

  private $filename = '';

  function __construct($surveyId, $data, $filename, $keyValid) {
    parent::__construct("P", "mm", "A4"); // L=Querformat(Landscape), P=Hochformat(Portrait)

    $pdfFormat = array();
  //  $width = 175;
  //  $height = 266;
  //  $orientation = ($height>$width) ? 'P' : 'L';
  //  $pdf->addFormat("custom", $width, $height);
  //  $pdf->reFormat("custom", $orientation);

    $this->keyValid = $keyValid;

    // set default header data
    $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(
      0,
      64,
      255
    ), array(
      0,
      64,
      128
    ));
  //   $this->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
    $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
    $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
    $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $this->SetHeaderMargin(PDF_MARGIN_HEADER);
    $this->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
    $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
    $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set default font subsetting mode
    $this->setFontSubsetting(TRUE);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
    $this->SetFont('dejavusans', '', 14, '', TRUE);
  //  $this->AddPage();

    $this->AddPage('P','A4');
    $this->dataToCell($data);

  }



  /**
   * this function creates our response table
   * for demo version it reduces the output to 6 rows!
   * @param $data
   *
   */

  public function dataToCell($data) {

    $keyValid = $this->keyValid;

    $font_size = 10;
    $this->SetTextColor(0);
    $this->SetDrawColor(30, 30, 30);
    $this->SetCellPadding(1);  //
    $this->setCellHeightRatio(1.25);
    $ratio = $this->setCellHeightRatio(1.25);
    $i = 0;
    $dimensions = $this->getPageDimensions();
    $hasBorder = FALSE; //flag for fringe case
// demo version
  if($this->keyValid == FALSE){
    $this->SetTextColor(255,0,0);

      $message = <<<EOD
This is a demo version of PDF Notify. If you need this feature, please go to <a class="link" href="www.lime-support.com" target="_blank">www.lime-support.com</a> and buy a license key.
EOD;
      $this->writeHTMLCell(0, 0, '', '', $message, 0, 1, 0, TRUE, '', TRUE);
      $this->Ln();
    }
    $this->SetTextColor(0);
    foreach ($data as $sFieldname => $value) {
      // Limitation for Demo Version
       if ($i == 6 && $this->keyValid == FALSE) {

         break;
        }

     $w = 80;
          if (substr($sFieldname, 0, 4) == 'qid_'  && empty($value[1]) && empty($value[2]) ){
            $w = 200;
          };

      // strip html tags und Enter from text
      foreach ($value as $key => $input){
       $input=  strip_tags(str_replace("\n", "", $input), '<p></br><ol><ul><li>');
       $pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/"; // use this pattern to remove any empty tag
        $input =  preg_replace($pattern, '', $input);
        $value[$key] = $input;
      }

      $rowcount = max(
        (isset($value[0]) ? $this->getNumLines($value[0], $w) : ''),
        (isset($value[1]) ? $this->getNumLines($value[1],  80) : ''),
        (isset($value[2]) ? $this->getNumLines($value[2], 80) : ''));

      $rHeight = ($rowcount * 6) + 2;

      $startY = $this->GetY();
      $border = '';
      if (($startY + $rHeight) + $dimensions['bm'] > ($dimensions['hk'])) {
        //this row will cause a page break, draw the bottom border on previous row and give this a top border
        //we could force a page break and rewrite grid headings here
        if ($hasBorder) {
          $hasBorder = FALSE;
        }
        else {
        //  $this->Ln();
          $this->checkPageBreak($this->PageBreakTrigger + 1);
      //    $this->Cell(200, 0, '', 'T'); //draw bottom border on previous row

        }
        $border = 'T';
      }
      elseif ((ceil($startY) + $rHeight) + $dimensions['bm'] == floor($dimensions['hk'])) {
        //fringe case where this cell will just reach the page break
        //draw the cell with a bottom border as we cannot draw it otherwise
        $borders = 'LRB';
        $hasborder = TRUE; //stops the attempt to draw the bottom border on the next row
      }
      else {
        //normal cell
        $borders = 'LR';  // echo strip_tags($value[0], '');
      }
      if (substr($sFieldname, 0, 4) == 'gid_') { // extract group
        $this->SetFont('', 'B', $font_size);
        $this->Cell(0, 0, '', ''); // empty row
        $this->Ln();
        $this->SetFillColor(220, 220, 220);
        $this->MultiCell(0, $rHeight, $value[0], 'LRTB', 'L', 1, 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
        $this->Ln();
      }
      elseif (substr($sFieldname, 0, 4) == 'qid_') {
        $this->SetFont('', '', $font_size);
        if (empty($value[1]) && empty($value[2])) {
          $this->Ln(1);
          $this->MultiCell(0, $rHeight, $value[0], 'LRBT'. $border, 'L', '', 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
          $this->Ln();
        }
        else {
          $this->MultiCell(100, $rHeight, $value[0], 'LRB'. $border, 'L', '', 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
          $this->MultiCell(0, $rHeight, $value[2], 'LRB'. $border, 'L', '', 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
          $this->Ln();
        }
      }
      // questions normal!!!   $w,$h,$txt,$border,$align,$fill,// subquestions, text questions
      else {
        $this->SetFont('', '', $font_size);
        if (empty($value[0])) {
          $this->MultiCell(100, $rHeight, $value[1], 'LRBT'. $border, 'L', '', 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
          $this->MultiCell(0, $rHeight, $value[2], 'LRBT'. $border, 'L', '', 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
          $this->Ln();
        }
        else {
          $this->Ln(1);
          ($i == 0) ? $border = 'LRBT' : $border = 'LRBT';
          $this->MultiCell(100, $rHeight, $value[0], $border, 'L', '', 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
          $this->MultiCell(0, $rHeight, $value[2], $border, 'L', '', 0, '', '', TRUE, 0, TRUE, TRUE, $rHeight);
          $this->Ln();
        }
      }
      $i++;
    }
  }




  /**
   * this function creates a header for PDF - not used yet
   */

  function Header() {
    //   $this->SetFont("Arial","B","16");                    // Schrift
    $this->SetTextColor(255, 000, 000); // Schriftfarbe
    $this->SetXY(20, 50); // Position
    // $this->Cell(90, 10, $this->twVariable01, 1, 1, "L"); // Box(Textbox)
  }

}
