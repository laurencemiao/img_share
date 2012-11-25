<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/boot.php");

class Unit_Api_doDeleteAll extends PHPUnit_Framework_TestCase{

    // prevent PHPUnit from damaging APP object
    protected $backupGlobalsBlacklist = array('APP');
    private $_conf = array();

    function setup(){
        global $APP;

        $server_conf = $APP->getConfig('server');
        $this->_conf['image_api_url'] = $server_conf['api']['image'];
        $this->_conf['upload_api_url'] = $server_conf['api']['upload'];
        $this->_conf['sample_img_dir'] = $server_conf['sampledir'];

    }

    public function testDeleteAll(){
        $up_api = $this->_conf['upload_api_url'];
        $img_api = $this->_conf['image_api_url'];

        while(1){
            $result = Curl::getJson($img_api);
            $this->assertEquals(200, $result['code']);

            if(count($result['body']) == 0)
                break;

            foreach($result['body'] as $file){
                $result = Curl::getJson($img_api, $file, 'DELETE');
                $this->assertEquals(204, $result['code']);

                $url = $img_api . "?id={$file['id']}";
                $result = Curl::getJson($url);

                $this->assertEquals(404, $result['code']);
            }
        }
    }

}

