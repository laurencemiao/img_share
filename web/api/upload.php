<?php

$__DIR_CUR__ = dirname(__FILE__);
include_once("$__DIR_CUR__/../boot.php");

/**
 * accept POST
 */
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pdo = $APP->getPdoInstance();
    $store = $APP->getStorageInstance();

    $ret = array();
    $cnt_file = 0;
    foreach($_FILES as $k => $v){
        $cnt_file ++;
        
        $name = $v['name'];
        $size = $v['size'];
        $basename = basename($v['tmp_name']);
        $id = $store->upload($v['tmp_name']);
        if($id){
            $sql = "INSERT INTO upload(store_id, filename, filesize) VALUES('$id', '$name', $size)";
            $affected = $pdo->exec($sql);

            // recycle will take care of the orphan images, if failed.
            if($affected){
                $ret[] = array('id'=>$id, 'state' => 'pending');
            }
        }
    }

    echo json_encode($ret);

}else{
    header("HTTP/1.1 403 Forbidden");
}
