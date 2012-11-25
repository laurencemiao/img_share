<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/../boot.php");

/*
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
 */
class Unit_Api_Image extends PHPUnit_Framework_TestCase{

    // prevent PHPUnit from damaging APP object
    protected $backupGlobalsBlacklist = array('APP');
    private $_conf = array();
    private $_imgs = array();

    function setup(){
        global $APP;

        $server_conf = $APP->getConfig('server');
        $this->_conf['image_api_url'] = $server_conf['api']['image'];
        $this->_conf['upload_api_url'] = $server_conf['api']['upload'];
        $this->_conf['sample_img_dir'] = $server_conf['sampledir'];
        $this->_conf['tmp_dir'] = $server_conf['tmpdir'];

        $up_api = $this->_conf['upload_api_url'];
        $img_api = $this->_conf['image_api_url'];

        $post = array(
            "up_file1"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
            "up_file2"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
            "up_file3"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
        );
        $result = Curl::getJson($up_api, $post, 'POST.FILE');

        $this->assertEquals(200, $result['code']);

        foreach($result['body'] as $file){
            $file['title'] = "test_file_".$file['id'];
            $file['desc'] = '';
            $result = Curl::getJson($img_api, $file);
            $this->assertEquals(204, $result['code']);

            $url = $img_api . "?id={$file['id']}";
            $result = Curl::getJson($url);

            $this->assertEquals(200, $result['code']);
            $this->_imgs[] = $file['id'];
        }
    }

    public function testGenThumb(){
        $up_api = $this->_conf['upload_api_url'];
        $img_api = $this->_conf['image_api_url'];
        $tmp_dir = $this->_conf['tmp_dir'];

        foreach($this->_imgs as $id){
            $result = Curl::getJson("$img_api?id=$id");
            $this->assertEquals(200, $result['code']);

            $image = array_pop($result['body']);
            $this->assertTrue(empty($image['thumb_url']));

            $result = Curl::get($image['url']);
            $this->assertEquals($result['info']['http_code'], 200);

            $tmp_file = tempnam($tmp_dir, 'unit_img_');
            file_put_contents($tmp_file, $result['body']);

            $img = new Image($tmp_file);
            $thumb_file = tempnam($tmp_dir, 'unit_thmb_');
            $thumb = $img->createThumb($thumb_file, 100);
            $post = array(
                "up_file"=>"@" . $thumb_file,
            );
            $result = Curl::getJson($up_api, $post, "POST.FILE");
            $this->assertEquals(200, $result['code']);

            $file = array_shift($result['body']);
            $file['thumb_id'] = $file['id'];
            $file['id'] = $id;
            $result = Curl::getJson($img_api, $file, 'PUT');
            $this->assertEquals(204, $result['code']);

            unlink($tmp_file);
            unlink($thumb_file);

        }

    }

    public function teardown(){
        $img_api = $this->_conf['image_api_url'];

        foreach($this->_imgs as $id){
            $result = Curl::getJson("$img_api?id=$id");
            // some testcases will delete gallery images totally
            if($result['code'] != 200)
                continue;
            $file = array('id' => $id);
            $result = Curl::getJson($img_api, $file, 'DELETE');

            $this->assertEquals(204, $result['code']);

            $url = "$img_api?id={$file['id']}";
            $result = Curl::getJson($url);
            $this->assertEquals(404, $result['code']);
        }
    }

}
