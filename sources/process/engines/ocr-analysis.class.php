<?php
/**
 * OCR Analysis Class file.
 *
 */
namespace MyGED\Process\Engines;

use MyGED\Exceptions\ApplicationException;
use MyGED\Core\Tasks\GenericTask;
use MyGED\Core\Tasks\AbstractTask;
use MyGED\Vault\Vault;
use MyGED\Application\Application as Application;
use MyGED\Core\FileSystem\FileSystem as FileSystem;
use MyGED\Core\FileSystem\PDFHandler as PDFHandler;
use TesseractOCR;

use MyGED\Process\Engines\OCRAnalysis;

/**
 * Proceed an OCR Analysis of a file.
 *
 * @package MyGED
 * @subpackage OCR
 */
class OCRAnalysis extends AbstractTask
{
    /**
     * Input filepath
     *
     * @var string
     * @access private
     */
    private $_inputFilePath = null;

    /**
     * Input file UID
     *
     * @var string
     * @access private
     */
    private $_inputFileUID = null;

    /**
     * Output text filepath
     *
     * @var string
     * @access private
     */
    private $_outputTxtFilePath = null;

    /**
     * Output Pattern for images extraction filename
     *
     * @var string
     * @access private
     */
    private $_outputImagePatternFilename = null;

    /**
     * Output Directory for OCR Analysis.
     *
     * @var string
     * @access private
     */
    private $_outputDirectoryPath = '';

    private $_stepCount = 0;
    private $_currentStep = 0;

    /**
     * Default Construtor
     *
     * @param string $pStrFilepath  Complete filepath to OCR-analyze
     * @todo  Rajouter gestion des extensions de fichier
     */
    public function __construct($pStrFileUID=null)
    {
        parent::__construct(null, Application::getAppDabaseObject());
        $this->setInputFileUID($pStrFileUID);
    }//end __construct()

    // Getters and Setters !
    // =========================================================================
    /**
     * Set InputFilePath object value (_inputFilePath)
     *
     * @param file  $pStrInputFilePathValue
     */
    public function setInputFilePath($pStrInputFilePathValue)
    {
        $this->_inputFilePath = $pStrInputFilePathValue;
    }//end setInputFilePath()

    /**
     * Get InputFilePath object value (_inputFilePath)
     *
     * @return file
     */
    public function getInputFilePath()
    {
        return $this->_inputFilePath;
    }//end getInputFilePath()

    /**
     * Set InputFilePath object value (_inputFilePath)
     *
     * @param string  $pStrInputFileUIDValue
     */
    public function setInputFileUID($pStrInputFileUIDValue)
    {
        $this->_inputFileUID = $pStrInputFileUIDValue;
    }//end setInputFilePath()

    /**
     * Get InputFilePath object value (_inputFilePath)
     *
     * @return file
     */
    public function getInputFileUID()
    {
        return $this->_inputFileUID;
    }//end getInputFilePath()

    /**
     * Set OutputTextFile object value (_outputTxtFilePath)
     *
     * @param file  $pStrOutputTextFileValue
     */
    public function setOutputTextFile($pStrOutputTextFileValue)
    {
        $this->_outputTxtFilePath = $pStrOutputTextFileValue;
    }//end setOutputTextFile()

    /**
     * Get OutputTextFile object value (_outputTxtFilePath)
     *
     * @return file
     */
    public function getOutputTextFile()
    {
        return $this->_outputTxtFilePath;
    }//end getOutputTextFile()

    /**
     * Set OutputDirectory object value (_outputDirectoryPath)
     *
     * @param string  $pStrOutputDirectoryValue
     */
    public function setOutputDirectory($pStrOutputDirectoryValue)
    {
        $this->_outputDirectoryPath = $pStrOutputDirectoryValue;
    }//end setOutputDirectory()

    /**
     * Get OutputDirectory object value (_outputDirectoryPath)
     *
     * @return string
     */
    public function getOutputDirectory()
    {
        return $this->_outputDirectoryPath;
    }//end getOutputDirectory()

    /**
     * Set CurrentStep object value (_currentStep)
     *
     * @param Integer  $pIntCurrentStep
     */
     public function setCurrentStep($pIntCurrentStep)
     {
         $this->_currentStep = $pIntCurrentStep;
     }//end setCurrentStep()

    /**
     * Get CurrentStep object value (_currentStep)
     *
     * @return Integer
     */
    public function getCurrentStep()
    {
      return $this->_currentStep;
    }//end getCurrentStep()

  /**
   * Set StepCount object value (_stepCount)
   *
   * @param Integer  $pIntStepCountValue
   */
   public function setStepCount($pIntStepCountValue)
   {
       $this->_stepCount = $pIntStepCountValue;
   }//end setStepCount()

   /**
    * Get StepCount object value (_stepCount)
    *
    * @return Integer
    */
    public function getStepCount()
    {
        return $this->_stepCount;
    }//end getStepCount()

    /**
     * Instanciate a new OCR Task
     *
     * @return string Task UID
     */
    public static function createNewOCRTask()
    {
      // New task init.!
      $lObjOCRTask = new OCRAnalysis();
      $lStrTaskUID = $lObjOCRTask->createTask(null, 'ocr');
      $lObjOCRTask->setTitle('OCR analysis task.');
      $lObjOCRTask->setCreationTimestamp(time());
      $lObjOCRTask->setStartTimeStamp(time());
      $lObjOCRTask->setStatus('INIT');
      $lObjOCRTask->updateTask();
      return $lStrTaskUID;
    }//end createNewOCRTask()

    /**
     * Launch OCR Analysis
     *
     * @param bool $pBoolForceOCR   Force OCR analysis and images extraction if set to TRUE.
     *
     * @return array(string)  Array containing OCR result page by page.
     * @internal tesseract
     */
    public function launchOCRAnalysis($pBoolForceOCR = false)
    {
        if(is_null($this->getInputFileUID()))
        {
          throw new ApplicationException('OCR-INVALID-PARAM', [['msg'=>'Input File UID  must be set before launching analysis. OCR analysis aborted.']]);
        }
        else {
          $lStrFilePath = Vault::getFilePathByID($this->_inputFileUID);
          if (file_exists($lStrFilePath)) {
              $this->setInputFilePath($lStrFilePath);
          } else {
              throw new ApplicationException('OCR-SOURCE-NOT-REACHABLE', [['msg'=>'Invalid source file. OCR analysis aborted. FileUid:'.$pStrFileUID]]);
          }
        }

        //$this->loadTask($this->_idTask);
        $lStrTaskUID=$this->_idTask;

        // File to analyze!
        $lStrInputFilepath = $this->getInputFilePath();

        // Define Output TXT file for OCR Result storage!
        $lStrOutputTxtFilepath = Vault::getVaultOCRDirectory().$this->getInputFileUID().'/'.$this->getInputFileUID().'.txt';
        $lStrOutputTxtDirectoryPath = Vault::getVaultOCRDirectory().$this->getInputFileUID();

        if(!file_exists($lStrOutputTxtDirectoryPath)){
          mkdir($lStrOutputTxtDirectoryPath);
        }

        // OCR Result already performed ?
        if(file_exists($lStrOutputTxtFilepath) && filesize($lStrOutputTxtFilepath)>0 && !$pBoolForceOCR)
        {
          return file_get_contents($lStrOutputTxtFilepath);
        }
        else {
          $this->setOutputTextFile($lStrOutputTxtFilepath);

          //  Input File exists ?
          if (file_exists($lStrInputFilepath))
          {
            // Task update!
            $this->setTitle('OCR analysis creation. File to analyze : '.$lStrInputFilepath);
            $this->setCreationTimestamp(time());
            $this->setStartTimeStamp(time());
            $this->setStatus('STARTED');
            $this->updateTask();

            // OCR Main Process!
            if(FileSystem::getExtensionFromPath($lStrInputFilepath) == 'pdf')
            {
              $lArrPDFFileToAnalyze = PDFHandler::splitPDFPageByPage($lStrInputFilepath);

              $this->setCurrentStep(1);
              $this->setStepCount(count($lArrPDFFileToAnalyze));
              $this->setStatus('IN PROGRESS');
              $this->updateTask();

              $lArrResult = array();
              $lbFirst = true;
              $lIntCpt = 0;

              foreach ($lArrPDFFileToAnalyze as $lStrKey => $lStrFilepath)
              {
                // Convert Input file to PNG Image!
                $lStrCmd = "convert -density 300 -trim $lStrFilepath -quality 100 $lStrFilepath.png";
                exec($lStrCmd, $lArrOutputCmd, $this->_resultCode);
                if ($this->_resultCode===0) {
                    // OCR Processing!
                    $lObjOCR = new TesseractOCR("$lStrFilepath.png");
                    $lObjOCR->lang('fra');
                    $lStrOcrResult = $lObjOCR->run();
                    $lArrResult[] = $lStrOcrResult;
                    $lIntFlag = FILE_APPEND;
                    if ($lbFirst === true) {
                        $lIntFlag = 0;
                        $lbFirst = false;
                    }
                    //Write result to new file!
                    file_put_contents($this->getOutputTextFile(), $lStrOcrResult, $lIntFlag);

                    // Extract Images of PDF!
                    $lStrImagesPattern = $lStrOutputTxtDirectoryPath.'/'.$this->getInputFileUID().'-img_p'.str_pad(strval($lIntCpt+1),2,'0',STR_PAD_LEFT);

                    $lStrCmd = "pdfimages -png $lStrFilepath $lStrImagesPattern";
                    //return ($lStrCmd);
                    exec($lStrCmd, $lArrOutputCmd, $this->_resultCode);
                    //print_r($lArrOutputCmd);

                    $lIntCpt++;
                    $this->setCurrentStep($lIntCpt);
                    $this->updateTask();
                }
              }//end foreach

              $this->setStatus('FINISHED');
              $this->setEndTimesTamp(time());
              $this->updateTask();
              return $lStrOcrResult;
            }
            else {
              throw new ApplicationException(
                  'OCR-FILE-EXTENSION-NOT-SUPPORTED',
                  array('msg' =>
                    sprintf(
                      "Input file '%s' can't be managed because his extension which isn't supported by OCR engine!",
                      $lStrInputFilepath
                      )
                    )
              );
            }
          } else {
            throw new ApplicationException(
                  'OCR-INPUT-FILE-NOT-FOUND',
                  array('msg' =>
                    sprintf(
                      "Input file '%s' not found!",
                      $lStrInputFilepath
                      )
                    )
                );
          }
        }
    }//end launchOCRAnalysis()

    // INHERITED METHODS
    // =========================================================================

    /**
     * Returns a string with json encode specific param
     *
     * @return array(paramkey=>paramValue)  Array bout Specific Parameters values
     */
    protected function getTaskParametersJSON()
    {
      $lArrParam = array( 'currentStep' => $this->getCurrentStep(), 'totalStep' => $this->getStepCount());
      return json_encode($lArrParam);

    }//end getTaskParametersJSON()

    /**
     * Returns a string with json encode specific param
     *
     * @return array(paramkey=>paramValue)  Array bout Specific Parameters values
     */
    protected function loadTaskParametersFromJSON($pStrJSONParametersValues)
    {
      $lObjParam = json_decode($pStrJSONParametersValues);

      if(!is_null($lObjParam->currentStep)){
        $this->setCurrentStep($lObjParam->currentStep);
      }
      if(!is_null($lObjParam->totalStep)){
        $this->setStepCount($lObjParam->totalStep);
      }

    }//end loadTaskParametersFromJSON()
}
