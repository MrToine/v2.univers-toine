<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use App\Entity\News;
use App\Form\NewsType;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Doctrine\ORM\EntityManagerInterface;

class NewsController extends AbstractController
{   
    
    /**
     * Cette méthode affiche toute les news
     * 
     * @param  NewsRepository $repository
     * @param  PaginatorInterface $paginator
     * @param  Request $request
     * @return Response
     */
    #[Route('/news', name: 'news.index', methods: ['GET'])]
    public function index(NewsRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        
        $news = $paginator->paginate(
        $repository->findAll(), /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        5 /*limit per page*/
        );

        return $this->render('news/index.html.twig', [
            'news' => $news
        ]);
    }

    #[Route('/news/my', name: 'news.users.index', methods: ['GET'])]
    public function newsById(NewsRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $news = $paginator->paginate(
        $repository->findBy(['author' => $this->getUser()]), /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        5 /*limit per page*/
        );

        return $this->render('news/index.html.twig', [
            'news' => $news
        ]);
    }

    
    /**
     * Cette méthode sert à créer une news et l'ajouter dans la base de données
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     */
    #[Route('/news/add', name: 'create.news', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(
            Request $request,
            EntityManagerInterface $manager
        ): Response
    {

        $news = new News();

        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $news = $form->getData();
            $news->setAuthor($this->getUser());
            $manager->persist($news);
            $manager->flush();

            $this->addFlash(
                'success',
                'News créer avec succès'
            );


            return $this->redirectToRoute('news.index');
        }

        return $this->render('news/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/news/edit/{id}', name: 'edit.news', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit( 
            News $news,
            Request $request,
            EntityManagerInterface $manager
        ): Response
    {

        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $news = $form->getData();

            $manager->persist($news);
            $manager->flush();

            $this->addFlash(
                'success',
                'News Mise à jour avec succès'
            );


            return $this->redirectToRoute('news.index');
        }

        return $this->render('news/add.html.twig', [
            'form' => $form->createView()
        ]);

         return $this->render('news/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/news/delete/{id}', name: 'delete.news', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, News $news): Response
    {
        $manager->remove($news);
        $manager->flush();

        if(!$news){

            $this->addFlash(
                'warning',
                'La news n\'existe pas !'
            );


            return $this->redirectToRoute('news.index');
        }
        $this->addFlash(
            'success',
            'News supprimée avec succès'
        );


        return $this->redirectToRoute('news.index');
    }

    #[Route('/news/{id}', name: 'news.view', methods: ['GET'])]
    public function showNews(
        NewsRepository $repository,
        News $news,
        Request $request, ): Response
    {
        $news = $repository->find(['id' => $news]);
        
        return $this->render('news/view.html.twig', ['news' => $news]);
    }

}
