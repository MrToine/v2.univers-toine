<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\ForumCategory;
use App\Repository\ForumCategoryRepository;

use App\Entity\ForumForum;
use App\Repository\ForumForumRepository;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

class ForumHomeController extends AbstractController
{
    #[Route('/forum', name: 'forum.home')]
    public function index(
        ForumCategoryRepository $repositoryCat, 
        ForumForumRepository $repositoryForum, 
        ForumTopicRepository $repositoryTopic, 
        Request $request
        ): Response 
    {   
        /**
         * On récupère la liste des news dans un arrray en fixant une limite à 6 
         * @var array
         */
        
        $categories = $repositoryCat->createQueryBuilder('c')
            ->orderBy('c.position')
            ->getQuery()
            ->getResult();

        $forums = $repositoryForum->createQueryBuilder('f')
            ->orderBy('f.position')
            ->getQuery()
            ->getResult();

        $topic = $repositoryTopic->createQueryBuilder('t')
             ->orderBy('t.id', 'DESC')
             ->setMaxResults(1)
             ->getQuery()
             ->getOneOrNullResult();
        
        return $this->render('forum/home.html.twig', [
            'categories' => $categories,
            'forums' => $forums,
            'topic' => $topic
        ]);
    }
}
