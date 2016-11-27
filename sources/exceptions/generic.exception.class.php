<?php

/**
 * GenericException Exception Class File Definition
 *
 * @package MyGED
 * @subpackage CoreExceptions
 */
namespace MyGED\Exceptions;

/**
 * GenericException Exception Class Definition
 *
 */
class GenericException extends ApplicationException
{
    public function __construct($pStrCodeException, $pArrParameters=null)
    {
        parent::__construct($pStrCodeException, $pArrParameters);
    }
}
