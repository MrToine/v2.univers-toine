<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumForumController extends AbstractController
{
    #[Route('/forum/forum', name: 'app_forum_forum')]
    public function index(): Response
    {
        return $this->render('forum_forum/index.html.twig', [
            'controller_name' => 'ForumForumController',
        ]);
    }
}
