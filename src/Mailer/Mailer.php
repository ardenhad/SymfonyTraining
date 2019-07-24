<?php


namespace App\Mailer;


use App\Entity\User;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig\Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $mailFrom = 'aaa@dugun.com';

    public function __construct(\Swift_Mailer $mailer, \Twig\Environment $twig, string $mailFrom)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;
    }

    public function sendConfirmationMail(User $user)
    {

        $body = $this->twig->render("email/registration.html.twig", [
            "user" => $user
        ]);

        $message = (new \Swift_Message())
            ->setSubject("Welcome to micro-post app!")
            ->setFrom($this->mailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, "text/html");

        $this->mailer->send($message);
    }
}