<?php

namespace App\Controller;

use Exception;
use App\Entity\Commentes;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use App\Models\CommentesManager;
use App\Models\PostsManager;

class BlogsPostsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function posts(int $currentPage)
    {
        try {
            $sql = 'SELECT * FROM posts WHERE status = :public';
            $posts = (new PostsManager())->listes($sql);

            $totalItems = count($posts);
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $limit = $pagination->limit();
            $offset = $pagination->offset();

            $posts = (new PostsManager())->pagination($sql, $limit, $offset);

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
            $post = (new PostsManager())->getPost($id);
            $commentes = (new CommentesManager())->getCommentes($post['id']);

            if (! empty($_POST) && $_POST['_token'] === $this->getUser()['token']) {
                
                $user = $this->getUser()['id'];
                $comment = new Commentes();

                $comment->setPostId($post['id'])
                    ->setUserId($user)
                    ->setContent($_POST['message']);

                (new CommentesManager())->addCommentes($comment);

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
