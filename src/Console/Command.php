<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Command
 *
 * @author Administrator
 */
class Command {

    function colorize($text, $color, $bold = FALSE) {
        // Standard CLI colors
        $colors = array_flip(array(30 => 'gray', 'red', 'green', 'yellow', 'blue', 'purple', 'cyan', 'white', 'black'));

        // Escape string with color information
        return"\033[" . ($bold ? '1' : '0') . ';' . $colors[$color] . "m$text\033[0m";
    }

}
