<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;

use App\Entity\Item;
use App\Repository\ItemRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class ShopController extends BaseController
{
    #[Route('/shop', name: 'shop.home')]
    #[Security("is_granted('ROLE_USER')")]
    public function index(): Response
    {
        return $this->render($this->theme . '/shop/index.html.twig', [
            'controller_name' => 'ShopController',
        ]);
    }

    #[Route('/shop/themes', name: 'shop.themes')]
    #[Security("is_granted('ROLE_USER')")]
    public function themes(
        ItemRepository $itemRepository,
    ): Response
    {
        $themes = $itemRepository->createQueryBuilder('i')
            ->where("i.type = 'theme'")
            ->getQuery()
            ->getResult();

        return $this->render($this->theme . '/shop/themes.html.twig', [
            'themes' => $themes,
        ]);
    }

    #[Route('/shop/avatar', name: 'shop.avatar')]
    #[Security("is_granted('ROLE_USER')")]
    public function avatar(
        ItemRepository $itemRepository,
    ): Response
    {
        $avatars = $itemRepository->createQueryBuilder('i')
            ->where("i.type = 'avatar'")
            ->getQuery()
            ->getResult();

        return $this->render($this->theme . '/shop/avatar.html.twig', [
            'avatars' => $avatars,
        ]);
    }

    #[Route('/shop/buy/{id}', name: 'shop.buy', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
    public function buy(
            Item $item,
            Request $request,
            MemberRepository $memberRepository,
            EntityManagerInterface $manager,
            KernelInterface $kernel,
        ): Response
    {
        /*$csrfTokenManager = new CsrfTokenManager();
        $token = $request->query->get('token');
        $isTokenValid = $csrfTokenManager->isTokenValid(new CsrfToken('shop', $token));

        if (!$isTokenValid) {
            die('Invalid CSRF token => ' . $token . '<br> Token attendu => '. $csrfTokenManager->getToken('token'));
        }

        die('token valid!! ' . $token);*/

        if($this->getUser()->getMoney() >= $item->getPrice())
        {
            $user = $memberRepository->find(['id' => $this->getUser()]);
            if (!$user->hasItem($item->getId())) 
            {
                if($item->getType() === 'theme')
                {
                    $user->setTheme(strtolower($item->getName()));
                }

                if($item->getType() === 'avatar')
                {

                    // Crée un objet File à partir du fichier source
                    $sourceFile = new File($item->getImg());

                    $sourcePath = $sourceFile->getRealPath();

                    // Génère un nom unique pour le fichier destination
                    $destinationFilename = md5(uniqid()) . '.' . $sourceFile->guessExtension();

                    // Définit le chemin complet du fichier destination
                    $destinationPath = $kernel->getProjectDir() . '/public/uploads/avatars/' . $this->getUser()->getId() . '/' . $destinationFilename;

                    // Copie le fichier source vers le fichier destination
                    $filesystem = new Filesystem();
                    $filesystem->copy($sourcePath, $destinationPath);

                    // Crée un objet UploadedFile à partir du fichier destination
                    $uploadedFile = new UploadedFile($destinationPath, $destinationFilename);
                    
                    $this->getUser()->setAvatarName($destinationFilename);
                }

                $user->addItem($item);
                $user->setMoney($user->getMoney() - $item->getPrice());
                $manager->persist($user);
                $manager->flush();
                
                $this->addFlash(
                    'success',
                    'Tu as acheter l\'item ' . $item->getName() . ' avec succès !'
                );
            } 
            else 
            {
                $this->addFlash(
                    'danger',
                    'Tu possèdes déjà cet item.'
                );
            }

        }
        else
        {
            $this->addFlash(
                'danger',
                'Tu n\'as pas suffisament d\'or...'
            );
        }


        return $this->redirectToRoute('shop.themes');
    }
}
