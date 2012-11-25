<?php

/*
 * a simple bootstrap
 */
class App{
    private $_conf;
    private $_pdo;
    private $_store;

    function __construct($conf){
        $this->_conf = $conf;

        // register our lib loader
        spl_autoload_register(array($this, 'autoload'));

        $this->_store = new Storage($this->getConfig('storage'));

        $pdo_conf = $this->getConfig('database');
        $this->_pdo = new PDO("mysql:host={$pdo_conf['host']};dbname={$pdo_conf['dbname']}",
                                $pdo_conf['user'],
                                $pdo_conf['password']);
        foreach($pdo_conf['options'] as $k => $v){
            $this->_pdo->setAttribute($k, $v);
        }
    }

    function __destruct(){
        $this->_store = null;
        $this->_pdo = null;
    }

    function getStorageInstance(){
        return $this->_store;
    }

    function getPdoInstance(){
        return $this->_pdo;
    }

    // this is a simple lib loader, dir is specified in $_CONF;
    function autoload($libname){
        include_once($this->_conf['libs']."/{$libname}.php");
    }

    function getConfig($name){
        return $this->_conf[$name];
    }
}

