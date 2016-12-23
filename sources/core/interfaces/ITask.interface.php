<?php

/**
 * Task Interface file definition
 *
 * @author polux <polux@poluxfr.org>
 * @package MyGED
 * @subpackage Tasks
 */

namespace MyGED\Core\Interfaces;

/**
 * ITask interface.
 *
 * Interface defintion about a Task.
 */
interface ITask
{
    /**
     * Check Parameters about this Tasks.
     *
     * @param array() $pArraySpecificParams Parameters.
     *
     * @return boolean TRUE if OK
     */
    public function checkTaskParameters($pArraySpecificParams);

    /**
     *
     */
    public function prepareTask();

    /**
     *
     */
    public function startTask();

    /**
     *
     */
    public function endTask();

}//end interface
