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
   * @var array(pid)
   */
  private static $_arrWorkers = [];


  /**
   * Default Construtor
   *
   * @param string $pStrFilepath  Complete filepath to OCR-analyze
   */
  public function __construct($pStrFilepath)
  {
      if (file_exists($pStrFilepath)) {
          parent::__construct($pStrFilepath);
      } else {
          throw new ApplicationException('OCR-SOURCE-NOT-REACHABLE', array('msg'=>'Invalid source file. OCR analysis aborted. =>'.$pStrFilepath));
      }
  }
}
