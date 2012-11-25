<?php

define('__DIR_ROOT__', realpath(dirname(__FILE__) . '/..'));
define('__DIR_ETC__', __DIR_ROOT__.'/etc');
define('__DIR_LIBS__', __DIR_ROOT__.'/libs');
include_once(__DIR_ETC__ . "/cfg.php");
include_once(__DIR_LIBS__ . "/App.php");

$APP = new App($_CONF);

class Utils{
    static function randomFile($dir, $pattern = '*'){
        $files = glob("$dir/$pattern");
        shuffle($files);
        return array_pop($files);
    }

}
