<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\Expr\Join;

use App\Entity\ForumCategory;
use App\Repository\ForumCategoryRepository;

use App\Entity\ForumForum;
use App\Repository\ForumForumRepository;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

use App\Entity\Reading;
use App\Repository\ReadingRepository;

class ForumHomeController extends BaseController
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
            $user = $this->getUser();
            $topic = $repositoryTopic->createQueryBuilder('t')
                ->select('t, r')
                ->leftJoin('t.readings', 'r', Join::WITH, 'r.user = :user')
                ->where('t.forum = :forum')
                ->setParameter('forum', $f)
                ->setParameter('user', $user)
                ->orderBy('t.updateAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            if ($topic === null) {
                // Gérer le cas où aucun sujet n'a été trouvé
            }
            $topics[$f->getId()] = $topic;
        }


        return $this->render($this->theme . '/forum/home.html.twig', [
            'categories' => $categories,
            'forums' => $forums,
            'topics' => $topics
        ]);
    }
}