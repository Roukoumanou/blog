<?php

namespace App\Controller\Administrator;

use App\Repository\Manager;
use App\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class AdminCommentsController extends AbstractController
{
    /**
     * @var EntityManagerInterface $em
     */
    public function comments()
    {
        $em = Manager::getInstance()->getEm();
        $comments = $em->getRepository("App\Entity\Commentes")->findAll();

        return $this->render('admin/comments.html.twig', [
            'title' => 'Liste des commentaires',
            'comments' => $comments
        ]);
    }

    /**
     * @var EntityManagerInterface $em
     *
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
     * @var EntityManagerInterface $em
     *
     * @param integer $id
     */
    public function validedComment(int $id)
    {
        $em = Manager::getInstance()->getEm();
        $comment = $em->getRepository("App\Entity\Commentes")->findOneBy(['id' => $id]);

        $comment->setIsValid(true);

        $em->merge($comment);
        $em->flush();

        return $this->redirect('/admin-comments');
    }
}
