<?php

namespace App\Controller\Administrator;

use Exception;
use App\Models\CommentsManager;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use App\Entity\Comments;

class AdminCommentsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function comments(int $currentPage)
    {
        try {
            $comments = (new CommentsManager())->getInvalidComments();

            $totalItems = count($comments);
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, Comments::ITEMS_PER_PAGE, Comments::NEIGHBOURS);
            
            $comments = (new CommentsManager())->pagination($pagination->limit(), $pagination->offset());

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
        $comment = (new CommentsManager())->getComment($id);

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
                (new CommentsManager())->valided($id);

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
