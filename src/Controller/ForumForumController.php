<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;

use App\Entity\ForumForum;
use App\Repository\ForumForumRepository;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

class ForumForumController extends AbstractController
{
    #[Route('/forum/{id}', name: 'forum.topic.list', methods: ['GET'])]
    public function index(
        ForumForum $forum,
        ForumForumRepository $repositoryForum, 
        ForumTopicRepository $repositoryTopic,
        ForumPostRepository $repositoryPost, 
        PaginatorInterface $paginator,
        Request $request
        ): Response 
    {   
        /**
         * On récupère la liste des topics dans un arrray en fixant une limite à 6 
         * @var array
         */

        $forum = $repositoryForum->find(['id' => $forum]);

        $topics = $paginator->paginate(
            $repositoryTopic
            ->createQueryBuilder('t')
            ->orderBy('t.createAt', 'DESC')
            ->getQuery()
            ->getResult(), 
            $request->query->getInt('page', 1), 5);

        $post = $repositoryPost->createQueryBuilder('p')
             ->orderBy('p.updateAt', 'DESC')
             ->setMaxResults(1)
             ->getQuery()
             ->getOneOrNullResult();

        return $this->render('forum/topic_list.html.twig', [
            'forum' => $forum,
            'topics' => $topics,
            'post' => $post
        ]);
    }
}
