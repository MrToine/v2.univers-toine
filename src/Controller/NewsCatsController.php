<?php

namespace App\Controller;

use App\Repository\NewsCatsRepository;
use App\Entity\NewsCats;
use App\Form\NewsCatsType;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

class NewsCatsController extends BaseController
{   
    
    /**
     * Cette méthode affiche toute les news
     * 
     * @param  NewsCatsRepository $repository
     * @param  PaginatorInterface $paginator
     * @param  Request $request
     * @return Response
     */
    #[Route('/news/categories', name: 'news.cat.index', methods: ['GET'])]
    public function index(NewsCatsRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        
        $cats = $paginator->paginate(
        $repository->findAll(), /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        5 /*limit per page*/
        );

        return $this->render($this->theme . '/news/categories/index.html.twig', [
            'cats' => $cats
        ]);
    }

    /**
     * Cette méthode sert à créer une catégorie de news et l'ajouter dans la base de données
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     */
    #[Route('/news/categories/add', name: 'create.news.cat', methods: ['GET', 'POST'])]
    public function add(
            Request $request,
            EntityManagerInterface $manager
        ): Response
    {
        $cat = new NewsCats();
        $form = $this->createForm(NewsCatsType::class, $cat);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $cat = $form->getData();
            $manager->persist($cat);
            $manager->flush();

            $this->addFlash(
                'success',
                'Catégorie créer avec succès'
            );


            return $this->redirectToRoute('news.cat.index');
        }

        return $this->render($this->theme . '/news/categories/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

     #[Route('/news/categorie/edit/{id}', name: 'edit.news.cat', methods: ['GET', 'POST'])]
    public function edit( 
            NewsCats $cat,
            Request $request,
            EntityManagerInterface $manager
        ): Response
    {

        $form = $this->createForm(NewsCatsType::class, $cat);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $cat = $form->getData();

            $manager->persist($cat);
            $manager->flush();

            $this->addFlash(
                'success',
                'Catégorie Mise à jour avec succès'
            );


            return $this->redirectToRoute('news.cat.index');
        }

        return $this->render($this->theme . '/news/categories/edit.html.twig', [
            'form' => $form->createView()
        ]);

         return $this->render($this->theme . '/news/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/news/categories/delete/{id}', name: 'delete.news.cat', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, NewsCats $cat): Response
    {
        $manager->remove($cat);
        $manager->flush();

        if(!$cat){

            $this->addFlash(
                'warning',
                'La catégorie n\'existe pas !'
            );


            return $this->redirectToRoute('news.cat.index');
        }
        $this->addFlash(
            'success',
            'Catégorie supprimée avec succès'
        );


        return $this->redirectToRoute('news.cat.index');
    }
}

