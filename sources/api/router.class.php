<?php
namespace MyGED\API;

/**
 * Application API Class file definition
 *
 * @package MyGED
 * @subpackage API_RESTful
 */
use MyGED\Core\API\API as API;
use MyGED\Core\Exceptions as Exceptions;
use MyGED\Business as Business;
use MyGED\Vault\Vault as VaultApp;
use MyGED\Vault\VaultDb as VaultAppDb;

use MyGED\Exceptions\APIException;
use MyGED\Exceptions\GenericException;
use MyGED\Process\Engines\OCRAnalysis as OCRAnalysis;

/**
 * Application API Class definition
 */
class Router extends API
{
    /**
     *  Default constructor
     *
     * @param array(mixed)  $request    HTTP Request Data
     * @param string        $origin     Server Name
     */
    public function __construct($request, $origin)
    {
        parent::__construct($request, $origin);

        // Specific Routes init!

        // API Document relatives Routes
        static::setSpecificRoute('GET', '#^document/[0-9A-Za-z\-]*/getmeta/#', 'cb_GET_DocumentGetMeta', 'document');
        static::setSpecificRoute('GET', '#^document/[0-9A-Za-z\-]*/getcat/#', 'cb_GET_DocumentGetCategories', 'document');
        static::setSpecificRoute('GET', '#^document/[0-9A-Za-z\-]*/gettiers/#', 'cb_GET_DocumentGetTiers', 'document');
        static::setSpecificRoute('POST', '#^document/[0-9A-Za-z\-]*/addtier/[0-9A-Za-z\-]*/#', 'cb_POST_DocumentAddTier', 'document');
        static::setSpecificRoute('POST', '#^document/[0-9A-Za-z\-]*/addcat/[0-9A-Za-z\-]*/#', 'cb_POST_DocumentAddCat', 'document');

        // Getting all Documents !
        //static::setSpecificRoute('GET','#^document/#', 'cb_GET_AllDocuments', 'document');

        // Create a new document
        static::setSpecificRoute('POST', '#^document/#', 'cb_POST_CreateDocument', 'document');


        // Update an existing Document Object!
        static::setSpecificRoute('PUT', '#^document/[0-9A-Za-z\-]*/#', 'cb_PUT_UpdateDocument', 'document');

        // Documents & Files ...
        static::setSpecificRoute('POST', '#^document/[0-9A-Za-z\-]*/file/#', 'cb_POST_DocumentFileAddFileAndLink', 'document');
        static::setSpecificRoute('POST', '#^document/[0-9A-Za-z\-]*/file/[0-9A-Za-z\-]*/#', 'cb_POST_DocumentAddLink', 'document');
        static::setSpecificRoute('DELETE', '#^document/[0-9A-Za-z\-]*/file/[0-9A-Za-z\-]*/#', 'cb_DELETE_DocumentFileDeleteLink', 'document');

        // API TypeDocument relatives Routes
        static::setSpecificRoute('GET', '#^typedocument/[0-9A-Za-z\-]*/getmeta/#', 'cb_GET_TypeDocumentGetMeta', 'document');
        static::setSpecificRoute('GET', '#^typedocument/[0-9A-Za-z\-]*/#', 'cb_GET_TypeDocument', 'document');
        static::setSpecificRoute('GET', '#^typedocument/#', 'cb_GET_AllTypeDocument', 'document');

        // API File relatives Routes
        static::setSpecificRoute('GET', '#^file/[0-9A-Za-z\-]*/#', 'cb_GET_getFileContent', 'file');
        static::setSpecificRoute('GET', '#^file/#', 'cb_GET_getAllFiles', 'file');
        static::setSpecificRoute('POST', '#^file/#', 'cb_POST_NewFile', 'document');
        static::setSpecificRoute('DELETE', '#^file/[0-9A-Za-z\-]*#', 'cb_DELETE_FileIntoDB', 'file');
        //static::setSpecificRoute('PUT', '#^file/#', 'cb_PUT_NewFile', 'document');

        // OCR Task management routes
        static::setSpecificRoute('POST', '#^tasks/ocr/[0-9A-Za-z\-]*/[0-9A-Za-z\-]*/#', 'cb_POST_LaunchOCRTask', 'document');
        static::setSpecificRoute('POST', '#^tasks/ocr/#', 'cb_POST_CreateOCRTask', 'document');


        // Generic Tasks routes
        static::setSpecificRoute('GET', '#^tasks/[0-9A-Za-z\-]*#', 'cb_GET_OCRTaskInfo', 'document');
    }

    /**
     * CallBack Document GetMeta in GET Request.
     *
     * @internal grab '#^document/[0-9A-Za-z\-]/addmeta/#' URI
     *
     * @return string Message
     */
    protected function cb_GET_DocumentGetMeta()
    {
        // Getting Data
        $lStrDocUID = array_shift($this->args);
        $lObjDoc = new Business\Document($lStrDocUID);
        return $this->_response($lObjDoc->getAllMetadataDataInArray(), 200);
    }//end cb_GET_DocumentGetMeta()

    /**
     * CallBack TypeDocument GetMeta in GET Request.
     *
     * @internal grab '#^typedocument/[0-9A-Za-z\-]*\/getmeta/#' URI
     *
     * @return string Message
     */
    protected function cb_GET_TypeDocumentGetMeta()
    {

        // Getting Data
        $lStrDocUID = array_shift($this->args);
        $lObjDoc = new Business\TypeDocument($lStrDocUID);

        return $this->_response($lObjDoc->getAllMetadataDataInArray(), 200);
    }//end cb_GET_TypeDocumentGetMeta()

    /**
     * CallBack TypeDocument attributes in GET Request.
     *
     * @internal grab '#^typedocument/[0-9A-Za-z\-]*#' URI
     *
     * @return string Message
     */
    protected function cb_GET_TypeDocument()
    {
        // Getting Data
      $lStrDocUID = array_shift($this->args);
        $lObjDoc = new Business\TypeDocument($lStrDocUID);

        return $this->_response($lObjDoc->getAllAttributeValueToArray(), 200);
    }//end cb_GET_TypeDocument()

    /**
     * CallBack All TypeDocument in GET Request.
     *
     * @internal grab '#^typedocument#' URI
     *
     * @return string Message
     */
    protected function cb_GET_AllTypeDocument()
    {
        // Getting Data

        $lObjDoc = new Business\TypeDocument(null);

        return $this->_response($lObjDoc->getAllClassItemsData(), 200);
    }//end cb_GET_TypeDocument()


    /**
     * CallBack Document AddTier in POST Request.
     *
     * @internal grab '#^document/[0-9A-Za-z\-]/addtier/[0-9A-Za-z\-]*#' URI
     *
     * @return string Message
     */
    protected function cb_POST_DocumentAddTier()
    {
        // Getting Data
        $lStrDocUID = array_shift($this->args);
        $lStrAddTier = array_shift($this->args);
        $lStrTierUid = array_shift($this->args);
        $lObjDoc = new Business\Document($lStrDocUID);

        return $this->_response($lObjDoc->addTierToDocument($lStrTierUid), 200);
    }//end cb_POST_DocumentAddTier()

    /**
     * CreateDocument
     */
    protected function cb_POST_CreateDocument()
    {

        // Doc creation !
        /* var MyGED\Business\Document */
        $lObjDoc = new Business\Document();

        $lObjDoc->setAttributeValue('doc_code', $_POST['doc_code']);
        $lObjDoc->setAttributeValue('tdoc_id', $_POST['tdoc_id']);
        $lObjDoc->setAttributeValue('doc_title', $_POST['doc_title']);
        $lObjDoc->setAttributeValue('doc_desc', $_POST['doc_desc']);
        $lObjDoc->setAttributeValue('doc_year', $_POST['doc_year']);
        $lObjDoc->setAttributeValue('doc_month', $_POST['doc_month']);
        $lObjDoc->setAttributeValue('doc_day', $_POST['doc_day']);

        $lObjDoc->store();
        $lStrDocID = $lObjDoc->getId();
        $lObjDoc->initializeMetadataFromTypeDoc();

        $lObjDoc = new Business\Document($lStrDocID);

        if (!empty($_POST['file_id'])) {
            $lObjDoc->linkFile($_POST['file_id']);
        }

        if (!empty($_POST['cat_id'])) {
            $lObjDoc->addCategorieToDocument($_POST['cat_id']);
        }

        if (!empty($_POST['tier_id'])) {
            $lObjDoc->addTierToDocument($_POST['tier_id']);
        }

        $lArrMetaDoc = Business\MetaDocument::getAllItemsDataFromDocument($lStrDocID);

        $lObjDoc = new Business\Document($lStrDocID);

        foreach ($lArrMetaDoc as $lArrMetaAttr) {
            $lStrMetaId = $lArrMetaAttr['meta_id'];
            if (array_key_exists($lStrMetaId, $_POST)) {
                $lObjDoc->setMetaValueForDocument($lStrMetaId, $_POST[$lStrMetaId]);
            }
        }

        return $this->_response($lStrDocID, 200);
        //return $this->_response($lObjDoc->addTierToDocument($lStrTierUid),200);
    }//end cb_POST_CreateDocument()

    /**
     * POST CallBack which creates a new OCR Task
     *
     * @internal grab '#^tasks/ocr/#' URI
     *
     * @return string NewOCRTask UID
     */
    protected function cb_POST_CreateOCRTask()
    {
        $lStrTaksUID = OCRAnalysis::createNewOCRTask();
        return $this->_response($lStrTaksUID, 200);
    }//end cb_POST_CreateOCRTask()

    /**
     * POST Method CallBack which launch an OCR Task
     *
     * @internal grab '#^tasks/ocr/[0-9A-Za-z\-]*\/[0-9A-Za-z\-]*#' URI
     *
     * @return string NewOCRTask UID
     */
    protected function cb_POST_LaunchOCRTask()
    {
        $lStrtmp = array_shift($this->args);
        $lStrTaskUID = array_shift($this->args);
        $lStrFileUID = array_shift($this->args);

        try {
            $lObjTask = new OCRAnalysis();
            $lBoolLoadOK = $lObjTask->loadDB($lStrTaskUID);
            $lObjTask->setInputFileUID($lStrFileUID);
            $lStrResult = $lObjTask->launchOCRAnalysis(false);
        } catch (\Exception $lObjException) {
            $lArrResponse = array('error' => $lObjException->getMessage());
            return $this->_response($lArrResponse, 500);
        }
        return $this->_responseSpecificType($lStrResult, 'text', 200);
    }//end cb_POST_LaunchOCRTask()


    /**
     * GET Method CallBack which launch an OCR Task
     *
     * @internal grab '#^tasks/[0-9A-Za-z\-]*\/#' URI
     *
     * @return string NewOCRTask UID
     */
    protected function cb_GET_OCRTaskInfo()
    {
        // Getting URI Data!
      $lStrTaskUID = array_shift($this->args);

        try {
            $lObjTask = new OCRAnalysis();
            $lBoolLoadOK = $lObjTask->loadTask($lStrTaskUID);
            $lArrResult = array(
          'title' => $lObjTask->getTitle(),
          'status'=> $lObjTask->getStatus(),
          'id'=> $lObjTask->getID(),
          'currentstep'=> $lObjTask->getCurrentStep(),
          'stepscount'=> $lObjTask->getStepCount()
        );
        } catch (\Exception $lObjException) {
            $lArrResponse = array('error' => $lObjException->getMessage());
            return $this->_response($lArrResponse, 500);
        }
        return $this->_response($lArrResult, 200);
    }//end cb_GET_OCRTaskInfo()

    /**
     * cb_GET_AllDocuments
     *
     * @return array(DocumentsValues)   All Documents data & metadata
     */
    protected function cb_GET_AllDocuments()
    {

        // Doc main data !
        $lObjDocTmp = new Business\Document();
        $lArrDocData = Business\Document::getAllClassItemsData();

        $funcGetCatIdOnly = function ($value) {
            return $value['cat_id'];
        };
        $funcGetTierIdOnly = function ($value) {
            return $value['tier_id'];
        };
        $funcGetFileIdOnly = function ($value) {
            return $value['file_id'];
        };

        // Add categories data!
        foreach ($lArrDocData as $lIntKey => $lArrDocAttr) {
            $lObjCat = new Business\Categorie();
            $lArrCat = $lObjCat->getCategoriesDataForDocument($lArrDocAttr['doc_id']);
            $lArrCatIdOnly = array_map($funcGetCatIdOnly, $lArrCat);
            $lArrDocData[$lIntKey]['cat_id']=implode('|', $lArrCatIdOnly);
        }

        // Add Tiers data!
        foreach ($lArrDocData as $lIntKey => $lArrDocAttr) {
            $lObjTier = new Business\Tier();
            $lArrTiers = $lObjTier->getTiersDataForDocument($lArrDocAttr['doc_id']);
            $lArrTierIdOnly = array_map($funcGetTierIdOnly, $lArrTiers);
            $lArrDocData[$lIntKey]['tier_id']=implode('|', $lArrTierIdOnly);
        }

        // Add Metadata of Document!
        foreach ($lArrDocData as $lIntKey => $lArrDocAttr) {
            $lObjDoc = new Business\Document($lArrDocAttr['doc_id']);
            $lArrMetaDoc = $lObjDoc->getAllMetadataDataInArray();

            if (!empty($lArrMetaDoc)) {
                foreach ($lArrMetaDoc as $lIntKeyMeta => $lArrMetaAttr) {
                    $lArrDocData[$lIntKey][$lArrMetaAttr['meta_id']] = $lArrMetaAttr['mdoc_value'];
                }
            }
        }

        // Add Files of Document!
        foreach ($lArrDocData as $lIntKey => $lArrDocAttr) {
            $lObjDoc = new Business\Document($lArrDocAttr['doc_id']);
            $lArrFilesDoc = $lObjDoc->getAllFilesOfDocument();
            $lArrFilesIdOnly = array_map($funcGetFileIdOnly, $lArrFilesDoc);

            if (!empty($lArrFilesIdOnly)) {
                $lArrDocData[$lIntKey]['file_id'] = implode('|', $lArrFilesIdOnly);
            }
        }

        return $this->_response($lArrDocData, 200);
    }//end cb_GET_AllDocuments()

    /**
     * CallBack Document AddTier in POST Request.
     *
     * @internal grab '#^document/[0-9A-Za-z\-]/addcat/[0-9A-Za-z\-]*#' URI
     *
     * @return string Message
     */
    protected function cb_POST_DocumentAddCat()
    {
        // Getting Data
        $lStrDocUID = array_shift($this->args);
        $lStrAddCat = array_shift($this->args);
        $lStrCatUid = array_shift($this->args);
        $lObjDoc = new Business\Document($lStrDocUID);

        return $this->_response($lObjDoc->addCategorieToDocument($lStrCatUid), 200);
    }//end cb_POST_DocumentAddCat()

    /**
     * CallBack Document getCat in GET Request.
     *
     * @internal grab '#^document/[0-9A-Za-z\-]/getcat/#' URI
     *
     * @return string Message
     */
    protected function cb_GET_DocumentGetCategories()
    {
        // Getting Data
        $lStrDocUID = array_shift($this->args);
        $lObjDoc = new Business\Categorie();
        return $this->_response($lObjDoc->getCategoriesDataForDocument($lStrDocUID), 200);
    }//end cb_GET_DocumentGetCategories()

    /**
     * CallBack Document getTier in GET Request.
     *
     * @internal grab '#^document/[0-9A-Za-z\-]/gettier/#' URI
     *
     * @return string Message
     */
    protected function cb_GET_DocumentGetTiers()
    {
        // Getting Data
        $lStrDocUID = array_shift($this->args);
        $lObjDoc = new Business\Tier();
        return $this->_response($lObjDoc->getTiersDataForDocument($lStrDocUID), 200);
    }//end cb_GET_DocumentGetTiers()


    /**
     * CallBack Document file in PUT Request
     *
     * Create and store a new File
     * @internal grab '#^file/#' URI
     *
     * @return string Message
     */
    protected function cb_PUT_NewFile()
    {
        // Getting Data
        //$lStrFileID = VaultApp::storeFromContent($this->fileContent,$this->fileName,$this->fileType);
        return $this->_response('PUT Method doesn\'t work to upload file ! Use POST method instead.', 500);
    }

    protected function cb_PUT_UpdateDocument()
    {
        $lStrDocUID = array_shift($this->args);

        $lObjDoc = new Business\Document($lStrDocUID);

        foreach ($this->args as $lStrKey => $lStrValue) {
            if ($lStrKey != 'metas' && $lStrKey != 'cats' && $lStrKey != 'tiers') {
                $lObjDoc->setAttributeValue($lStrKey, $lStrValue);
            } else {
                if ($lStrKey == 'tiers') {
                    $lArrTiers = mbsplit(',', $lStrValue);
                    foreach ($lArrTiers as $lStrTierKey) {
                        $lObjDoc->addTierToDocument($lStrTierKey);
                    }
                } elseif ($lStrKey == 'cats') {
                    $lArrCats = mbsplit(',', $lStrValue);
                    foreach ($lArrCats as $lStrCatKey) {
                        $lObjDoc->addCategorieToDocument($lStrCatKey);
                    }
                } elseif ($lStrKey == 'metas') {
                    // foreach ($lStrValue as $lStrKeyMeta => $lStrMetaValue) {
                    //     $lObjDoc->setMetaValueForDocument($lStrKeyMeta, $lStrMetaValue);
                    // }
                }
            }
        }

        $lObjDoc->store();

        return $this->_response($lObjDoc->getId(), 200);
    }

    /**
     * CallBack Document file in PUT Request
     *
     * Create and store a new File
     * @internal grab '#^file/#' URI
     * @deprecated
     * @return string Message
     */
    protected function cb_POST_NewFile()
    {
        $lArrFileData = null;

        // Getting Data
        if (is_array($_FILES) && count($_FILES) > 0) {
            $lArrFileData = array_shift($_FILES);
        } else {
            throw new APIException('API-HTTP-FILE-PARAM', 'Upload a new file.', 'POST', $_REQUEST);
        }

        $lStrFileID = VaultApp::storeFromFilepathWithFilename($lArrFileData['tmp_name'], $lArrFileData['name']);
        return $this->_response($lStrFileID, 200);
    }//end cb_POST_NewFile()

    /**
     * CallBack Document file in POST Request
     *
     * Create and store a new File and link it to the document
     *
     * @return string Message
     */
    protected function cb_POST_DocumentFileAddFileAndLink()
    {
        // Getting Data

        // File Storage !
        $lArrFileUploaded = array_shift($_FILES);
        $lStrFileID = VaultApp::storeFromContent(file_get_contents($lArrFileUploaded['tmp_name']), $lArrFileUploaded['name'], $lArrFileUploaded['type']);

        $lStrDocUID = array_shift($this->args);
        $lObjDoc = new Business\Document($lStrDocUID);
        $lObjDoc->linkFile($lStrFileID);
        return $this->_response($lStrFileID, 200);
    }//end cb_POST_DocumentFileAddFileAndLink()


    /**
     * CallBack Document file in POST Request
     *
     * Create and store a new File and link it to the document
     *
     * @return string Message
     */
    protected function cb_POST_DocumentAddLink()
    {
        // Getting Data

        $lStrDocUID  = array_shift($this->args);
        $lStrFileUID = array_shift($this->args);
        $lObjDoc = new Business\Document($lStrDocUID);

        return $this->_response($lObjDoc->linkFile($lStrFileUID), 200);
    }//end cb_POST_DocumentAddLink()


    /**
     * CallBack Document file in DELETE Request
     *
     * Create and store a new File and link it to the document
     *
     * @return string Message
     */
    protected function cb_DELETE_DocumentFileDeleteLink()
    {
        // Getting Data

        $lStrDocUID  = array_shift($this->args);
        $lStrFileUID = array_shift($this->args);
        $lObjDoc = new Business\Document($lStrDocUID);

        return $this->_response($lObjDoc->deleteLinkFile($lStrFileUID), 200);
    }//end cb_POST_DocumentAddLink()




    /**
     * CallBack Document file in PUT Request
     *
     * Create and store a new File
     * @internal grab '#^file/#' URI
     *
     * @return string Message
     */
    protected function cb_GET_getFileContent()
    {
        // Getting Data
        $lStrDocUID = array_shift($this->args);
        $lStrFileContent = VaultApp::getFileContentByID($lStrDocUID);
        $lStrContentType = VaultAppDb::getFileMimeType($lStrDocUID);
        return $this->_responseSpecificType($lStrFileContent, $lStrContentType);
    }


    /**
     * CallBack  file in GET Request
     *
     * Create and store a new File
     * @internal grab '#^file/#' URI
     *
     * @return string Message
     */
    protected function cb_GET_getAllFiles()
    {
        // Getting Data
        $lArrFiles = VaultAppDb::getAllFiles();
        return $this->_response($lArrFiles, 200);
    }//end cb_GET_getAllFiles

    /**
     * CallBack  file in DELETE Request
     *
     * Create and store a new File
     * @internal grab '#^file/#' URI
     *
     * @return string Message
     */
    protected function cb_DELETE_FileIntoDB()
    {
        // Getting Data
        $lStrFileID = array_shift($this->args);
        $lBoolResult = VaultAppDb::deleteFile($lStrFileID);
        return $this->_response($lBoolResult, 200);
    }//end cb_DELETE_FileIntoDB

    /**
     * Update fields on Business Object concerned by request.
     *
     * @param \MyGED\Core\AbstractDBObject     $pObjToUpdate           Object to update
     * @param array(fieldname => fieldvalue)   $pArrFieldsToUpdate     Fieldsname to update
     *
     * @throws Exceptions\GenericException     if field not valid for current type of Object
     */
    private function setItemAttributesFromCurrentRequestArgs($pObjToUpdate, $pArrFieldsToUpdate)
    {
        if ($pObjToUpdate instanceof \MyGED\Core\AbstractDBObject) {
            foreach ($pArrFieldsToUpdate as $lStrFieldAttributeName => $lStrFieldAttributeValue) {
                if ($pObjToUpdate->isValidFieldForClass($lStrFieldAttributeName)) {
                    $pObjToUpdate->setAttributeValue($lStrFieldAttributeName, $lStrFieldAttributeValue);
                } else {
                    $lArrOptions = array(
                       'msg' => sprintf(
                                   "Fieldname '%s' isn't valid for Object of type '%s'.",
                                   $lStrFieldAttributeName,
                                   $this->endpoint
                               )
                       );
                    throw new Exceptions\GenericException('API_BUSINESS_FIELDNAME_INVALID', $lArrOptions);
                }
            }
        }
    }//end setItemAttributesFromCurrentRequestArgs()
}//end class
