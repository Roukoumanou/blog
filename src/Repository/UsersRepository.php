<?php

namespace App\Repository;

use App\Repository\Manager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Cette classe sert de repository pour l'entité des utilisateurs
 * 
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
class UsersRepository extends EntityRepository
{

    public function getUser($email)
    {
        /** @var EntityManagerInterface */
        $em = Manager::getInstance()->getEm();
        return $em->getRepository('App\Entity\Users')->findOneBy(['email' => htmlspecialchars($email)]);
    }
}
