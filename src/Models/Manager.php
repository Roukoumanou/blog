<?php

namespace App\Models;

use Dotenv\Dotenv;

class Manager
{
    protected $em;

    private static $_instance;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Manager();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $this->em = $this->getEm();
    }

    /**
     * Permet de renvoyer le manageur
     */
    public function getEm()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'MAIL_USERNAME', 'MAIL_PASSWORD']);

        $dsn = 'mysql:dbname='.$_ENV['DB_NAME'].';host='.$_ENV['DB_HOST'].'';

        try {
            $db = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ]);
        } catch (\PDOException $pe) {
            echo $pe->getMessage();
        }
        

        return $db;
    }
}
