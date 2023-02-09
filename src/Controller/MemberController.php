<?php
/**
 * La fonction editPassword() ne fonctionne pas pour le moment. Pour la rendre possible, il faut qu'un des champs de la table soit mis à jour (modification_date par ex)
 */
namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Form\MemberType;
use App\Form\MemberPasswordType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;

class MemberController extends AbstractController
{
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/users/{id}/edit', name: 'users.edit', methods: ['GET', 'POST'])]
    public function edit(
        Member $choosenUser,
        Request $request, 
        EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(MemberType::class, $choosenUser);

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

    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/users/{id}/edit/password', name: 'users.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(Member $choosenUser, 
            Request $request,
            UserPasswordHasherInterface $hasher,
            EntityManagerInterface $manager,
        ): Response
    {
           
        $form = $this->createForm(MemberPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword']))
            {
                $choosenUser->setUpdatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword($form->getData()['new_password']);
                $this->addFlash(
                'success',
                'Le mot de passe à bien été modifié' 
                );

                $manager->persist($choosenUser);
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

    #[Route('/users/profile/{id}', name: 'users.profile', methods: ['GET', 'POST'])]
    public function viewProfile(
        MemberRepository $repository,
        Member $choosenUser,
        Request $request, ): Response
    {
        $user = $repository->find(['id' => $choosenUser]);
        
        return $this->render('users/profile.html.twig', ['user' => $user]);
    }
}
