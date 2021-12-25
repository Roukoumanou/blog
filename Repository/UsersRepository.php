<?php 
require_once 'DB.php';
require_once '../Entity/Users.php';

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
            'is_valid' => $user->getIs_valid(),
            'created_at' => date_format($user->getCreatedAt(),"Y-m-d"),
            'images_id' => 1
        ];
        
        $result = $this->db->prepare('
            INSERT INTO users 
                (first_name, last_name, email, password, role, is_valid, created_at, images_id)
                VALUES
                (:first_name, :last_name, :email, :password, :role, :is_valid, :created_at, :images_id)');
        $result->execute($data);
    }

    public function getUser($email)
    {
        $result = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $result->execute([':email' => $email]);
        
        return $result->fetch();
    }
}