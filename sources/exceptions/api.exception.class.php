<?php

/**
 * APIException Exception Class File Definition
 *
 * @package MyGED
 * @subpackage Exceptions
 */
namespace MyGED\Exceptions;

use MyGED\Exceptions\APIException;

/**
 * APIException Exception Class Definition
 *
 * @author polux <polux@poluxfr.org>
 */
class APIException extends ApplicationException
{

    /**
     * Default Constructor about APIException instance
     *
     * @param string $pStrCodeException       Exception Code
     * @param string $pStrRouteName           Route name concerned
     * @param string $pStrMethod              Request Method
     * @param string $pArrParametersRequest   All HTTP Request Parameters (i.e : $_GLOBALS)
     *
     */
    public function __construct($pStrCodeException, $pStrRouteName, $pStrMethod, $pArrParametersRequest)
    {
        // building specific Exception message !
        $lStrMessage = sprintf(
          'API Exception "%s"- Error occured during calling API RestFULL endpoint. (Route: "%s" |Method: "%s" |RequestParam: "%s").',
          $pStrCodeException,
          $pStrRouteName,
          $pStrMethod,
          print_r($pArrParametersRequest, true)
        );
        parent::__construct($pStrCodeException, array(msg=> $lStrMessage));
    }
}
