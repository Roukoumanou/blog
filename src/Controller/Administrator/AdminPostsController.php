<?php

namespace App\Controller\Administrator;

use Exception;
use App\Entity\Posts;
use App\Repository\Manager;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Exception\ExceptionController;

class AdminPostsController extends AbstractController
{
    /**
     * @var EntityManagerInterface $em
     */
    public function postList($currentPage)
    {
        try {
            $em = Manager::getInstance()->getEm();

            $repo = $em->getRepository("App\Entity\Posts");

            $totalItems = count($repo->findAll());
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $offset = $pagination->offset();
            $limit = $pagination->limit();

            $posts = $repo->findBy(
                [], ['id' => 'DESC'], $limit, $offset);

            $pages = $pagination->build();
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
        
        return $this->render('admin/post_list.html.twig', [
            'title' => 'Liste des Articles',
            'posts' => $posts,
            'pages' => $pages
        ]);
    }

    /**
     * @var EntityManagerInterface $em
     */
    public function newPost()
    {
        if (! empty($_POST) && $this->csrfVerify($_POST)) {
            try {
                $post = new Posts();

                $post->setTitle($_POST['title'])
                    ->setIntro($_POST['intro'])
                    ->setContent($_POST['content'])
                    ->setStatus($_POST['status'])
                    ->setCreatedBy($this->getUser()['firstName'].' '.$this->getUser()['lastName'])
                    ;

                if ($this->testDoubleTitle($post->getTitle())) {
                    throw new Exception("Cet Titre est déja utilisé!", 1);
                }

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

    /**
     * @var EntityManagerInterface $em
     *
     * @param integer $id
     */
    public function updatePost(int $id)
    {
        $post = $this->getPost($id);
        if (! empty($_POST) && $this->csrfVerify($_POST)) {
            try {
                $post->setTitle($_POST['title'])
                ->setIntro($_POST['intro'])
                ->setContent($_POST['content'])
                ->setStatus($_POST['status'])
                ->setUpdatedAt(new \DateTime())
                ->setCreatedBy($this->getUser()['firstName'].' '.$this->getUser()['lastName'])
                ;

                if ($this->testDoubleTitle($post->getTitle())) {
                    throw new Exception("Cet Titre est déja utilisé!", 1);
                }

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


    /**
     * @var EntityManagerInterface $em
     * @var Posts $post
     *
     * @param integer $id
     */
    public function deletePost(int $id)
    {
        if (! empty($_POST) && $this->csrfVerify($_POST)) {
            try {
                $em = Manager::getInstance()->getEm();
                $post = $em->find(Posts::class, $id);

                $comments = $post->getCommentes();

                if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $em->remove($comment);
                    }
                }
                
                //Je supprime le post
                $em->remove($post);
                $em->flush();
    
                return $this->redirect('/admin');
            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }
    }

    /**
     * @var EntityManagerInterface $em
     *
     * @param integer $id
     */
    private function getPost(int $id)
    {
        try {
            $em = Manager::getInstance()->getEm();
            return $em->getRepository('App\Entity\Posts')->findOneBy(['id' => $id]);
            
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
    }

    private function testDoubleTitle(string $title): bool
    {
        $em = Manager::getInstance()->getEm();
        if ($em->getRepository('App\Entity\Posts')->findOneBy(
            ['title' => htmlspecialchars($title)])) {
            return true;
        };

        return false;
    }
}
