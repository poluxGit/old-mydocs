<?php
/**
 * Import files class Definition
 *
 */
namespace MyGED\Process\Engines;

use MyGED\Exceptions\ApplicationException;
use MyGED\Core\Tasks\GenericTask;
use MyGED\Core\Tasks\Task;
use MyGED\Vault\Vault;
use MyGED\Application\Application as Application;
use MyGED\Core\FileSystem\FileSystem as FileSystem;

/**
 * Proceed Import of files from a Directory.
 *
 * @package MyGED
 * @subpackage Import
 */
class ImportFiles extends Task
{
    /**
     * Input Directory where files will be imported
     *
     * @var string
     * @access private
     */
    private $_inputDirectoryPath = null;

    /**
     * Input file pattern
     *
     * @var string
     * @access private
     */
    private $_inputFilePattern = null;

    /**
     * Total Files to manage
     *
     * @var  integer
     * @access private
     */
    private $_stepCount = 0;

    /**
     * Files managed
     *
     * @var  integer
     * @access private
     */
    private $_currentStep = 0;

    /**
     * Default Construtor
     *
     * @param string $pStrInputDirectoryPath    Imput Directory Path
     * @param string $pStrFilePatternToSelect   File pattern about files to import (example : *.jpg - to select JPEG files only)
     *
     */
    public function __construct($pStrInputDirectoryPath, $pStrFilePatternToSelect='*.*')
    {
        parent::__construct(null, Application::getAppDabaseObject());

        $this->setInputDirectoryPath($pStrInputDirectoryPath);
    }//end __construct()

    // Getters and Setters !
    // =========================================================================
    /**
     * Set InputDirectoryPath object value (_inputDirectoryPath)
     *
     * @param string  $pStrInputDirectoryPath
     */
    public function setInputDirectoryPath($pStrInputDirectoryPath)
    {
        $this->_inputDirectoryPath = $pStrInputDirectoryPath;
    }//end setInputFilePath()

    /**
     * Get InputDirectoryPath object value (_inputDirectoryPath)
     *
     * @return file
     */
    public function getInputDirectoryPath()
    {
        return $this->_inputDirectoryPath;
    }//end getInputDirectoryPath()

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
     * Instanciate a new ImportFiles Task
     *
     * @return string Task UID
     */
    public static function createNewImportTask()
    {
        // New OCR task init.!
        $lObjOCRTask = new ImportFiles(null);
        $lObjOCRTask->setTitle('ImportFiles task - default title.');
        $lObjOCRTask->setCreationTimestamp(time());
        $lObjOCRTask->setStatus('INIT');
        $lObjOCRTask->store();
        return $lObjOCRTask->getId();
    }//end createNewImportTask()

    /**
     * Launch Import Directory
     *
     * @param bool $pBoolRecursive   Recursive mode
     *
     * @return array(string)  Array containing fileUID created.
     */
    public function launchImportDirectory($pBoolRecursive=false)
    {
        $lArrFileUIDs = array();
        $lStrTaskUID = $this->getId();

        // Input Directory defined ?
        if (is_null($this->getInputDirectoryPath())) {
            throw new ApplicationException('IMPORT-DIRECTORY-INVALID-PATH-VALUE', array('msg'=>'Path to import must be defined before launching the task (ID:'.$lStrTaskUID.').'));
        }

        // Input Directory valid and reachable ?
        if (!is_dir($this->getInputDirectoryPath())) {
            throw new ApplicationException('IMPORT-DIRECTORY-INVALID', array('msg'=>'Path to import seems to be not valid or not reachable.Task aborted (ID:'.$lStrTaskUID.').'));
        }

        // Update Taks Object!
        $this->setTitle('ImportFiles task from "'.$this->getInputDirectoryPath().'".');
        $this->setStartTimeStamp(time());
        $this->setStatus('STARTED');
        $this->store();

        $this->addLogMessageAboutCurrentTask('Input Directory validated => "'.$this->getInputDirectoryPath().'".');

        // Listing all files from intpuDirectory !
        $lArrFilesToImport = FileSystem::getAllFilenamesOfDirectory($this->getInputDirectoryPath(), $pBoolRecursive);

        $liCptFileManaged = 0;

        $this->setCurrentStep($liCptFileManaged);
        $this->setStepCount(count($lArrFilesToImport));
        $this->setStatus('IN-PROGRESS');
        $this->store();

        // Import all files!
        foreach ($lArrFilesToImport as $lStrFile) {
            $lStrFileUIDs = Vault::storeFromFilepath($lStrFile);
            $this->addLogMessageAboutCurrentTask(sprintf("File %s imported sucessfully into Application Vault (id:%s).", $lStrFile, $lStrFileUIDs));
            $lArrFileUIDs[] = $lStrFileUIDs;
            $liCptFileManaged++;
            $this->setCurrentStep($liCptFileManaged);
            $this->store();
        }

        $this->setStatus('FINISHED');
        $this->setEndTimesTamp(time());
        $this->store();
        return $lArrFileUIDs;
    }//end launchImportDirectory()

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

        if (!is_null($lObjParam->currentStep)) {
            $this->setCurrentStep($lObjParam->currentStep);
        }
        if (!is_null($lObjParam->totalStep)) {
            $this->setStepCount($lObjParam->totalStep);
        }
    }//end loadTaskParametersFromJSON()
}
