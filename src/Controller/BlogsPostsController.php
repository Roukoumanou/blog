<?php

namespace App\Controller;

use Exception;
use App\Entity\Comments;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use App\Entity\Posts;
use App\Models\CommentsManager;
use App\Models\PostsManager;

class BlogsPostsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function posts(int $currentPage)
    {
        try {
            $posts = (new PostsManager())->lists();

            $totalItems = count($posts);

            $pagination = new Pagination($totalItems, $currentPage, Posts::ITEMS_PER_PAGE, Posts::NEIGHBOURS);

            $posts = (new PostsManager())->pagination($pagination->limit(), $pagination->offset());

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
            $commentes = (new CommentsManager())->getComments($post['id']);

            if (! empty($_POST) && $_POST['_token'] === $this->getUser()['token']) {
                
                $user = $this->getUser()['id'];
                $comment = new Comments();

                $comment->setPostId($post['id'])
                    ->setUserId($user)
                    ->setContent($_POST['message']);

                (new CommentsManager())->addComments($comment);

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
