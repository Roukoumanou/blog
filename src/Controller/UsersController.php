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
    public function register()
    {
        if (!empty($_POST) && $_POST['condition'] === "on") {
            
            $user = new Users();
            $user->setFirstName($_POST['firstName'])
                ->setLastName($_POST['lastName'])
                ->setEmail($_POST['email'])
                ->setPassword($_POST['password']);

            if ($this->userVerify($user->getEmail())) {
                throw new UserException("Cet email est déja utilisé");
            }

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
    public function connexion()
    {
        if (!empty($_POST)) {
            $user = $this->userVerify(htmlspecialchars($_POST['email']));
            $bcrypt = new Bcrypt();

            if (!empty($user) && $bcrypt->verify(htmlspecialchars($_POST['password']), $user->password)) {
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'firstName' => $user->first_name,
                    'lastName' => $user->last_name,
                    'email' => $user->email,
                    'is_connected' => true
                ];
                
                return $this->redirect('/');
            }
    
            if (!empty($user) && !$bcrypt->verify(htmlspecialchars($_POST['password']), $user['password'])) {
                throw new UserException("Email ou mot de passe erronné");
            }

            if (empty($user)) {
                throw new UserException("Cet email n'est pas sur notre site. Inscrivez vous!");
            }
        }

        return $this->render('connexion.html.twig', [
            'title' => 'Veuillez vous connecter',
        ]);
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