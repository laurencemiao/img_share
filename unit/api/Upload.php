<?php

define('__DIR_CUR__', dirname(__FILE__));
include_once(__DIR_CUR__ . "/../boot.php");


class Unit_Api_Upload extends PHPUnit_Framework_TestCase{

    // prevent PHPUnit from damaging APP object
    protected $backupGlobalsBlacklist = array('APP');
    private $_conf = array();
    private $_store = null;

    function setup(){
        global $APP;

        $server_conf = $APP->getConfig('server');
        $this->_conf['upload_api_url'] = $server_conf['api']['upload'];
        $this->_conf['sample_img_dir'] = $server_conf['sampledir'];
        $this->_conf['tmp_dir'] = $server_conf['tmpdir'];

        $this->_store = $APP->getStorageInstance();

    }

    public function testUpload(){
        $up_api = $this->_conf['upload_api_url'];

        $post = array(
            "up_file"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
        );
        $result = Curl::getJson($up_api, $post, 'POST.FILE');

        $this->assertEquals(200, $result['code']);

        foreach($result['body'] as $file){
            $id = $file['id'];
            $this->assertTrue(! empty($id));
            $this->assertTrue($this->_store->exist($id, 'pending'));
        }
    }

    public function testMultifileUpload(){
        $up_api = $this->_conf['upload_api_url'];

        $post = array(
            "up_file1"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
            "up_file2"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
            "up_file3"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
        );
        $result = Curl::getJson($up_api, $post, 'POST.FILE');

        $this->assertEquals(200, $result['code']);


        foreach($result['body'] as $file){
            $id = $file['id'];
            $this->assertTrue(! empty($id));
            $this->assertTrue($this->_store->exist($id, 'pending'));
        }
    }

}

