<?php

$loader = new Reticen8\Phalcon\Autoload\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->setDirectories(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libraryDir
    )
)->register();
