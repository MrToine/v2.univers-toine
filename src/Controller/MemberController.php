<?php
/**
 * La fonction editPassword() ne fonctionne pas pour le moment
 */
namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberType;
use App\Form\MemberPasswordType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;

class MemberController extends AbstractController
{
    #[Route('/users/{id}/edit', name: 'users.edit', methods: ['GET', 'POST'])]
    public function edit(Member $user,
        Request $request, 
        EntityManagerInterface $manager): Response
    {

        if(!$this->getUser())
        {
            return $this->redirectToRoute('users.login');
        }

        if($this->getUser() !== $user)
        {
            return $this->redirectToRoute('home.index');
        }

        $form = $this->createForm(MemberType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $this->addFlash(
                'success',
                'Les informations du compte on bien été modifiées.' 
            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('news.index');
        }

        return $this->render('users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/users/{id}/edit/password', name: 'users.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(Member $user, 
            Request $request,
            UserPasswordHasherInterface $hasher,
            EntityManagerInterface $manager,
        ): Response
    {   
        $form = $this->createForm(MemberPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($user, $form->getData()['plainPassword']))
            {
                $user->setPlainPassword($form->getData()['new_password']);
                $this->addFlash(
                'success',
                'Le mot de passe à bien été modifié' 
                );

                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute('news.index');
            }else{
                $this->addFlash(
                'error',
                'Le mot de passe est incorrect.');
            }
        }

        return $this->render('users/edit_password.html.twig', ['form' => $form->createView()]);
    }
}
