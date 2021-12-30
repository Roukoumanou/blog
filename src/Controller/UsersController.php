<?php 

namespace App\Controller;

use App\Entity\Users;
use Zend\Crypt\Password\Bcrypt;
use App\Exception\UserException;
use App\Repository\UsersRepository;
use App\Controller\AbstractController;

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
            
            // Je procède à la validation des données 
            $user = new Users();
            $user->setFirstName($_POST['firstName'])
                ->setLastName($_POST['lastName'])
                ->setEmail($_POST['email'])
                ->setPassword($_POST['password']);

            // Je vérifie si un utilisateur existe déja avec ce email et je renvois une error
            if ($this->userVerify($user->getEmail())) {
                throw new UserException("Cet email est déja utilisé");
            }

            // Sinon je sauvegarde le nouveau utilisateur
            (new UsersRepository())->register($user);

            return $this->redirect('/');
        }
        
        return $this->render('register.html.twig', [
            'title' => 'Inscription',
        ]);
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
            if (!empty($user) && $bcrypt->verify(htmlspecialchars($_POST['password']), $user->password)) {
                
                // Je met le user dans la session
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'firstName' => $user->first_name,
                    'lastName' => $user->last_name,
                    'email' => $user->email,
                    'is_connected' => true
                ];
                
                return $this->redirect('/');
            }
    
            //Si il y a un utilisateur et que le mot de passe ne correspond pas
            if (!empty($user) && !$bcrypt->verify(htmlspecialchars($_POST['password']), $user['password'])) {
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
     * @return void
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
        return (new UsersRepository())->getUser($email);
    }
}
