<?php

namespace App\Repository;

use Dotenv\Dotenv;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

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
     *
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;

        $config = Setup::createAnnotationMetadataConfiguration(array(dirname(__DIR__)."/Entity"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
        
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_DRIVER', 'MAIL_USERNAME', 'MAIL_PASSWORD']);

        // database configuration parameters
        $conn = array(
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'host' => $_ENV['DB_HOST'],
            'driver' => $_ENV['DB_DRIVER'],
        );

        // obtaining the entity manager
        $entityManager = EntityManager::create($conn, $config);

        return $entityManager;
    }
}
