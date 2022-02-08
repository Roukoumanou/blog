<?php

namespace App\Controller\Administrator;

use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use Exception;

class AdminCommentsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function comments(int $currentPage)
    {
        try {
            $em = $this->getDB();
            $result = $em->prepare("SELECT * FROM commentes WHERE is_valid = :not_valid");
            $result->execute([':not_valid' => false]);
            $commentes = $result->fetchAll();

            $totalItems = count($commentes);
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $offset = $pagination->offset();
            $limit = $pagination->limit();

            $req = $em->prepare("SELECT * FROM commentes WHERE is_valid = :not_valid ORDER BY id ASC LIMIT $limit OFFSET $offset");
            $req->execute([':not_valid' => false]);
            $comments = $req->fetchAll();

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
        $em = $this->getDB();
            $result = $em->prepare("SELECT * FROM commentes WHERE id = :id");
            $result->execute([':id' => $id]);
            $comment = $result->fetch();

        return $this->render('admin/comment.html.twig', [
            'title' => 'Commentaire a valider n° '.$comment['id'],
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
                $em = $this->getDB();
                $result = $em->prepare("UPDATE commentes SET is_valid = :is_valid WHERE id = :id");
                $result->execute([
                    ':id' => $id,
                    ':is_valid' => true
                ]);

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
