<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('users/login', name: 'users.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authencationUtils): Response
    {

        return $this->render('users/login.html.twig', [
            'last_username' => $authencationUtils->getLastUsername(),
            'error' => $authencationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('users/logout', name: 'users.logout')]
    public function logout()
    {
        //Nothing to do here
    }
}
