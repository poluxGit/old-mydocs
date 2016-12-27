<?php
/**
 * Generic Task Class file.
 *
 */
namespace MyGED\Core\Tasks;

use MyGED\Exceptions\ApplicationException;
use MyGED\Core\Tasks\Task;
use MyGED\Application\Application;
use TesseractOCR;

/**
 * GenericTask Class defintion
 *
 * @package MyGED
 * @subpackage Tasks
 */
class GenericTask extends Task
{
    // /**
    //  * Input filepath
    //  *
    //  * @var array(key=>value)
    //  * @access private
    //  */
    // private $_aTasksParameters = array();
    //
    // /**
    //  * Default Construtor
    //  *
    //  * @param string $pStrFilepath  Complete filepath to OCR-analyze
    //  * @todo  Rajouter gestion des extensions de fichier
    //  */
    // public function __construct($pStrFileUID)
    // {
    //     $lStrFilePath = Vault::getFilePathByID($pStrFileUID);
    //     if (file_exists($lStrFilePath)) {
    //         // New task init.!
    //         parent::__construct(null, Application::getPDODatabase());
    //         $this->setInputFilePath($lStrFilePath);
    //     } else {
    //         throw new ApplicationException('OCR-SOURCE-NOT-REACHABLE', [['msg'=>'Invalid source file. OCR analysis aborted. FileUid:'.$pStrFileUID]]);
    //     }
    // }//end __construct()
    //
    // // Getters and Setters !
    // // =========================================================================
    // /**
    //  * Set InputFilePath object value (_inputFilePath)
    //  *
    //  * @param file  $pStrInputFilePathValue
    //  */
    // public function setInputFilePath($pStrInputFilePathValue)
    // {
    //     $this->_inputFilePath = $pStrInputFilePathValue;
    // }//end setInputFilePath()
    //
    // /**
    //  * Get InputFilePath object value (_inputFilePath)
    //  *
    //  * @return file
    //  */
    // public function getInputFilePath()
    // {
    //     return $this->_inputFilePath;
    // }//end getInputFilePath()
    //
    // /**
    //  * Set OutputTextFile object value (_outputTxtFilePath)
    //  *
    //  * @param file  $pStrOutputTextFileValue
    //  */
    // public function setOutputTextFile($pStrOutputTextFileValue)
    // {
    //     $this->_outputTxtFilePath = $pStrOutputTextFileValue;
    // }//end setOutputTextFile()
    //
    // /**
    //  * Get OutputTextFile object value (_outputTxtFilePath)
    //  *
    //  * @return file
    //  */
    // public function getOutputTextFile()
    // {
    //     return $this->_outputTxtFilePath;
    // }//end getOutputTextFile()
    //
    // /**
    //  * Set OutputDirectory object value (_outputDirectoryPath)
    //  *
    //  * @param string  $pStrOutputDirectoryValue
    //  */
    // public function setOutputDirectory($pStrOutputDirectoryValue)
    // {
    //     $this->_outputDirectoryPath = $pStrOutputDirectoryValue;
    // }//end setOutputDirectory()
    //
    // /**
    //  * Get OutputDirectory object value (_outputDirectoryPath)
    //  *
    //  * @return string
    //  */
    // public function getOutputDirectory()
    // {
    //     return $this->_outputDirectoryPath;
    // }//end getOutputDirectory()
    //
    // /**
    //  * Set CurrentStep object value (_currentStep)
    //  *
    //  * @param Integer  $pIntCurrentStep
    //  */
    //  public function setCurrentStep($pIntCurrentStep)
    //  {
    //      $this->_currentStep = $pIntCurrentStep;
    //  }//end setCurrentStep()
    //
    //  /**
    //   * Get CurrentStep object value (_currentStep)
    //   *
    //   * @return Integer
    //   */
    //   public function getCurrentStep()
    //   {
    //       return $this->_currentStep;
    //   }//end getCurrentStep()
    //
    //   /**
    //    * Set StepCount object value (_stepCount)
    //    *
    //    * @param Integer  $pIntStepCountValue
    //    */
    //    public function setStepCount($pIntStepCountValue)
    //    {
    //        $this->_stepCount = $pIntStepCountValue;
    //    }//end setStepCount()
    //
    //    /**
    //     * Get StepCount object value (_stepCount)
    //     *
    //     * @return Integer
    //     */
    //     public function getStepCount()
    //     {
    //         return $this->_stepCount;
    //     }//end getStepCount()
    //
    // /**
    //  * Launch OCR Analysis
    //  *
    //  * @param file $pStrOutputTextFile  Output file where OCR result will be written.
    //  *
    //  * @return array(string)  Array containing OCR result page by page.
    //  * @internal tesseract
    //  */
    // public function launchOCRAnalysis($pStrOutputTextFile)
    // {
    //     // File to analyze!
    //     $lStrInputFilepath = $this->getInputFilePath();
    //     $this->setOutputTextFile($pStrOutputTextFile);
    //
    //     // File exists ?
    //     if (file_exists($lStrInputFilepath)) {
    //
    //         // Task creation!
    //         $this->setTitle('OCR analysis creation. File to analyze : "'.$lStrInputFilepath.'".');
    //         $this->setCreationTimestamp(time());
    //         $lStrTaskUID = $this->createTask(null, 'ocr');
    //         $this->setStartTimeStamp(time());
    //         $this->setStatus('STARTED');
    //         $this->updateTask();
    //
    //         // Fork processus!
    //         $lIntPID = pcntl_fork();
    //
    //         if ($lIntPID == -1) { //ERROR
    //             die('duplication impossible');
    //         } elseif ($lIntPID) { // FATHER
    //             $this->setPID($lIntPID);
    //             cli_set_process_title('OCR analysis - father');
    //             pcntl_wait($status); // Protège encore des enfants zombies
    //
    //             return $lStrTaskUID;
    //         } else { // SON
    //
    //             cli_set_process_title('OCR analysis - Son - Processing');
    //
    //             // OCR Main Process!
    //             $lArrPDFFileToAnalyze = self::splitPDFPageByPage($lStrInputFilepath);
    //
    //             $this->_currentStep = 1;
    //             $this->_stepCount = count($lArrPDFFileToAnalyze);
    //
    //             $lArrResult = array();
    //             $lbFirst = true;
    //             foreach ($lArrPDFFileToAnalyze as $lStrKey => $lStrFilepath) {
    //                 // Convert Input file to PNG Image!
    //                 $this->setStatus();
    //                 $lStrCmd = "convert -density 300 -trim $lStrFilepath -quality 100 $lStrFilepath.png";
    //                 exec($lStrCmd, $lArrOutputCmd, $this->_resultCode);
    //                 if ($this->_resultCode===0) {
    //                     // OCR Processing!
    //                     $lObjOCR = new TesseractOCR("$lStrFilepath.png");
    //                     $lObjOCR->lang('fra');
    //                     $lStrOcrResult = $lObjOCR->run();
    //                     $lArrResult[] = $lStrOcrResult;
    //                     $lIntFlag = FILE_APPEND;
    //                     if ($lbFirst === true) {
    //                         $lIntFlag = 0;
    //                         $lbFirst = false;
    //                     }
    //                     //Write result to new file!
    //                     file_put_contents($pStrOutputTextFile, $lStrOcrResult, $lIntFlag);
    //                 }
    //             }
    //             // // Return OCR Analysis content !
    //             // return $lArrResult;
    //         }
    //     } else {
    //         throw new ApplicationException(
    //               'OCR-INPUT-FILE-NOT-FOUND',
    //               array('msg' =>
    //                 sprintf(
    //                   "Input file '%s' not found!",
    //                   $lStrInputFilepath
    //                   )
    //                 )
    //             );
    //     }
    // }//end launchOCRAnalysis()
    //
    // // INHERITED METHODS
    // // =========================================================================
    //
    // /**
    //  * Returns a string with json encode specific param
    //  *
    //  * @return array(paramkey=>paramValue)  Array bout Specific Parameters values
    //  */
    // protected function getTaskParametersJSON()
    // {
    // }//end getTaskParametersJSON()
    //
    // /**
    //  * Returns a string with json encode specific param
    //  *
    //  * @return array(paramkey=>paramValue)  Array bout Specific Parameters values
    //  */
    // protected function loadTaskParametersFromJSON($pStrJSONParametersValues)
    // {
    // }//end loadTaskParametersFromJSON()
}//end class
