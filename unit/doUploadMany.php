<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/boot.php");

class Unit_doUploadMany extends PHPUnit_Framework_TestCase{

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

    public function testUploadMany(){
        $up_api = $this->_conf['upload_api_url'];
        $img_api = $this->_conf['image_api_url'];

        for($i = 0; $i<8; $i++){
            $post = array();
            for($j = 0; $j<3; $j++){
                $post["up_file_{$i}_{$j}"] = "@" . Utils::randomFile($this->_conf['sample_img_dir']);
            }
            $result = Curl::getJson($up_api, $post, 'POST.FILE');
            $this->assertEquals(200, $result['code']);

            foreach($result['body'] as $k => $file){
                $file['title'] = __FUNCTION__ . "_{$i}_{$k}";
                $file['desc'] = '';
                $result = Curl::getJson($img_api, $file);
                $this->assertEquals(204, $result['code']);

                $url = $img_api . "?id={$file['id']}";
                $result = Curl::getJson($url);

                $this->assertEquals(200, $result['code']);
            }

        }

        $result = Curl::getJson($img_api . "?start=0&count=24");

        $this->assertEquals(200, $result['code']);

        $this->assertEquals(24, count($result['body']));
    }

}

