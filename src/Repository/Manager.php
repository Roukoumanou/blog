<?php

namespace App\Repository;


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
      
        // database configuration parameters
        $conn = array(
            'dbname' => 'p5test',
            'user' => 'loukmane',
            'password' => 'Mot2*Passe',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );

        // obtaining the entity manager
        $entityManager = EntityManager::create($conn, $config);

        return $entityManager;
    }
}
