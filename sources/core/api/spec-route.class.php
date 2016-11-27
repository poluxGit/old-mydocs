<?php

namespace MyGED\Core\API;

/**
 * APIRoute Class File definition
 *
 * @package MyGED
 * @subpackage API_RESTful
 */

/**
 * APIRoute Class Definition
 *
 * @abstract
 */
class APIRoute
{
    private $_sURIRequested = '';
    /**
     * Constructor: __construct
     *
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($pStrURI)
    {
        $this->_sURIRequested = $pStrURI;
    }
}
