<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 效率测试工具
 */

namespace hmvc\Utils;

class Benchmark {

    /**
     * Start the timer
     */
    static public function timer_start($timeName = "execution_time") {
        $stimer = explode(' ', microtime());
        $GLOBALS['timeCounter'][$timeName] = $stimer[1] + $stimer[0];
    }

    /**
     * Get the time passed
     */
    static public function timer($timeName = "execution_time", $precision = 6) {
        $etimer = explode(' ', microtime());
        $timeElapsed = $etimer[1] + $etimer[0] - $GLOBALS['timeCounter'][$timeName];
        return substr($timeElapsed, 0, $precision);
    }

    function memory_usage_start($memName = "execution_time") {
        return $GLOBALS['memoryCounter'][$memName] = memory_get_usage();
    }

    /**
     * Get the memory used
     */
    function memory_usage($memName = "execution_time", $byte_format = true) {
        $totMem = memory_get_usage() - $GLOBALS['memoryCounter'][$memName];
        return $byte_format ? byte_format($totMem) : $totMem;
    }

}
