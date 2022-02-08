<?php

namespace App\Controller\Administrator;

use Exception;
use App\Entity\Posts;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use App\Entity\Users;
use App\Models\PostsManager;

class AdminPostsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function postList(int $currentPage)
    {
        try {
            $sql = 'SELECT * FROM posts';
            $posts = (new PostsManager())->listes($sql);
 
            $totalItems = count($posts);
            $itemsPerPage = 2;
            $neighbours = 4;
            
            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $limit = $pagination->limit();
            $offset = $pagination->offset();
            
            $posts = (new PostsManager())->pagination($sql, $limit, $offset, Users::ROLE_ADMIN);
            
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

                    // Je vérifie si le nouveau titre envoyé n'est pas déja utilisé
                    if ((new PostsManager())->testDoubleTitle($post->getTitle())) {
                        throw new Exception("Cet Titre est déja utilisé!", 1);
                    }

                //je sauvegarde l'article
                (new PostsManager())->insert($post);

                $this->addFlash(
                    'success',
                    'L\'article a été correctement ajouté!'
                );
    
                return $this->redirect("/admin-posts");
                
            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }

        return $this->render('admin/new_post.html.twig', [
            'title' => 'Ajouter un nouveau poste'
        ]);
    }

    /**
     * @param integer $id
     */
    public function updatePost(int $id)
    {
        $lastPost = (new PostsManager())->getPost($id);

        if (! empty($_POST) && $this->csrfVerify($_POST)) {
            try {
                $post = (new Posts())
                    ->setTitle($_POST['title'])
                    ->setIntro($_POST['intro'])
                    ->setContent($_POST['content'])
                    ->setStatus($_POST['status'])
                    ->setUpdatedAt(new \DateTime())
                    ;
                $testTitle = (new PostsManager())->testDoubleTitle($post->getTitle());
                // Je vérifie si le nouveau titre envoyé n'est pas déja utilisé
                if (!empty($testTitle) && $lastPost['id'] !== $testTitle['id']) {
                    throw new Exception("Cet Titre est déja utilisé!", 1);
                }
                
                (new PostsManager())->update($lastPost, $post);

                $this->addFlash(
                    'success',
                    'L\'article a été correctement modifié! vérifiez...'
                );

                return $this->redirect('/post-'.$lastPost['id']);

            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }

        return $this->render('admin/update_post.html.twig', [
            'title' => 'Modifier ce blog post',
            'post' => $lastPost
        ]);
    }
}
