<?php


use \PDSL\Launcher;


define('WEB_SERVER_HOST','localhost');
define('WEB_SERVER_PORT','8080');
define('WEB_SERVER_DOCROOT','./tests/_files/');

Launcher::staticRun();
