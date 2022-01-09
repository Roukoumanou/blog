<?php

namespace App\Controller\Administrator;

use App\Entity\Posts;
use App\Repository\Manager;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AdminPostsController extends AbstractController
{
    public function postList()
    {
        /** @var EntityManagerInterface */
        $em = Manager::getInstance()->getEm();
        $posts = $em->getRepository("App\Entity\Posts")->findAll();
        
        return $this->render('admin/post_list.html.twig', [
            'title' => 'liste des Articles',
            'posts' => $posts
        ]);
    }

    public function newPost()
    {
        if (! empty($_POST)) {
            try {
                $post = new Posts();

                $post->setTitle($_POST['title'])
                    ->setIntro($_POST['intro'])
                    ->setContent($_POST['content'])
                    ->setStatus($_POST['status'])
                    ->setCreatedBy($this->getUser()['firstName'].' '.$this->getUser()['lastName'])
                    ;
                
                //je sauvegarde l'article
                $em = Manager::getInstance()->getEm();
                $em->persist($post);
                $em->flush();
    
                return $this->redirect("/admin");
            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }

        return $this->render('admin/new_post.html.twig', [
            'title' => 'Ajouter un nouveau poste'
        ]);
    }

    public function updatePost(int $id)
    {
        $post = $this->getPost($id);
        if (! empty($_POST)) {
            try {
                $post->setTitle($_POST['title'])
                ->setIntro($_POST['intro'])
                ->setContent($_POST['content'])
                ->setStatus($_POST['status'])
                ->setUpdatedAt(new \DateTime())
                ->setCreatedBy($this->getUser()['firstName'].' '.$this->getUser()['lastName'])
                ;

                //je sauvegarde le post
                $em = Manager::getInstance()->getEm();
                $em->merge($post);
                $em->flush();

                return $this->redirect('/admin');

            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }

        return $this->render('admin/update_post.html.twig', [
            'title' => 'Modifier ce blog post',
            'post' => $post
        ]);
    }

    public function viewPost(int $id)
    {
        $post = $this->getPost($id);
        
        return $this->render('admin/post_view.html.twig', [
            'title' => $post->getTitle(),
            'post' => $post
        ]);
    }

    public function deletePost(int $id)
    {
        try {
            /** @var EntityManagerInterface $em */
            $em = Manager::getInstance()->getEm();
            $post = $em->find(Posts::class, $id);
            
            //Je supprime le post
            $em->remove($post);
            $em->flush();

            return $this->redirect('/admin');
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
    }

    private function getPost($id)
    {
        try {
            /** @var EntityManagerInterface */
            $em = Manager::getInstance()->getEm();
            return $em->getRepository('App\Entity\Posts')->findOneBy(['id' => htmlspecialchars($id)]);
            
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
    }
}