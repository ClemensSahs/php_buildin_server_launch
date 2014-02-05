<?php


use \PDSL\Launcher;

$options = array(
    'phpunitxml'=>'./tests/phpunit.xml'
);

// set over constructor
$launcher = new Launcher($options);

// alternative over methode
$launcher = new Launcher();
$launcher->setOption($options);

// launght
$launcher->start();
