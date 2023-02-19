<?php

namespace App\Controller;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

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

use App\Entity\Reading;
use App\Repository\ReadingRepository;

class ForumForumController extends BaseController
{
    #[Route('/forum/{id}', name: 'forum.topic.list', methods: ['GET'])]
    public function index(
        ForumForum $forum,
        ReadingRepository $repositoryReading,
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

        $forum = $repositoryForum->find($forum);

        $user = $this->getUser();
        $topicsQuery = $repositoryTopic->createQueryBuilder('t')
            ->select('t, p, r')
            ->leftJoin('t.post', 'p', Join::WITH, 'p.createAt = (
                SELECT MAX(p2.createAt) 
                FROM App\Entity\ForumPost p2 
                WHERE p2.topic = t
            )')
            ->leftJoin('t.readings', 'r', Join::WITH, 'r.user = :user')
            ->setParameter('user', $user)
            ->where('t.forum = :forum')
            ->setParameter('forum', $forum)
            ->orderBy('t.updateAt', 'DESC')
            ->getQuery();


        $topics = $paginator->paginate(
            $topicsQuery,
            $request->query->getInt('page', 1),
            20
        );

        $lastPosts = [];
        foreach ($topics as $topic) {
            if (!empty($topic->getPost())) {
                $lastPost = $repositoryPost->getLastPostByTopic($topic);
                $lastPosts[$topic->getId()] = $lastPost;
            }
        }


        return $this->render($this->theme . '/forum/topic_list.html.twig', [
            'forum' => $forum,
            'topics' => $topics,
            'Lastposts' => $lastPosts,
            'repositoryPost' => $repositoryPost
        ]);
    }
}
