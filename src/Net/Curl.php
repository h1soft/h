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
 * Curl Request
 *
 * @author h@h1soft.net
 */
class Curl {

    public function request($url, array $options = NULL) {
        $ch = curl_init($url);

        $defaults = array(
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 5,
        );

        // Connection options override defaults if given
        curl_setopt_array($ch, (array) $options + $defaults);

        // Create a response object
        $object = new stdClass;

        // Get additional request info
        $object->response = curl_exec($ch);
        $object->error_code = curl_errno($ch);
        $object->error = curl_error($ch);
        $object->info = curl_getinfo($ch);

        curl_close($ch);

        return $object;
    }

}
