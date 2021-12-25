<?php 
require_once '../setting.php';

abstract class DB
{
    protected $db;
    public function __construct()
    {
        $this->db = new \PDO('mysql:host='.$GLOBALS['db_host'].';dbname='.$GLOBALS['db_name'].';charset=utf8', $GLOBALS['db_user'], $GLOBALS['db_user_password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}