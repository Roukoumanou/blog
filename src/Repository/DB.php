<?php
namespace App\Repository;

require_once '../setting.php';

abstract class DB
{
    private $db;

    protected $db_host;

    protected $db_name;

    protected $db_user;

    protected $db_user_password;

    public function __construct($db_host = 'localhost', $db_name = 'test', $db_user = 'root', $db_user_password = '')
    {
        $this->db = new \PDO('mysql:host='.$GLOBALS['db_host'].';dbname='.$GLOBALS['db_name'].';charset=utf8', $GLOBALS['db_user'], $GLOBALS['db_user_password']);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getDb()
    {
        return $this->db;
    }
}