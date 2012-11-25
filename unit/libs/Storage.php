<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/../../bootstrap.php");

include_once("Storage.php");

class Unit_Libs_Storage extends PHPUnit_Framework_TestCase{
    public function testCreateAndDelete(){

        $sto = new Storage(__DIR_TMP__, __DIR_IMG__, __DIR_THMB__);

        $filename = 'abc';
        $filepath = $sto->create($filename);
        $this->assertEquals($filename, basename($filepath));
        $this->assertTrue($sto->exist($filepath));
        $this->assertTrue($sto->delete($filepath));
        $this->assertTrue(! $sto->exist($filepath));
 
        $filename = strrev('abc');
        $filepath = $sto->create($filename, 'images');
        $this->assertEquals($filename, basename($filepath));
        $this->assertTrue($sto->exist($filepath));
        $this->assertTrue($sto->delete($filepath));
        $this->assertTrue(! $sto->exist($filepath));

        $filename = strtoupper('abc');
        $filepath = $sto->create($filename, 'thumbs');
        $this->assertEquals($filename, basename($filepath));
        $this->assertTrue($sto->exist($filepath));
        $this->assertTrue($sto->delete($filepath));
        $this->assertTrue(! $sto->exist($filepath));

    }

    public function testMove(){
        $sto = new Storage(__DIR_TMP__, __DIR_IMG__, __DIR_THMB__);

        $filename = 'abc';
        $filepath = $sto->create($filename, 'pendings');
        $this->assertEquals($filename, basename($filepath));
        $this->assertTrue($sto->exist($filepath));
        $this->assertTrue($sto->move($filepath, $filename, 'images'));
        $this->assertTrue(! $sto->exist($filepath));
        $this->assertTrue($sto->exist($filename, 'images'));

        $filepath = $sto->create();
        $filename = basename($filepath);
        $this->assertTrue($sto->exist($filepath));
        $this->assertTrue($sto->move($filepath, $filename, 'images'));
        $this->assertTrue(! $sto->exist($filepath));
        $this->assertTrue($sto->exist($filename, 'images'));
    }
}
