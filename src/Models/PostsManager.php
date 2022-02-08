<?php

namespace App\Models;

use App\Entity\Posts;
use App\Entity\Users;
use App\Controller\AbstractController;

class PostsManager extends AbstractController
{
    public function listes($sql)
    {
        $em = $this->getDB();
        $result = $em->prepare($sql);
        $result->execute([':public' => Posts::PUBLISHED]);
        $posts = $result->fetchAll();

        return $posts;
    }

    public function insert(Posts $post)
    {
        $em = $this->getDb();
        $result = $em->prepare("INSERT INTO posts (title, intro, status, content, created_by, created_at, updated_at)
                    VALUES (:title, :intro, :status, :content, :created_by, :created_at, :updated_at)");
        $result->execute([
            ':title' => $post->getTitle(),
            ':intro' => $post->getIntro(),
            ':status' => $post->getStatus(),
            ':content' => $post->getContent(),
            ':created_by' => $post->getCreatedBy(),
            ':created_at' => date_format($post->getCreatedAt(), 'Y-m-d'),
            ':updated_at' => date_format($post->getUpdatedAt(), 'Y-m-d')
        ]);
    }

    public function update(array $lastPost, Posts $post)
    {
        //je sauvegarde le post
        $em = $this->getDB();
        $result = $em->prepare("UPDATE posts SET title = :title, intro = :intro, status = :status, content = :content, updated_at = :updated_at
                                WHERE id = :id");
        $result->execute([
            ':id' => $lastPost['id'],
            ':title' => $post->getTitle(),
            ':intro' => $post->getIntro(),
            ':status' => $post->getStatus(),
            ':content' => $post->getContent(),
            ':updated_at' => date_format($post->getUpdatedAt(), 'Y-m-d')
        ]);
    }

    public function delete(int $id)
    {
        $em = $this->getDB();
        $comment = $em->prepare("DELETE FROM commentes WHERE post_id = :id");
        $comment->execute(['id' => $id]);

        $post = $em->prepare("DELETE FROM posts WHERE id = :id");
        $post->execute([':id' => $id]);
    }

    public function pagination($sql, $limit, $offset, $user = null)
    {
        $req = $this->getDB()->prepare($sql.' ORDER BY id DESC LIMIT '.$limit.' OFFSET '.$offset);

        if ($user === Users::ROLE_ADMIN) {
            $req->execute();
        }
        $req->execute([':public' => Posts::PUBLISHED]);

        $posts = $req->fetchAll();
        return $posts;
    }

    public function getPost(int $id)
    {
        $em = $this->getDB();

        // Je recupère l'article
        $sql = "SELECT * FROM posts WHERE id = :id";
        $result = $em->prepare($sql);
        $result->execute([':id' => $id]);
        $post = $result->fetch();
        return $post;
    }

    public function testDoubleTitle(string $title)
    {
        $em = $this->getDB();
        $result = $em->prepare("SELECT * FROM posts WHERE title = :title");
        $result->execute([':title' => $title]);

        $response = $result->fetch();
        return $response;
    }
}
