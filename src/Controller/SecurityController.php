<?php

namespace App\Controller;

use App\Entity\Member;

use App\Form\RegistrationType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    
    
    /**
     * Permet de connecter un utilisateur à partir d'un formulaire de connexion
     * 
     * @param  AuthenticationUtils $authencationUtils []
     * @return render->(view)                         []
     */
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

    
    /**
     * Permet de créer un utilisateur à parti d'un formulaire d'inscription
     * 
     * @param  Request                $request 
     * @param  EntityManagerInterface $manager 
     * @return render->(view)                          
     */
    
    #[Route('users/register', name: 'users.register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $manager): Response
    {
        $user = New Member();
        $user->setLevel(1);
        $user->setRoles(["ROLE_USER"]);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $this->addFlash(
                'success',
                'Le compte à bien été enregistré, vous pouvez dès à présent vous connecter.' 
            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('users.login');
        }

        return $this->render('users/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
