<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/../boot.php");

/**
 * accept GET, POST, PUT, DELETE
 */
class Image_Service extends Rest{

    function get($query){
        global $APP;

        $store = $APP->getStorageInstance();
        $pdo = $APP->getPdoInstance();

        $server_conf = $APP->getConfig('server');

        $start = empty($query['start']) ? 0 : intval($query['start']);
        $count = empty($query['count']) ? 24 : intval($query['count']);

        if(empty($query['id'])){
            /*
             * if 'id' provided, it is a single image query
             * otherwise, it is a image browsing request
             */
            if(empty($query['order']) || ($query['order'] != 'old_first')){
                $order = 'new_first';
            }else{
                $order = 'old_first';
            }

            $sql = "SELECT `id`, `title`, `desc`, `filename`, `store_id`, `store_thumb_id`, `change_ts` FROM gallery";
            if($order =='new_first'){
                $sql .= " ORDER BY `id` DESC";
            }
            $sql .= " LIMIT $start , $count";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        }else{
            $image = $this->_getImage($query['id']);
            if(empty($image)){
                $this->_status('404 Not Found');
                return;
            }
            $result = array($image);
        }

        $ret = array();
        foreach($result as $img){
            $thumb_url = empty($img['store_thumb_id']) ?
                        "" : $store->getUrl($img['store_thumb_id']);

            $ret[] = array(
                        'id'=>$img['store_id'],
                        'title'=>$img['title'],
                        'url'=>$store->getUrl($img['store_id']),
                        'thumb_url'=>$thumb_url,
                        'change_time' => $img['change_ts']);
        }

        return json_encode($ret);
    }

    function post($query, $raw_data){
        global $APP;

        $store = $APP->getStorageInstance();
        $pdo = $APP->getPdoInstance();

        $data = json_decode($raw_data, true);

        $stm = $pdo->prepare("SELECT filename, filesize FROM upload WHERE store_id=:store_id");
        $stm->bindParam(":store_id", $data['id']);
        $stm->execute();
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        $file = array_shift($result);

        // no such image uploaded, give it up.
        if(empty($file)){
            $this->_status('403 Forbidden');
            return;
        }

        $stm = $pdo->prepare("DELETE FROM upload WHERE store_id=:store_id");
        $stm->bindParam(":store_id", $data['id']);
        $stm->execute();

        if($store->save($data['id'])){
            $stm = $pdo->prepare("INSERT INTO gallery(`title`, `desc`, `filename`, `filesize`, `store_id`) VALUES(:title, :desc, :filename, :filesize, :store_id)");
            $stm->bindParam(":title", $data['title']);
            $stm->bindParam(":desc", $data['desc']);
            $stm->bindParam(":filename", $file['filename']);
            $stm->bindParam(":filesize", $file['filesize']);
            $stm->bindParam(":store_id", $data['id']);
        
            if($stm->execute() && $stm->rowCount()){
                $this->_status('204 No Contents');
            }else
                $this->_status('400 Bad Request');
        }else{
            $this->_status('403 Forbidden');
        }

        return;
    }

    function put($query, $raw_data){
        global $APP;

        $pdo = $APP->getPdoInstance();

        $data = json_decode($raw_data, true);
        if(empty($data['id'])){
            $this->_status('400 Bad Request');
            return;
        }

        $old_img = $this->_getImage($data['id']);

        if(empty($old_img)){
            $this->_status('400 Bad Request');
            return;
        }

        $sql = "UPDATE gallery SET";
        $params = array();
        if(!empty($data['thumb_id'])){
            /*
             * client is trying to upload a new thumbnail.
             */

            $stm = $pdo->prepare("SELECT filename, filesize FROM upload WHERE store_id=:store_id");
            $stm->bindParam(":store_id", $data['thumb_id']);
            $stm->execute();
            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
            $file = array_shift($result);

            if(empty($file)){
                $this->_status('403 Forbidden');
                return;
            }

            $stm = $pdo->prepare("DELETE FROM upload WHERE store_id=:store_id");
            $stm->bindParam(":store_id", $data['thumb_id']);
            $stm->execute();

            $store = $APP->getStorageInstance();
            if($store->save($data['thumb_id'])){
                $sql .= "`store_thumb_id`=:store_thumb_id";
            }else{
                $this->_status('403 Forbidden');
                return;
            }

            $stm = $pdo->prepare($sql . " WHERE store_id=:store_id");
            $stm->bindParam(":store_thumb_id", $data['thumb_id']);
            $stm->bindParam(":store_id", $data['id']);

            if($stm->execute() && $stm->rowCount()){
                $this->_status('204 Non Content');
                if(!empty($old_img['store_thumb_id']))
                    $store->delete($old_img['store_thumb_id']);
            }else{
                $this->_status('400 Bad Request');
                return;
            }
        }else{
            // currently, do not support updating other attributes
        }

        return;
    }

    function delete($query, $raw_data){
        global $APP;

        $store = $APP->getStorageInstance();
        $pdo = $APP->getPdoInstance();

        $data = json_decode($raw_data, true);
        if(empty($data['id'])){
            $this->_status('400 Bad Request');
            return;
        }

        $old_img = $this->_getImage($data['id']);

        if(empty($old_img)){
            $this->_status('400 Bad Request');
            return;
        }

        $stm = $pdo->prepare("DELETE FROM gallery WHERE store_id=:store_id");
        $stm->bindParam(":store_id", $data['id']);
        $stm->execute();

        // no such image uploaded, give it up.
        if(! $stm->rowCount()){
            $this->_status('403 Forbidden');
            return;
        }

        $store->delete($data['id']);
        if(!empty($old_img['store_thumb_id']))
            $store->delete($old_img['store_thumb_id']);

        $this->_status('204 No Content');

        return;
    }
    
    private function _getImage($id){
        global $APP;

        $pdo = $APP->getPdoInstance();

        $stm = $pdo->prepare("SELECT `id`, `title`, `desc`, `filename`, `filesize`, `store_id`, `store_thumb_id`, `change_ts` FROM gallery WHERE store_id=:store_id");
        $stm->bindParam(":store_id", $id);
        $stm->execute();
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);

        return array_shift($result);
    }
}

$img_service = new Image_Service();
$img_service->handle();
