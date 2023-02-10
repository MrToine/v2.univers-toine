<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumCategoryController extends AbstractController
{
    #[Route('/forum/category', name: 'app_forum_category')]
    public function index(): Response
    {
        return $this->render('forum_category/index.html.twig', [
            'controller_name' => 'ForumCategoryController',
        ]);
    }
}
