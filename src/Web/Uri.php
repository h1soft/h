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

namespace H1Soft\H\Web;

/**
 * Description of Uri
 *
 * @author Administrator
 */
class Uri {

    static public function base64_url_encode($string = NULL) {
        return strtr(base64_encode($string), '+/=', '-_~');
    }

    static public function base64_url_decode($string = NULL) {
        return base64_decode(strtr($string, '-_~', '+/='));
    }

}
