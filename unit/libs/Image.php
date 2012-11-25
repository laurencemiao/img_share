<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/../../bootstrap.php");

include_once("Storage.php");
include_once("Image.php");

class Unit_Libs_Image extends PHPUnit_Framework_TestCase{
    public function testThumb(){

        $img = new Image(__DIR_TEST_IMG__);

        $filename = 'abc.jpg';
        $filepath = __DIR_TMP__ . "/$filename";

        $img_thumb = $img->createThumb($filepath, 50);

        $this->assertEquals(50, $img_thumb->getWidth());
    }
}
