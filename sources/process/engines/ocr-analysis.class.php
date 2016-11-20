<?php
/**
 * OCR Analysis Class file.
 *
 */
namespace MyGED\Process\Engines;

use MyGED\Exceptions\ApplicationException;
use TesseractOCR;

/**
 * Proceed an OCR Analysis of a file.
 *
 * @package MyGED
 * @subpackage OCR
 */
class OCRAnalysis
{
    /**
   * Input filepath
   *
   * @var string
   * @access private
   */
  private $_strInputFilePath = null;

  /**
   * Output text filepath
   *
   * @var string
   * @access private
   */
  private $_strOutputTxtFilePath = null;

  /**
   * Output Pattern for images extraction filename
   *
   * @var string
   * @access private
   */
  private $_strOutputImagePatternFilename = null;

  /**
   * Default Construtor
   *
   * @param string $pStrFilepath  Complete filepath to OCR-analyze
   * @todo  Rajouter gestion des extensions de fichier
   */
  public function __construct($pStrInputFilepath, $pStrOutputTextFile=null)
  {
      if (file_exists($pStrInputFilepath)) {
          parent::__construct($pStrInputFilepath);
          $this->_strInputFilePath = $pStrInputFilepath;
      } else {
          throw new ApplicationException('OCR-SOURCE-NOT-REACHABLE', [['msg'=>'Invalid source file. OCR analysis aborted. =>'.$pStrInputFilepath]]);
      }
  }

  /**
   * Launch OCR Analysis
   *
   * @return string OCR result
   * @internal tesseract
   */
  public function launchOCRAnalysis($pStrOutputTextFile)
  {
      $this->lang('fra');
      return $this->run();
  }//end launchOCRAnalysis()
}
