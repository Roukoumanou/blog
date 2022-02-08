<?php

namespace App\Models;

use App\Entity\Images;
use App\Controller\AbstractController;

class ImagesManager extends AbstractController
{
    public function getUserImage(int $image)
    {
        $req = $this->getDB()->prepare('SELECT * FROM images WHERE id = :image');
            $req->execute([':image' => $image]);
            $image = $req->fetch();
        return $image;
    }

    public function insert(Images $image)
    {
        $req   = $this->getDB()->prepare("INSERT INTO images (name, created_at) VALUES (:name, :created_at)");
        $req->execute([
            ':name' => $image->getName(),
            ':created_at' => date_format($image->getCreatedAt(), 'Y-m-d')
        ]);
        
        return $this->getLast();
    }

    private function getLast()
    {
        $req   = $this->getDB()->prepare('SELECT * FROM images ORDER BY id DESC LIMIT 1');
        $req->execute();
        $image = $req->fetch();
        return $image['id'];
    }

    public function update($image, $lastImage)
    {
        $req   = $this->getDB()->prepare("UPDATE images SET name = :name, updated_at = :updated_at WHERE id = :id");
                        
        $req->execute([
            ':name' => $image->getName(),
            ':id' => $lastImage['id'],
            ':updated_at' => date_format($image->getUpdatedAt(), 'Y-m-d')
        ]);
    }
}
