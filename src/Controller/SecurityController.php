<?php


namespace App\Controller;


use App\Event\UserRegisterEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(\Twig\Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return new Response($this->twig->render(
            "security/login.html.twig",
            [
                "last_username" => $authenticationUtils->getLastUsername(),
                "error" => $authenticationUtils->getLastAuthenticationError()
            ]
        ));
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
        //doNothing. We just made this because security.yaml needs to redirect somewhere?
    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     */
    public function confirm(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->findOneBy([
            "confirmationToken" => $token
        ]);

        if (null !== $user) {
            $user->setEnabled(true);
            $user->setConfirmationToken("");

            $entityManager->flush();
        }

        return new Response($this->twig->render("security/confirmation.html.twig", ["user" => $user]));
    }
}