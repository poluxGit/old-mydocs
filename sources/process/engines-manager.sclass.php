<?php
/**
 * Engines Manager
 *
 */
namespace MyGED\Process;

/**
 * Main Static class for threaded process management
 *
 * @package MyGED
 * @subpackage EnginesManager
 */
class EngineFactory
{
  /**
   * Workers in progress
   *
   * @var array(Thread)
   */
  private static $_arrWorkers = [];

  

  /**
   * Default Construtor
   *
   * @param string $pStrFilepath  Complete filepath to OCR-analyze
   */
  function __construct($pStrFilepath)
  {
    if(file_exists($pStrFilepath))
    {
      parent::__construct($pStrFilepath);
    }
    else {
      throw new ApplicationException('OCR-SOURCE-NOT-REACHABLE',[['msg'=>'Invalid source file. OCR analysis aborted. =>'.$pStrFilepath]]);
    }
  }

  /**
   * Launch OCR Analysis
   *
   * @return string OCR result
   */
  public function launchOCRAnalysis() {
    $this->_StrResult = $this->run();
    return $this->_StrResult;
  }//end launchOCRAnalysis()
}



























?>
