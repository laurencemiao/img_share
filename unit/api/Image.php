<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/../boot.php");

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

    public function testCreateOne(){
        $up_api = $this->_conf['upload_api_url'];
        $img_api = $this->_conf['image_api_url'];

        $post = array(
            "up_file"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
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
        }
    }

    public function testDeleteOne(){
        $up_api = $this->_conf['upload_api_url'];
        $img_api = $this->_conf['image_api_url'];

        $post = array(
            "up_file"=>"@" . Utils::randomFile($this->_conf['sample_img_dir']),
        );

        $result = Curl::getJson($up_api, $post, 'POST.FILE');
        $this->assertEquals(200, $result['code']);

        $this->assertTrue(is_array($result['body']));
        $file = array_pop($result['body']);
        $file['title'] = "test_file_".$file['id'];
        $file['desc'] = '';
        $result = Curl::getJson($img_api, $file);
        $this->assertEquals(204, $result['code']);

        $result = Curl::getJson($img_api, $file, 'DELETE');
        $this->assertEquals(204, $result['code']);

        $url = "$img_api?id={$file['id']}";
        $result = Curl::getJson($url);
        $this->assertEquals(404, $result['code']);
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

    public function testBrowse(){
        $up_api = $this->_conf['upload_api_url'];
        $img_api = $this->_conf['image_api_url'];

        $result = Curl::getJson($img_api . "?start=0&count=5");

        $this->assertEquals(200, $result['code']);
        $list1 = $result['body'];

        $list2 = array();
        $start = 0;
        $count = 10;
        while(1){
            $result = Curl::getJson($img_api . "?start=$start&count=$count&order=old_first");
            $this->assertEquals(200, $result['code']);
            $ret = $result['body'];
            $list2 = array_merge($list2, $ret);
            if(count($ret) != $count)
                break;
            $start += $count;
        }
        foreach($list1 as $image){
            $this->assertEquals($image, array_pop($list2));
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

