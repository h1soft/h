<?php

/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hmvc\Web\Exception;

class Error extends \hmvc\Web\Controller {
    
    public $appName;
    public $controllerName;
    public $actionName;


    public function indexAction() {
        
    }

    public function notfoundAction() {
        echo <<<EOF
		<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL  was not found on this server.</p>
<p>Additionally, a 404 Not Found
error was encountered while trying to use an ErrorDocument to handle the request.</p>
EOF;
        if (defined('DEBUG')) {
            echo <<<EOF
            <div style="border:solid 1px #ccc;">
                <div style="line-height:30px;font-weight:bold;background-color: #F3B516;">Debug</div>
                AppName : {$this->appName}<br/>
                ControllerName : {$this->controllerName}<br/>
                Action : {$this->actionName}<br/>
            </div>
EOF;
        }
        echo <<<EOF
</body></html>
EOF;
    }

    public function errorAction() {
        echo <<<EOF
		
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>500 Internal Server Error</title>
</head><body>
<h1>Internal Server Error</h1>
<p>The server encountered an internal error or
misconfiguration and was unable to complete
your request.</p>
<p>Please contact the server administrator,
 == and inform them of the time the error occurred,
and anything you might have done that may have
caused the error.</p>
<p>More information about this error may be available
in the server error log.</p>
<p>Additionally, a 404 Not Found
error was encountered while trying to use an ErrorDocument to handle the request.</p>
</body></html>

EOF;
    }

    public function perimAction() {
        
    }

}
