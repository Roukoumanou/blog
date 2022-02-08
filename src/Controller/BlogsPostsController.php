<?php

namespace App\Controller;

use Exception;
use App\Entity\Posts;
use App\Entity\Users;
use App\Entity\Commentes;
use App\Repository\Manager;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Exception\ExceptionController;

class BlogsPostsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function posts(int $currentPage)
    {
        try {
            $em = $this->getDB();
            $sql = 'SELECT * FROM posts WHERE status = :public';
            $result = $em->prepare($sql);
            $result->execute([':public' => Posts::PUBLISHED]);
            $posts = $result->fetchAll();

            $totalItems = count($posts);
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $limit = $pagination->limit();
            $offset = $pagination->offset();

            $req = $em->prepare($sql.' ORDER BY id DESC LIMIT '.$limit.' OFFSET '.$offset);
            $req->execute([':public' => Posts::PUBLISHED]);

            $posts = $req->fetchAll();

            $pages = $pagination->build();
            
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
        
        return $this->render('blogs_posts.html.twig', [
            'title' => 'Articles',
            'posts' => $posts,
            'pages' => $pages
        ]);
    }

    public function showPost(int $id)
    {
        try {
            
            $em = $this->getDB();

            // Je recupère l'article
            $sql = "SELECT * FROM posts WHERE id = :id";
            $result = $em->prepare($sql);
            $result->execute([':id' => $id]);
            $post = $result->fetch();

            // Je recupère les commentaire de ce poste
            $req = $em->prepare("SELECT * FROM commentes WHERE is_valid = true AND post_id = :post");
            $req->execute([':post' => $post['id']]);
            $commentes = $req->fetchAll();

            if (! empty($_POST) && $_POST['_token'] === $this->getUser()['token']) {
                
                $user = $this->getUser()['id'];
                $comment = new Commentes();

                $comment->setPostId($post['id'])
                    ->setUserId($user)
                    ->setContent($_POST['message']);

                $req = $em->prepare("INSERT INTO commentes (post_id, user_id, content, is_valid, created_at)
                                        VALUES (:post_id, :user_id, :content, :is_valid, :created_at)");
                $req->execute([
                    ':post_id' => $comment->getPostId(),
                    ':user_id' => $comment->getUserId(),
                    ':content' => $comment->getContent(),
                    ':is_valid' => 0,
                    ':created_at' => date_format($comment->getCreatedAt(), 'Y-m-d')
                ]);

                $this->addFlash(
                    'success',
                    'Merci pour le commentaire ! il est en attente de validation !'
                );

                return $this->redirect('/post-'.$post['id']);
            }

        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->render('show_post.html.twig', [
            'title' => $post['title'],
            'post'  => $post,
            'commentes' => $commentes
        ]);
    }
}
