<?php 

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Images;
use App\Repository\Manager;
use Zend\Crypt\Password\Bcrypt;
use App\Exception\UserException;
use App\Controller\AbstractController;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
class UsersController extends AbstractController
{
    /**
     * Permet de s'inscrire sur le site
     */
    public function registration()
    {
        // Si le formulaire n'est pas vide et que les conditions d'utilisation ont été accepté
        if (!empty($_POST) && $_POST['condition'] === "on") {
            
            // Je traite l'image envoyée
            $image = $this->uplodeFile($_FILES['avatar']);
            
            // Je procède à la validation des données 
            $user = new Users();
            $user->setFirstName($_POST['firstName'])
                ->setLastName($_POST['lastName'])
                ->setEmail($_POST['email'])
                ->setPassword($_POST['password'])
                ->setImages($image);

            
            // Je vérifie si un utilisateur existe déja avec ce email et je renvois une error
            if ($this->userVerify($user->getEmail())) {
                throw new UserException("Cet email est déja utilisé");
            }

            // Sinon je sauvegarde le nouveau utilisateur
            $em = Manager::getInstance()->getEm();
            $em->persist($user);
            $em->flush();

            return $this->redirect('/');
        }
        
        return $this->render('register.html.twig', [
            'title' => 'Inscription',
        ]);
    }

    /**
     * Permet de modifier son compte
     */
    public function updateAccount()
    {
        if ($this->getUser()) {

            if (!empty($_POST)) {

                /** @var Users $user */
                $user = $this->userVerify(htmlspecialchars($this->getUser()['email']));
                
                // Je vérifie si l' utilisateur a changé d'email et que ce mail n'existe déja pas dans la base de donnée
                if ($user->getEmail() !== $_POST['email'] && empty(! $this->userVerify(htmlspecialchars($_POST['email'])))) {
                    throw new UserException("Cet email est déja utilisé");
                }

                // Je récupère l'image précédant
                $image = $user->getImages();

                // Je traite l'image envoyée s'il en en a
                if (!empty($_FILES['avatar'])) {
                    unlink(dirname(__DIR__, 2).'/public/img/avatars/'.$image->getName().'.jpg');
                    $image = $this->uplodeFile($_FILES['avatar']);
                }

                // Je procède à la  des types de données 
                $user->setFirstName($_POST['firstName'])
                    ->setLastName($_POST['lastName'])
                    ->setEmail($_POST['email'])
                    ->setUpdatedAt(new \DateTime())
                    ->setImages($image);
                
                //je sauvegarde l'utilisateur
                $em = Manager::getInstance()->getEm();
                $em->merge($user);
                $em->flush();
                
                // Je met a jour le user dans la session
                $_SESSION['user'] = [
                    'id' => $user->getId(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'is_connected' => true
                ];

                return $this->redirect('/');
                
            }

            return $this->render('update_account.html.twig', [
                'title' => 'Modifier mon compte',
                'user' => $this->getUser()
            ]);
        }

        return null;
    }

    /**
     * Permet de modifier le mot de passe
     */
    public function updatePassword()
    {
        if ($this->getUser()) {

            if (! empty($_POST)) {

                $bcrypt = new Bcrypt();
                $user = $this->userVerify($this->getUser()['email']);

                //Si il y a un utilisateur et que le mot de passe ne correspond pas
                if (!$bcrypt->verify(htmlspecialchars($_POST['lastPassword']), $user->getPassword())) {
                    throw new UserException("Mot de passe erronné");
                }

                //Si les mot de passe ne correspondent pas
                if (htmlspecialchars($_POST['newPassword']) !== htmlspecialchars($_POST['confirmNewPassword'])) {
                    throw new UserException("Les deux mots de passes sont divergeants");
                }

                $user->setPassword(htmlspecialchars($_POST['newPassword']));

                //je sauvegarde l'utilisateur
                $em = Manager::getInstance()->getEm();
                $em->merge($user);
                $em->flush();

                session_destroy();

                return $this->redirect('/');
            }
        
            return $this->render('update_password.html.twig', [
                'title' => 'Modifier mon mot de passe'
            ]);
        }

        return null;
    }

    /**
     * Permet le upload de fichier jpeg
     *
     * @param __FILE__ $file
     */
    private function uplodeFile($file)
    {
        $handle = new \Verot\Upload\Upload($file);
        if ($handle->uploaded) {
            $handle->file_new_name_body   = uniqid("avatar");
            $handle->image_convert = 'jpg';
            $handle->allowed = array('image/*');
            $name = $handle->file_new_name_body;
            $handle->image_resize         = true;
            $handle->image_x              = 100;
            $handle->image_ratio_y        = true;
            $handle->process('../public/img/avatars');
            if ($handle->processed) {
                return (new Images())->setName($name);
            } else {
                echo 'error : ' . $handle->error;
            }
        }
    }

    /**
     * Permet de se connecter sur le site
     */
    public function login()
    {
        if (!empty($_POST)) {

            // Je vérifie si un utilisateur a cet email
            $user = $this->userVerify(htmlspecialchars($_POST['email']));
            $bcrypt = new Bcrypt();

            //Si il y a un utilisateur, je vérifie que son mot de passe est valide
            if (!empty($user) && $bcrypt->verify(htmlspecialchars($_POST['password']), $user->getPassword())) {
                
                // Je met le user dans la session
                $_SESSION['user'] = [
                    'id' => $user->getId(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'is_connected' => true
                ];
                
                return $this->redirect('/');
            }
    
            //Si il y a un utilisateur et que le mot de passe ne correspond pas
            if (!empty($user) && !$bcrypt->verify(htmlspecialchars($_POST['password']), $user->getPassword())) {
                throw new UserException("Email ou mot de passe erronné");
            }

            // Si un utilisateur ne correspond pas
            if (empty($user)) {
                throw new UserException("Cet email n'est pas sur notre site. Inscrivez vous!");
            }
        }

        return $this->render('connexion.html.twig', [
            'title' => 'Veuillez vous connecter',
        ]);
    }

    /**
     * Permet de se deconnecter du site
     *
     * @return mixed
     */
    public function logout()
    {
        session_destroy();

        $_SESSION['user'] = [
            'is_connected' => false
        ];
        
        return $this->redirect('/');
    }

    /**
     * Permet de verifier si un utilisateur existe
     *
     * @param string $email
     */
    private function userVerify(string $email)
    {
        /** @var EntityManagerInterface */
        $em = Manager::getInstance()->getEm();
        return $em->getRepository('App\Entity\Users')->findOneBy(['email' => htmlspecialchars($email)]);
    }
}
