<?php
/**
 * Script Title
 *
 * Script Description
 *
 * @package Package
 * @subpackage Package
 * @author polux@poluxfr.org
 *
 */
namespace MyGED\Core\FileSystem;

use MyGED\Core\FileSystem\FileSystem as Fs;
use MyGED\Core as Core;
use TesseractOCR;
use MyGED\Exceptions\GenericException;
use MyGED\Exceptions\ApplicationException as ApplicationException;

/**
 * PDF Handler Toolkit
 *
 * @package FileSystem
 * @author polux
 */
class PDFHandler
{
    /**
     * PDF filepath
     *
     * @var file
     * @access private
     */
    private $_pdfFilepath = null;

    /**
     * Default Constructor
     *
     * @param filepath  $pStrFilepath   Filepath of pdf file.
     * @internal No controls about file.
     *
     * @return PDFHandler
     */
    public function __construct($pStrFilepath)
    {
        if (!empty($pStrFilepath) && file_exists($pStrFilepath)) {
            $this->_pdfFilepath = $pStrFilepath;
        } else {
            throw new ApplicationException(
            'PDF-SOURCEFILE-NOT-VALID',
             array(
               'msg'=>sprintf("File '%s' not valid.", $pStrFilepath))
             );
        }
    }//end __construct()

    /**
     * Separate current PDF file into images (one by page)
     *
     * @throws ApplicationException
     * @param string $pStrOutputFilePattern Output file pattern with extension (pdf)
     *
     * @return ReturnType ReturnDesc
     * @todo  Rajouter gestion des extensions de fichier
     */
    private function _splitPDFbyPage($pStrOutputFilePattern, $pIntStartIdx=null, $pIntEndIdx=null)
    {
        try {
            $lIntReturnExec = null;
            $lStrCommand = "pdfseparate ".$this->_pdfFilepath;
            if ($pIntStartIdx !== null && $pIntEndIdx !== null && $pIntStartIdx<=$pIntEndIdx) {
                $lStrCommand .= " -f $pIntStartIdx -l $pIntEndIdx ";
            }
            $lStrCommand .= " $pStrOutputFilePattern";
            exec($lStrCommand, $lStrOutputCmd, $lIntReturnExec);
            return $lIntReturnExec;
        } catch (\Exception $e) {
            throw new ApplicationException(
              'PDF-SPLIT-UNKNOW-ERR',
              array('msg' =>
                sprintf(
                  "PDF Separate command had some issues (Cmd:'%s' | ExMessage: '%s')",
                  $lStrCommand,
                  $e->getMessage()
                )
              )
            );
        }
    }//end _splitPDFbyPage()

    /**
     * Return Page count of Current PDF File
     *
     * @uses pdfinfo
     * @return int  Number Of Pages about current PDF file
     */
    public function getPagesCount()
    {
        try {
            $lStrCommand = "pdfinfo ".$this->_pdfFilepath.' | grep "Pages"';
            exec($lStrCommand, $lStrOutputCmd, $lIntReturnExec);
            return intval(str_replace('Pages:', '', (string)$lStrOutputCmd[0]));
        } catch (\Exception $e) {
            throw new ApplicationException(
            'PDF-INFO-UNKNOW-ERR',
            array('msg' =>
              sprintf(
                "PDF info command had some issues (Cmd:'%s' | ExMessage: '%s')",
                $lStrCommand,
                $e->getMessage()
              )
            )
          );
        }
    }//end getPagesCount()

    /**
     * Return Metadata count of Current PDF File
     *
     * @uses pdfinfo
     * @return int  Number Of Pages about current PDF file
     */
    public function getMetaCount()
    {
        try {
            $lStrCommand = "pdfinfo ".$this->_pdfFilepath.' | wc -l';
            exec($lStrCommand, $lStrOutputCmd, $lIntReturnExec);
            return intval((string)$lStrOutputCmd[0]);
        } catch (\Exception $e) {
            throw new ApplicationException(
            'PDF-INFO-UNKNOW-ERR',
            array('msg' =>
              sprintf(
                "PDF info command had some issues (Cmd:'%s' | ExMessage: '%s')",
                $lStrCommand,
                $e->getMessage()
              )
            )
          );
        }
    }//end getMetaCount()

    /**
     * Return all Metadata values defined for Current PDF File
     *
     * @uses pdfinfo
     * @return array(string)  Metatdata values (organized by key)=> array(metakey=>metavalue)
     */
    public function getAllMetaValues()
    {
        try {
            $lStrCommand = "pdfinfo ".$this->_pdfFilepath;
            exec($lStrCommand, $lStrOutputCmd, $lIntReturnExec);
            $lArrMetaValues = array();

            if ($lIntReturnExec == 0) {
                foreach ($lStrOutputCmd as $key => $line) {
                    $lStrTmp = mb_split(':', $line);
                    if (count($lStrTmp) > 1) {
                        $lStrKey = array_shift($lStrTmp);
                        $lArrMetaValues[$lStrKey] = join(':', $lStrTmp);
                    }
                }
            }
            return $lArrMetaValues;
        } catch (\Exception $e) {
            throw new ApplicationException(
            'PDF-INFO-UNKNOW-ERR',
            array('msg' =>
              sprintf(
                "PDF info command had some issues (Cmd:'%s' | ExMessage: '%s')",
                $lStrCommand,
                $e->getMessage()
              )
            )
          );
        }
    }//end getMetaCount()

    /**
     * Launch an OCR Analysis on PDF
     *
     * @param file    $pStrInputPDFFilepath   File PAth of PDF to OCR analyze
     * @param intger  $pIntPageNumber         Index of page of PDF to Analyze
     *
     * @return array(string) OCR results as array of string
     */
    public function launchOCRAnalysisByPage($pStrInputPDFFilepath, $pIntPageNumber)
    {
        if (file_exists($pStrInputPDFFilepath)) {
            $lArrPDFFileToAnalyze = self::splitPDFPageByPage($pStrInputPDFFilepath);
            $lArrResult = array();

            if ($pIntPageNumber > count($lArrPDFFileToAnalyze) && $pIntPageNumber>0) {
                throw new GenericException(
                'PDF-IDX-PAGE-NOT-EXISTS',
                array(
                  'msg' => sprintf(
                            'Page Number "%s" in parameters is upper than total page count "%s" of PDF file.',
                            $pIntPageNumber,
                            count($lArrPDFFileToAnalyze)
                          )
                      )
              );
            } else {
                # OCR Analysis
              $lStrFilepath = $lArrPDFFileToAnalyze[$pIntPageNumber];
                $lStrCmd = "convert -density 300 -trim $lStrFilepath -quality 100 $lStrFilepath.png";
                exec($lStrCmd, $lArrOutpuCmd, $lIntRetVal);
                if ($lIntRetVal===0) {
                    $lObjOCR = new TesseractOCR("$lStrFilepath.png");
                    $lObjOCR->lang('fra');
                    $lArrResult[] = $lObjOCR->run();
                }
                return $lArrResult;
            }
        } else {
            throw new ApplicationException(
            'PDF-OCR-INPUT-PDFFILE-NOT-FOUND',
            array('msg' =>
              sprintf(
                "PDF file '%s' not found!",
                $pStrInputPDFFilepath
              )
            )
          );
        }
    }//end launchOCRAnalysisByPage()

    /**
     * Split a PDF into X PDF (where X are the pages count).
     *
     * @param file  $pStrInputPDFFilepath Filepath of PDF to split (no writing on this file).
     *
     * @return array(file)  PDF Files collection resulting of splitting.
     */
    public static function splitPDFPageByPage($pStrInputPDFFilepath)
    {
        if (file_exists($pStrInputPDFFilepath)) {
            $lObjPDFFile = new PDFHandler($pStrInputPDFFilepath);

            // For each pages !
            $lIntPagecount = $lObjPDFFile->getPagesCount();
            //  print_r($lIntPagecount);
            $lIntCpt = 1;
            $lArrPDFFiles = array();

            while ($lIntCpt<=$lIntPagecount) {
                $lStrTemp = Fs::getTempFilename();
                $lStrCodeRetour = $lObjPDFFile->_splitPDFbyPage($lStrTemp, $lIntCpt, $lIntCpt);

                if (intval($lStrCodeRetour) === 0) {
                    array_push($lArrPDFFiles, $lStrTemp);
                }
                $lIntCpt++;
            }
            //print_r($lArrPDFFiles);
            return $lArrPDFFiles;
        } else {
            throw new ApplicationException(
              'PDF-SPLIT-PDFFILE-NOT-FOUND',
              array('msg' =>
                sprintf(
                  "PDF file '%s' not found!",
                  $pStrInputPDFFilepath
                )
              )
            );
        }
    }//end splitPDFPageByPage()

    /**
     * Launch an OCR Analysis on PDF
     *
     * @param file  $pStrInputPDFFilepath   File PAth of PDF to OCR analyze
     *
     * @return array(string) OCR results as array of string
     */
    public static function launchOCRAnalysis($pStrInputPDFFilepath)
    {
        if (file_exists($pStrInputPDFFilepath)) {
            $lArrPDFFileToAnalyze = self::splitPDFPageByPage($pStrInputPDFFilepath);
            $lArrResult = array();
            foreach ($lArrPDFFileToAnalyze as $lStrKey => $lStrFilepath) {
                $lStrCmd = "convert -density 300 -trim $lStrFilepath -quality 100 $lStrFilepath.png";
                exec($lStrCmd, $lArrOutpuCmd, $lIntRetVal);
                if ($lIntRetVal===0) {
                    $lObjOCR = new TesseractOCR("$lStrFilepath.png");
                    $lObjOCR->lang('fra');
                    $lArrResult[] = $lObjOCR->run();
                }
            }

            return $lArrResult;
        } else {
            throw new ApplicationException(
            'PDF-OCR-INPUT-PDFFILE-NOT-FOUND',
            array('msg' =>
              sprintf(
                "PDF file '%s' not found!",
                $pStrInputPDFFilepath
              )
            )
          );
        }
    }
}//end class
;
