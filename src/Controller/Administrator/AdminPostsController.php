<?php

namespace App\Controller\Administrator;

use Exception;
use App\Entity\Posts;
use App\Repository\Manager;
use Kilte\Pagination\Pagination;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;

class AdminPostsController extends AbstractController
{
    /**
     * @param integer $currentPage
     */
    public function postList(int $currentPage)
    {
        try {
            $em = $this->getDB();
            $sql = 'SELECT * FROM posts';
            $result = $em->prepare($sql);
            $result->execute([]);
            $posts = $result->fetchAll();

            $totalItems = count($posts);
            $itemsPerPage = 2;
            $neighbours = 4;

            $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
            $limit = $pagination->limit();
            $offset = $pagination->offset();

            $req = $em->prepare($sql.' ORDER BY id DESC LIMIT '.$limit.' OFFSET '.$offset);
            $req->execute([':public' => Posts::PUBLISHED]);

            $posts = $req->fetchAll();

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
                    if ($this->testDoubleTitle($post->getTitle())) {
                        throw new Exception("Cet Titre est déja utilisé!", 1);
                    }

                //je sauvegarde l'article
                $em = $this->getDb();
                $result = $em->prepare("INSERT INTO posts (title, intro, status, content, created_by, created_at, updated_at)
                            VALUES (:title, :intro, :status, :content, :created_by, :created_at, :updated_at)");
                $result->execute([
                    ':title' => $post->getTitle(),
                    ':intro' => $post->getIntro(),
                    ':status' => $post->getStatus(),
                    ':content' => $post->getContent(),
                    ':created_by' => $post->getCreatedBy(),
                    ':created_at' => date_format($post->getCreatedAt(), 'Y-m-d'),
                    ':updated_at' => date_format($post->getUpdatedAt(), 'Y-m-d')
                ]);

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
        $lastPost = $this->getPost($id);

        if (! empty($_POST) && $this->csrfVerify($_POST)) {
            try {
                $post = (new Posts())
                    ->setTitle($_POST['title'])
                    ->setIntro($_POST['intro'])
                    ->setContent($_POST['content'])
                    ->setStatus($_POST['status'])
                    ->setUpdatedAt(new \DateTime())
                    ;
                $testTitle = $this->testDoubleTitle($post->getTitle());
                // Je vérifie si le nouveau titre envoyé n'est pas déja utilisé
                if (!empty($testTitle) && $lastPost['id'] !== $testTitle['id']) {
                    throw new Exception("Cet Titre est déja utilisé!", 1);
                }
                
                //je sauvegarde le post
                $em = $this->getDB();
                $result = $em->prepare("UPDATE posts SET title = :title, intro = :intro, status = :status, content = :content, updated_at = :updated_at
                                        WHERE id = :id");
                $result->execute([
                    ':id' => $lastPost['id'],
                    ':title' => $post->getTitle(),
                    ':intro' => $post->getIntro(),
                    ':status' => $post->getStatus(),
                    ':content' => $post->getContent(),
                    ':updated_at' => date_format($post->getUpdatedAt(), 'Y-m-d')
                ]);

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


    /**
     * @param integer $id
     */
    public function deletePost(int $id)
    {
        $lastPost = $this->getPost($id);
        var_dump($lastPost);

        if (! empty($_POST) && $this->csrfVerify($_POST)) {
            try {
                $em = $this->getDB();
                $comment = $em->prepare("DELETE FROM commentes WHERE post_id = :id");
                $comment->execute(['id' => $id]);

                $post = $em->prepare("DELETE FROM posts WHERE id = :id");
                $post->execute([':id' => $id]);



                //Je supprime le post
                
                $this->addFlash(
                    'success',
                    'L\'article a été correctement supprimé!'
                );
    
                return $this->redirect('/admin-posts');
            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }
    }

    /**
     * @param integer $id
     */
    private function getPost(int $id)
    {
        try {
            $em = $this->getDB();
            $result = $em->prepare("SELECT * FROM posts WHERE id = :id");
            $result->execute([':id' => $id]);

            $response = $result->fetch();
            return $response;
            
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
    }

    private function testDoubleTitle(string $title)
    {
        $em = $this->getDB();
        $result = $em->prepare("SELECT * FROM posts WHERE title = :title");
        $result->execute([':title' => $title]);

        $response = $result->fetch();
        return $response;
    }
}
