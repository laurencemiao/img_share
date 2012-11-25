<?php

class Storage{

    private $_tmp;
    private $_img;
    private $_baseurl;

    function __construct($conf){
        $this->_tmp = $conf['pendings'];
        $this->_img = $conf['images'];
        $this->_baseurl = $conf['baseurl'];
    }

    function getUrl($id){
        return "$this->_baseurl/$id";
    }

    function upload($src_path){
        $info = pathinfo($src_path);
        $filepath = tempnam($this->_tmp, 'store_')."_".$info['basename'];
        $id = null;
        if(copy($src_path, $filepath))
            $id = basename($filepath);

        return $id;
    }

    function save($id){
        $old_path = $this->_tmp."/$id";
        $filepath = $this->_img."/$id";
        return rename($old_path, $filepath);
    }

    function delete($id, $substore = 'images'){
        if($substore == 'pending')
            $filepath = $this->_tmp."/$id";
        else
            $filepath = $this->_img."/$id";
        return unlink($filepath);
    }

    function exist($id, $substore = 'images'){
        if($substore == 'pending')
            $filepath = $this->_tmp."/$id";
        else
            $filepath = $this->_img."/$id";

        return is_file($filepath);
    }

}
