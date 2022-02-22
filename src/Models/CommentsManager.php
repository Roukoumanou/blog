<?php

namespace App\Models;

use App\Entity\Comments;
use App\Controller\AbstractController;

class CommentsManager extends AbstractController
{
    public function getComments(int $postId)
    {
        // Je recupère les commentaire de ce poste
        $req = $this->getDB()->prepare("SELECT * FROM comments WHERE is_valid = true AND post_id = :post");
        $req->execute([':post' => $postId]);
        $comments = $req->fetchAll();
        return $comments;
    }

    public function pagination(int $limit, int $offset)
    {
        $req = $this->getDB()->prepare("SELECT * FROM comments WHERE is_valid = :not_valid ORDER BY id ASC LIMIT $limit OFFSET $offset");
        $req->execute([':not_valid' => false]);
        $comments = $req->fetchAll();
        return $comments;
    }

    public function getComment(int $id)
    {
        $em = $this->getDB();
        $result = $em->prepare("SELECT * FROM comments WHERE id = :id");
        $result->execute([':id' => $id]);
        $comment = $result->fetch();
        return $comment;
    }

    public function valided(int $id)
    {
        $em = $this->getDB();
        $result = $em->prepare("UPDATE comments SET is_valid = :is_valid WHERE id = :id");
        $result->execute([
            ':id' => $id,
            ':is_valid' => true
        ]);
    }

    public function getInvalidComments()
    {
        $em = $this->getDB();
        $result = $em->prepare("SELECT * FROM comments WHERE is_valid = :not_valid");
        $result->execute([':not_valid' => false]);
        $comments = $result->fetchAll();
        return $comments;
    }

    public function addComments(Comments $comment)
    {
        $req = $this->getDB()->prepare("INSERT INTO comments (post_id, user_id, content, is_valid, created_at)
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
