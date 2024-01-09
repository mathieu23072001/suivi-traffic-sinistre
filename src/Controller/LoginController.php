<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'variable' => false,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function someAction(Security $security): Response
    {
//        // logout the user in on the current firewall
//        $response = $security->logout();
//
//        // you can also disable the csrf logout
//        $response = $security->logout(false);

        return $this->redirectToRoute('app_acceuil');
    }
}
