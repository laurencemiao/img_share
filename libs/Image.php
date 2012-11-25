<?php

/*
 * http://cn2.php.net/manual/en/function.imagejpeg.php
 */
class Image{
    private $_img_path = null;
    private $_img = null;
    private $_img_type = null;

    function __construct($img_path){


        $this->_img_type = $this->_getImageType($img_path);
        if(empty($this->_img_type))
            throw new Exception("image '".basename($img_path)."' type not supported");

        $this->_img_path = $img_path;
    }

    private function _loadImage(){
        if(is_null($this->_img)){
            $func = "ImageCreateFrom".$this->_img_type;
            $this->_img = $func($this->_img_path);
        }
    }

    private function _getImageType($filepath){
        $ret = exif_imagetype($filepath);
        switch ($ret){
            case IMAGETYPE_GIF:
                $type = 'gif';
                break;
            case IMAGETYPE_JPEG:
                $type = 'jpeg';
                break;
            case IMAGETYPE_PNG:
                $type = 'png';
                break;
            default:
                $type = null;
        }

        return $type;
    }

    // http://www.njphp.cn/thread-2800-1-1.html
    function createThumb($thumb_path, $thumb_size){

        $this->_loadImage();

        $orig_width = imagesx( $this->_img );
        $orig_height = imagesy( $this->_img );

        if($orig_width > $orig_height){
            $x_off = ($orig_width - $orig_height) / 2;
            $y_off = 0;
            $square_size = $orig_height;
        }else{
            $x_off = 0;
            $y_off = ($orig_height - $orig_width) / 2;
            $square_size = $orig_width;
        }

        $canvas = imagecreatetruecolor($thumb_size, $thumb_size);
        $ret = imagecopyresampled($canvas, $this->_img, 0, 0, $x_off, $y_off, $thumb_size, $thumb_size, $square_size, $square_size);
        if($ret){
            imagejpeg($canvas, $thumb_path);
        }

        return new Image($thumb_path);
    }

    function getWidth(){
        $this->_loadImage();
        return imagesx( $this->_img );
    }

    function getHight(){
        $this->_loadImage();
        return imagesy( $this->_img );
    }

    function __destruct(){
        if($this->_img)
            imagedestroy($this->_img);
    }
}

