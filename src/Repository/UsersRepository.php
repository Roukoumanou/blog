<?php

namespace App\Repository;

use App\Entity\Users;
use App\Repository\DB;
use \PDO;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
class UsersRepository extends DB
{
    public function register(Users $user)
    {
        $data = [
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'is_valid' => $user->getIsValid(),
            'created_at' => date_format($user->getCreatedAt(),"Y-m-d"),
            'images_id' => 1
        ];
        
        $result = $this->getDb()->prepare('
            INSERT INTO users 
                (first_name, last_name, email, password, role, is_valid, created_at, images_id)
                VALUES
                (:first_name, :last_name, :email, :password, :role, :is_valid, :created_at, :images_id)');
        $result->execute($data);
    }

    public function getUser($email)
    {
        $result = $this->getDb()->prepare('SELECT * FROM users WHERE email = :email');
        
        $result->execute([':email' => $email]);
        
        return $result->fetch(PDO::FETCH_OBJ);
    }
}
