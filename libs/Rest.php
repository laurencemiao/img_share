<?php

/*
 * http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
class Rest{

    function _status($code_message){
        header("HTTP/1.1 $code_message");
    }

    function handle(){
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if(!method_exists($this, $method)){
            $this->_status("405 Method Not Allowed");
            exit();
        }

        $args = array();
        switch($method){
            case 'get':
                $params = $_GET;
                $raw_data = '';
                break;
            case 'post':
                $params = $_POST;
                $raw_data = file_get_contents("php://input");
                break;
            case 'put':
            case 'delete':
                $raw_data = file_get_contents("php://input");
                $params = array();
                break;
        }
        $args = array($params, $raw_data);

        $ret = call_user_func_array(array($this, $method), $args);

        if(!is_null($ret))
            echo $ret;

        exit();
    }
}
