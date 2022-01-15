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
    public function posts($currentPage)
    {
        try {
            /** @var EntityManagerInterface */
            $em = Manager::getInstance()->getEm();

            $repo = $em->getRepository("App\Entity\Posts");
            
            $totalItems = count($repo->findBy(['status' => Posts::PUBLISHED]));
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $offset = $pagination->offset();
            $limit = $pagination->limit();

            $posts = $repo->findBy(
                ['status' => Posts::PUBLISHED], ['id' => 'DESC'], $limit, $offset);

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

    public function showPost($id)
    {
        try {
            /** @var EntityManagerInterface */
            $em = Manager::getInstance()->getEm();
            $post = $em->getRepository("App\Entity\Posts")->findOneBy(['id' => $id]);
            $commentes = $em->getRepository("App\Entity\Commentes")->findBy([
                'isValid' => true,
                'postId' => $post
            ], ['id' => 'DESC'], 5) ;
            
            if (! empty($_POST)) {
                $user = $em->getRepository("App\Entity\Users")->findOneBy(['id' => $this->getUser()['id']]);
                $comment = new Commentes();

                $comment->setPostId($post)
                    ->setUserId($user)
                    ->setContent($_POST['message']);
                
                $post->addCommentes($comment);
                $em->persist($comment);
                $em->flush();

                return $this->redirect('/post-'.$post->getId());
            }
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->render('show_post.html.twig', [
            'title' => $post->getTitle(),
            'post' => $post,
            'commentes' => $commentes
        ]);
    }
}
