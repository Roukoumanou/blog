<?php

namespace App\Controller;

use App\Repository\Manager;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use App\Entity\Posts;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class BlogsPostsController extends AbstractController
{
    public function posts()
    {
        try {
            /** @var EntityManagerInterface */
            $em = Manager::getInstance()->getEm();
            $posts = $em->getRepository("App\Entity\Posts")->findBy([
                'status' => Posts::PUBLISHED
            ], [
                'id' => 'DESC'
            ]);
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
        
        return $this->render('blogs_posts.html.twig', [
            'title' => 'Articles',
            'posts' => $posts
        ]);
    }

    public function showPost($id)
    {
        try {
            /** @var EntityManagerInterface */
            $em = Manager::getInstance()->getEm();
            $post = $em->getRepository("App\Entity\Posts")->findOneBy(['id' => $id]);
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->render('show_post.html.twig', [
            'title' => $post->getTitle(),
            'post' => $post
        ]);
    }
}
