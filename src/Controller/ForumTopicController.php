<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

class ForumTopicController extends AbstractController
{
    #[Route('/forum/topic/{id}', name: 'forum.topic', methods: ['GET'])]
        public function index(
        ForumTopic $topic,
        ForumTopicRepository $repositoryTopic,
        ForumPostRepository $repositoryPost, 
        Request $request
        ): Response 
    {   
        /**
         * On récupère la liste des topics dans un arrray en fixant une limite à 6 
         * @var array
         */

        $topic = $repositoryTopic->find(['id' => $topic]);

        $posts = $repositoryPost->createQueryBuilder('t')
             ->orderBy('t.id')
             ->getQuery()
             ->getResult();

        return $this->render('forum/posts_list.html.twig', [
            'topic' => $topic,
            'post' => $post
        ]);
    }
}
