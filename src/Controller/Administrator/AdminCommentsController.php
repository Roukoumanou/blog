<?php

namespace App\Controller\Administrator;

use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use App\Models\CommentesManager;
use Exception;

class AdminCommentsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function comments(int $currentPage)
    {
        try {
            $commentes = (new CommentesManager())->getInvalidCommentes();

            $totalItems = count($commentes);
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $limit = $pagination->limit();
            $offset = $pagination->offset();
            $commentes = (new CommentesManager())->pagination($limit, $offset);

            $pages = $pagination->build();
            
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->render('admin/comments.html.twig', [
            'title' => 'Liste des commentaires non validés',
            'comments' => $commentes,
            'pages' => $pages
        ]);
    }

    /**
     * @param integer $id
     */
    public function comment(int $id)
    {
        $comment = (new CommentesManager())->getComment($id);

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
                (new CommentesManager())->valided($id);

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
