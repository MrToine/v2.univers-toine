<?php
/**
 * La fonction editPassword() ne fonctionne pas pour le moment. Pour la rendre possible, il faut qu'un des champs de la table soit mis à jour (modification_date par ex)
 */
namespace App\Controller;

use App\Entity\Member;
use App\Entity\Item;
use App\Repository\MemberRepository;
use App\Form\MemberType;
use App\Form\MemberPasswordType;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

class MemberController extends BaseController
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

        return $this->render($this->theme . '/users/edit.html.twig', [
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

        return $this->render($this->theme . '/users/edit_password.html.twig', ['form' => $form->createView()]);
    }

    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/users/{id}/edit/avatar/{name}', name: 'users.edit.avatar', methods: ['GET', 'POST'])]
    public function editAvatar(
            $name,
            Member $choosenUser, 
            Request $request,
            EntityManagerInterface $manager,
        ): Response
    {

        $choosenUser->setAvatarName($name);
        $manager->persist($choosenUser);
        $manager->flush();

        $this->addFlash('success', 'Ton avatar à été modifié.');

        return $this->redirectToRoute('users.profile', ['id' => $choosenUser->getId()]);
    }

    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/users/{id}/edit/theme/{name}', name: 'users.edit.theme', methods: ['GET', 'POST'])]
    public function editTheme(
            $name,
            Member $choosenUser, 
            Request $request,
            EntityManagerInterface $manager,
        ): Response
    {

        $choosenUser->setTheme(strtolower($name));
        $manager->persist($choosenUser);
        $manager->flush();

        $this->addFlash('success', 'Ton theme à été modifié.');

        return $this->redirectToRoute('users.profile', ['id' => $choosenUser->getId()]);
    }

    #[Route('/users/profile/{id}', name: 'users.profile', methods: ['GET', 'POST'])]
    public function viewProfile(
        MemberRepository $repository,
        Member $choosenUser,
        EntityManagerInterface $manager,
        Request $request, ): Response
    {
        $user = $repository->find(['id' => $choosenUser]);
        
        $avatars = null;
        $themes = null;
        
        
        if($choosenUser === $this->getUser())
        {
            $avatarDir = 'uploads/avatars/' . $choosenUser->getId();

            $fullPath = getcwd() . '/' . $avatarDir . '/';

            $finder = new Finder();

            $finder->files()->in($fullPath)->name(['*.jpg', '*.jpeg', '*.png', '*.gif']);

            $avatars = iterator_to_array($finder);

        }

        $items = $user->getItem()->matching(Criteria::create());

        $topicRepository = $manager->getRepository(ForumTopic::class);
        $countTopics = $topicRepository->count(['author' => $choosenUser]);

        $postRepository = $manager->getRepository(ForumPost::class);
        $countMessages = $postRepository->count(['author' => $choosenUser]);


        return $this->render($this->theme . '/users/profile.html.twig', [
            'user' => $user, 
            "items" => $items,
            'avatars' => $avatars,
            'nbTopics' => $countTopics,
            'nbMessages' => $countMessages,
        ]);
    }
}
