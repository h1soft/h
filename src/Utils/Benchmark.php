<?php

/**
 * 效率测试工具
 */

namespace H1Soft\H\Utils;

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
