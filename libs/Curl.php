<?php

class Curl{

    static private function _request($url, $data = null, $headers = array(), $opts = array()){

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        if(! empty($data)){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        foreach($opts as $k => $v){
            curl_setopt($ch, $k, $v);
        }

        //execute post
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        //close connection
        curl_close($ch);

        return array('info'=>$info, 'body'=>$result);

    }

    static public function get($url){
        return self::_request($url, null);
    }

    static public function post($url, $data){
        return self::_request($url, $data);
    }

    static public function put($url, $data){
        return self::_request($url, $data, array(), array(CURLOPT_CUSTOMREQUEST=>'PUT'));
    }

    static public function delete($url, $data){
        return self::_request($url, $data, array(), array(CURLOPT_CUSTOMREQUEST=>'DELETE'));
    }

    static function getJson($url, $data = null, $method = null){
        if(empty($data) && empty($method)){
            // I guess, this is a get request
            $method = 'GET';
        }elseif(empty($method)){
            // I guess, this is a post request
            $method = 'POST';
        }
        switch($method){
            case 'POST.FILE':
                $result = Curl::post($url, $data);
                break;
            case 'POST':
                $json = json_encode($data);
                $headers = array('Content-Type: application/json; charset=utf-8');
                $result = Curl::post($url, $json, $headers);
                break;
            case 'PUT':
                $json = json_encode($data);
                $headers = array('Content-Type: application/json; charset=utf-8');
                $result = Curl::put($url, $json, $headers);
                break;
            case 'DELETE':
                $json = json_encode($data);
                $headers = array('Content-Type: application/json; charset=utf-8');
                $result = Curl::delete($url, $json, $headers);
                break;
            default:
                $result = Curl::get($url);
        }
        $body = json_decode($result['body'], true);
        return array('code' => $result['info']['http_code'], 'body' => $body);
    }
}
