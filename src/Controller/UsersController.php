<?php 

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Images;
use Zend\Crypt\Password\Bcrypt;
use App\Exception\UserException;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;
use App\Models\ImagesManager;
use App\Models\UsersManager;
use Exception;

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
        try {
            // Si le formulaire n'est pas vide et que les conditions d'utilisation ont été accepté
            if (! empty($_POST) && $_POST['condition'] === "on") {

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
                if ((new UsersManager())->userVerify($user->getEmail())) {
                    throw new UserException("Cet email est déja utilisé");
                }
                
                (new UsersManager())->insert($user);
                
                $this->addFlash(
                    'success',
                    'Bienvenu parmi nous!'
                );

                return $this->redirect('/login');
            }
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
        
        return $this->render('register.html.twig', [
            'title' => 'Inscription',
        ]);
    }

    /**
     * Permet de modifier son compte
     * @var Users $user
     */
    public function updateAccount()
    {
        try {
            if ($this->getUser()) {

                if (! empty($_POST) && $this->csrfVerify($_POST)) {
    
                    $user = (new UsersManager())->userVerify($this->getUser()['email']);
                    
                    // Je vérifie si l' utilisateur a changé d'email et que ce mail n'existe déja pas dans la base de donnée
                    if ($user['email'] !== $_POST['email'] && empty(! (new UsersManager())->userVerify(htmlspecialchars($_POST['email'])))) {
                        throw new UserException("Cet email est déja utilisé");
                    }
                    
                    // Je récupère l'image précédant
                    $imageId = (int) $user['images_id'];

                    $image = (new ImagesManager())->getUserImage($imageId);

                    // Je traite l'image envoyée s'il en a
                    if ($_FILES['avatar']['error'] === 0) {
                        if ($image !== null) {
                            unlink(dirname(__DIR__, 2).'/public/img/avatars/'.$image['name']);
                        }
                        
                        $this->uplodeFile($_FILES['avatar'], $image);
                    }

                    // Je procède à la validation  des types de données
                    $user = (new Users())
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setUpdatedAt(new \DateTime());
                    
                    (new UsersManager())->update($user);
                    
                    // Je met a jour le user dans la session
                    $_SESSION['user'] = [
                        'id' => $this->getUser()['id'],
                        'token' => uniqid('blog'),
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                        'email' => $user->getEmail(),
                        'role' => $user->getRole(),
                        'is_connected' => true
                    ];

                    $this->addFlash(
                        'success',
                        'Votre profile a été correctement modifié!'
                    );
    
                    return $this->redirect('/profil');
                    
                }
    
                return $this->render('update_account.html.twig', [
                    'title' => 'Modifier mon compte',
                    'user' => $this->getUser()
                ]);
            }
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
        return $this->redirect('/login');
    }

    /**
     * Permet de modifier le mot de passe
     * @var Users|null $user
     */
    public function updatePassword()
    {
        try {
            if ($this->getUser()) {

                if (! empty($_POST) && $this->csrfVerify($_POST)) {
    
                    $bcrypt = new Bcrypt();
                    $user = (new UsersManager())->userVerify($this->getUser()['email']);
    
                    //Si il y a un utilisateur et que le mot de passe ne correspond pas
                    if (! $bcrypt->verify(htmlspecialchars($_POST['lastPassword']), $user['password'])) {
                        throw new UserException("L'ancien mot de passe est erronné");
                    }
    
                    //Si les mot de passe ne correspondent pas
                    if (htmlspecialchars($_POST['newPassword']) !== htmlspecialchars($_POST['confirmNewPassword'])) {
                        throw new UserException("Les deux mots de passes sont divergeants");
                    }
    
                    $userUpdate = (new Users())
                    ->setPassword(htmlspecialchars($_POST['newPassword']))
                    ->setUpdatedAt(new \DateTime());
    
                    (new UsersManager())->updatePassword($user['id'], $userUpdate);

                    $this->addFlash(
                        'success',
                        'Votre mot de passe a été correctement modifié !'
                    );
    
                    return $this->redirect('/profil');
                }
            
                return $this->render('update_password.html.twig', [
                    'title' => 'Modifier mon mot de passe'
                ]);
            }
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->redirect('/login');
    }

    /**
     * Permet le upload de fichier jpeg
     *
     * @param FILES $file
     * @return null|int
     */
    private function uplodeFile($file, $lastImage = null): ?int
    {
        try {
            $handle = new \Verot\Upload\Upload($file);

            if ($handle->uploaded) {

                $handle->file_new_name_body   = uniqid("avatar");
                $handle->image_convert = 'jpg';
                $handle->allowed = array('image/*');
                $name = $handle->file_new_name_body.'.jpg';
                $handle->image_resize         = true;
                $handle->image_x              = 100;
                $handle->image_ratio_y        = true;
                $handle->process('../public/img/avatars');

                if ($handle->processed) {

                    $image = (new Images())->setName($name)->setUpdatedAt(new \DateTime());

                    if ($lastImage !== null) {
                        return (new ImagesManager())->update($image, $lastImage);
                    }

                    return (new ImagesManager())->insert($image);
                }

                return '';
            }
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
    }

    /**
     * Permet de se connecter sur le site
     * @var Users|null $user
     */
    public function login()
    {
        try {
            if (! empty($_POST) && $this->csrfVerify($_POST)) {

                // Je vérifie si un utilisateur a cet email
                $user = (new UsersManager())->userVerify(htmlspecialchars($_POST['email']));
                $bcrypt = new Bcrypt();
                
                //Si il y a un utilisateur, je vérifie que son mot de passe est valide
                if (! empty($user) && $bcrypt->verify(htmlspecialchars($_POST['password']), $user['password'])) {
                    
                    // Start session
                    session_start();

                    // J'initialise le flash bag
                    $_SESSION['flashes'] = [];

                    // Je met le user dans la session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'token' => uniqid('blog'),
                        'firstName' => $user['first_name'],
                        'lastName' => $user['last_name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'is_connected' => true
                    ];

                    return $this->redirect('/');
                }
        
                //Si il y a un utilisateur et que le mot de passe ne correspond pas
                if (! empty($user) && !$bcrypt->verify(htmlspecialchars($_POST['password']), $user['password'])) {
                    throw new UserException("Email ou mot de passe erronné");
                }
    
                // Si un utilisateur ne correspond pas
                if (empty($user)) {
                    throw new UserException("Cet email n'est pas sur notre site. Inscrivez vous!");
                }
            }
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->render('connexion.html.twig', [
            'title' => 'Veuillez vous connecter',
        ]);
    }

    /**
     * Permet de se connecter sur le site
     * @var Users|null $user
     */
    public function adminLogin()
    {
        try {
            if (! empty($_POST) && $this->csrfVerify($_POST)) {
                // Je vérifie si un utilisateur a cet email
                $user = (new UsersManager())->userVerify(htmlspecialchars($_POST['email']));
                $bcrypt = new Bcrypt();
                
                //Si il y a un utilisateur, je vérifie que son mot de passe est valide
                if (! empty($user) && $user['role'] == Users::ROLE_ADMIN && $bcrypt->verify(htmlspecialchars($_POST['password']), $user['password'])) {
                    // J'initialise le flash bag
                    $_SESSION['flashes'] = [];

                    // Je met le user dans la session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'token' => uniqid('blog'),
                        'firstName' => $user['first_name'],
                        'lastName' => $user['last_name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'is_connected' => true
                    ];
                    
                    return $this->redirect('/admin');
                }
        
                //Si il y a un utilisateur et que le mot de passe ne correspond pas
                if (! empty($user) && ! $bcrypt->verify(htmlspecialchars($_POST['password']), $user['password'])) {
                    throw new UserException("Email ou mot de passe erronné");
                }
    
                // Si un utilisateur ne correspond pas
                if (empty($user)) {
                    throw new UserException("Cet email n'est pas sur notre site. Inscrivez vous!");
                }
            }
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }

        return $this->render('admin/connexion.html.twig', [
            'title' => 'Veuillez vous connecter',
        ]);
    }

    public function profil()
    {
        return $this->render('account.html.twig', [
            'title' => 'Mon compte'
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

        return $this->redirect('/');
    }
}
