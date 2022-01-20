<?php

namespace App\Controller\Administrator;

use App\Repository\Manager;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AdminCommentsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function comments(int $currentPage)
    {
        try {
            $em = Manager::getInstance()->getEm();

            $repo = $em->getRepository("App\Entity\Commentes");

            $totalItems = count($repo->findBy(['isValid' => false]));
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $offset = $pagination->offset();
            $limit = $pagination->limit();

            $comments = $repo->findBy(
                ['isValid' => false], ['id' => 'ASC'], $limit, $offset);

            $pages = $pagination->build();
            
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->render('admin/comments.html.twig', [
            'title' => 'Liste des commentaires non validés',
            'comments' => $comments,
            'pages' => $pages
        ]);
    }

    /**
     * @param integer $id
     */
    public function comment(int $id)
    {
        $em = Manager::getInstance()->getEm();
        $comment = $em->getRepository("App\Entity\Commentes")->findOneBy(['id' => $id]);

        return $this->render('admin/comment.html.twig', [
            'title' => 'Commentaire de l\'article n° '.$comment->getPostId()->getId(),
            'comment' => $comment
        ]);
    }

    /**
     * @param integer $id
     */
    public function validedComment(int $id)
    {
        if (! empty($_POST) && $this->csrfVerify($_POST)) {
            try {
                $em = Manager::getInstance()->getEm();
                $comment = $em->getRepository("App\Entity\Commentes")->findOneBy(['id' => $id]);
    
                $comment->setIsValid(true);
    
                $em->merge($comment);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Le commentaire a été validé!'
                );

                return $this->redirect('/admin-comments');

            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }

        return $this->redirect('/admin-comments');
    }
}
