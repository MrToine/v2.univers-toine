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
        
        $categories = $repositoryCat->createQueryBuilder('c')
            ->orderBy('c.position')
            ->getQuery()
            ->getResult();

        $forums = $repositoryForum->createQueryBuilder('f')
            ->orderBy('f.position')
            ->getQuery()
            ->getResult();
        
        $topics = [];
        foreach ($forums as $f) {
            $topics[$f->getId()] = $repositoryTopic->createQueryBuilder('t')
                ->where('t.forum = :forum')
                ->setParameter('forum', $f)
                ->orderBy('t.updateAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $this->render('forum/home.html.twig', [
            'categories' => $categories,
            'forums' => $forums,
            'topics' => $topics
        ]);
    }
}