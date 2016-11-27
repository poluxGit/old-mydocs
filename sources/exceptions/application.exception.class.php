<?php

/**
 * ApplicationException Exception Class File Definition
 *
 * @package MyGED
 * @subpackage CoreExceptions
 */

namespace MyGED\Exceptions;

/**
 * ApplicationException Exception Class Definition
 */
class ApplicationException extends \Exception
{

    /**
     * Exception Code
     *
     * @var string Exception internal code.
     * @access private
     */
    private $_sCodeException = null;

    /**
     * Exception Parameters
     *
     * @var array(mixed) Exception internal parameters.
     * @access private
     */
    private $_sExceptionParameters = null;

    /**
     * Default constructor
     *
     * @param string        $pStrCodeException
     * @param array(mixed)  $pArrParameters
     *
     * @return \MyGED\Core\Exceptions\ApplicationException
     */
    public function __construct($pStrCodeException, $pArrParameters=null)
    {
        if (!is_null($pArrParameters) && array_key_exists('msg', $pArrParameters)) {
            $lStrMessage = $pArrParameters['msg'];
        } else {
            $lStrMessage = "Message not defined!";
        }

        parent::__construct($pStrCodeException." - ".$lStrMessage);

        $this->_sCodeException       = $pStrCodeException;
        $this->_sExceptionParameters = $pArrParameters;
    }

    /**
     * Returns Applicaion Code Exception
     *
     * @return string
     */
    public function getAppCodeException()
    {
        return $this->_sCodeException;
    }

    /**
     * Returns Application Parameters of Exception
     *
     * @return array(mixed)
     */
    public function getParametersException()
    {
        return $this->_sExceptionParameters;
    }

    //@TODO Gestion dictionnaire de message d'erreur
}
