<?php

/*
 * __DIR_ROOT__ is defined in bootstrap, we just use it as it
 * is already available.
 * And it should be the only thing that a config file imports
 * from outside.
 */

$_CONF = array(
    'storage' => array(
        'pendings' => __DIR_ROOT__.'/tmp',
        'images' => __DIR_ROOT__.'/uploads',
        'baseurl' => 'http://localhost:8080/storage',
    ),
    'database' => array(
        'host' => 'localhost',
        'dbname' => 'img_share',
        'user' => 'root',
        'password' => '',
        'options' => array(
            PDO::ATTR_AUTOCOMMIT => TRUE,
        )
    ),
    'libs' => __DIR_ROOT__.'/libs',
    'server' => array(
        'tmpdir' => __DIR_ROOT__.'/tmp',
        'sampledir' => '/usr/share/backgrounds/gnome',
        'api' => array(
            'image' => 'http://localhost:8080/api/image',
            'upload' => 'http://localhost:8080/api/upload',
        ),
    ),
);

