<?php


use \PDSL\Launcher;

$options = array(
    'host'=>'localhost',
    'port'=>'8080',
    'docRoot'=>'./tests/_files/'
);

// set over constructor
$launcher = new Launcher($options);

// alternative over methode
$launcher = new Launcher();
$launcher->setOption($options);


// launght
$launcher->start();
