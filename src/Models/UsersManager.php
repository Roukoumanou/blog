<?php

namespace App\Models;

use Exception;
use App\Entity\Users;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;

class UsersManager extends AbstractController
{
    public function insert($user)
    {
        // Sinon je sauvegarde le nouveau utilisateur
        $em  = $this->getDB();
        $req = $em->prepare("INSERT INTO users 
            (first_name, last_name, email, password, role, images_id, created_at, is_valid) 
            VALUES 
            (:first_name, :last_name, :email, :password, :role, :images, :created_at, :is_valid)");
        
        $req->execute([
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':role' => Users::ROLE_USER,
            ':images' => $user->getImages(),
            ':created_at' => date_format($user->getCreatedAt(), 'Y-m-d'),
            ':is_valid' => 0
        ]);
    }

    public function update($user)
    {
        //je sauvegarde l'utilisateur
        $result = $this->getDB()->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, updated_at = :updated_at
        WHERE id = :user");
        $result->execute([
        ':first_name' => $user->getFirstName(),
        ':last_name' => $user->getLastName(),
        ':email' => $user->getEmail(),
        ':updated_at' => date_format($user->getUpdatedAt(), 'Y-m-d'),
        ':user' => $this->getUser()['id']
        ]);
    }

    public function updatePassword($id, $userUpdate)
    {
        //je sauvegarde l'utilisateur
        $em = $this->getDB();
        $req = $em->prepare("UPDATE users SET password = :password, updated_at = :updated_at WHERE id = :id");
        $req->execute([
            ':id' => $id,
            ':password' => $userUpdate->getPassword(),
            ':updated_at' => date_format($userUpdate->getUpdatedAt(), 'Y-m-d')
        ]);
    }

    /**
     * Permet de verifier si un utilisateur existe
     *
     * @param string $email
     */
    public function userVerify(string $email)
    {
        try {
            $em = $this->getDB();
            $req = $em->prepare("SELECT * FROM users WHERE email = :email");
            $req->execute([':email' => $email]);
            $result = $req->fetch();
            return $result;
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
    }
}
