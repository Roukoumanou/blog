<?php

namespace App\Models;

use App\Entity\Commentes;
use App\Controller\AbstractController;

class CommentesManager extends AbstractController
{
    public function getCommentes(int $postId)
    {
        // Je recupère les commentaire de ce poste
        $req = $this->getDB()->prepare("SELECT * FROM commentes WHERE is_valid = true AND post_id = :post");
        $req->execute([':post' => $postId]);
        $commentes = $req->fetchAll();
        return $commentes;
    }

    public function pagination(int $limit, int $offset)
    {
        $req = $this->getDB()->prepare("SELECT * FROM commentes WHERE is_valid = :not_valid ORDER BY id ASC LIMIT $limit OFFSET $offset");
        $req->execute([':not_valid' => false]);
        $commentes = $req->fetchAll();
        return $commentes;
    }

    public function getComment(int $id)
    {
        $em = $this->getDB();
        $result = $em->prepare("SELECT * FROM commentes WHERE id = :id");
        $result->execute([':id' => $id]);
        $comment = $result->fetch();
        return $comment;
    }

    public function valided(int $id)
    {
        $em = $this->getDB();
        $result = $em->prepare("UPDATE commentes SET is_valid = :is_valid WHERE id = :id");
        $result->execute([
            ':id' => $id,
            ':is_valid' => true
        ]);
    }

    public function getInvalidCommentes()
    {
        $em = $this->getDB();
        $result = $em->prepare("SELECT * FROM commentes WHERE is_valid = :not_valid");
        $result->execute([':not_valid' => false]);
        $commentes = $result->fetchAll();
        return $commentes;
    }

    public function addCommentes(Commentes $comment)
    {
        $req = $this->getDB()->prepare("INSERT INTO commentes (post_id, user_id, content, is_valid, created_at)
                                        VALUES (:post_id, :user_id, :content, :is_valid, :created_at)");
        $req->execute([
            ':post_id' => $comment->getPostId(),
            ':user_id' => $comment->getUserId(),
            ':content' => $comment->getContent(),
            ':is_valid' => 0,
            ':created_at' => date_format($comment->getCreatedAt(), 'Y-m-d')
        ]);
    }
}
