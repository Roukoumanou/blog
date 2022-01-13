<?php

namespace App\Controller;

use Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use App\Controller\AbstractController;
use App\Controller\Exception\ExceptionController;

class ContactController extends AbstractController
{
    public function contactMe()
    {
        $mail = $this->getMailler();

        if (! empty($_POST)) {
            try {
                //Recipients
                $subject = htmlspecialchars($_POST['subject']);
                $userMail = filter_var(htmlspecialchars($_POST['email']), FILTER_VALIDATE_EMAIL);
                $userFullName = htmlspecialchars($_POST['fullName']);
                $content = htmlspecialchars($_POST['message']);

                $mail->setFrom($userMail, $userFullName);
                $mail->addAddress('roukoumanouamidou@gmail.com', 'Amidou Abdou Roukoumanou');
                
                //Content
                $mail->isHTML(true);
                $mail->Subject = 'Message de '.$userFullName.' | '.$subject;
                $mail->Body    = nl2br($content);
                
                $mail->send();

                return $this->redirect('/');
                
            } catch (Exception $e) {
                return (new ExceptionController())->error500($e->getMessage());
            }
        }

        return $this->render('contact.html.twig', [
            'title' => 'Contactez-moi'
        ]);
    }

    private function getMailler()
    {
        $mail = new PHPMailer(true);
        try {

            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->Port       = 587;

            return $mail;
        } catch (Exception $e) {
            return (new ExceptionController())->error500($e->getMessage());
        }
    }
}