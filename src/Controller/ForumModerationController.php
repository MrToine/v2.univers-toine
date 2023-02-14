<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\ForumForum;
use App\Repository\ForumForumRepository;
use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;
use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

use App\Entity\HistoricModeration;
use App\Repository\HistoricModerationRepository;

class ForumModerationController extends AbstractController
{
    #[Route('/forum/moderation/lockUnlock/{id}', name: 'forum.moderation.lockUnlock', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function lockUnlock(
            ForumTopic $topic,
            ForumTopicRepository $repositoryTopic,
            EntityManagerInterface $manager,
            Request $request
        ): Response
    {
        if($topic->getState() === 0)
        {

            $historic = new HistoricModeration();

            $historic->setUser($this->getUser());
            $historic->setIpAdress($request->getClientIp());
            $historic->setContent("à déverrouillé le sujet \"" . $topic->getName() . "\"");

            $topic->setState(1);

            if($request->query->get('content'))
            {
                $post = new ForumPost();
                $post->setContent($request->query->get('content'));
                $post->setAuthor($this->getUser());
                $post->setTopic($topic);

                $manager->persist($post);
            }
            
            $manager->persist($historic);
            $manager->persist($topic);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le topic à bien été déverrouillé.'
            );
        }
        else
        {
            $historic = new HistoricModeration();

            $historic->setUser($this->getUser());
            $historic->setIpAdress($request->getClientIp());
            $historic->setContent("à verrouillé le sujet \"" . $topic->getName() . "\"");

            $topic->setState(0);

            if($request->query->get('content'))
            {
                $post = new ForumPost();
                $post->setContent($request->query->get('content'));
                $post->setAuthor($this->getUser());
                $post->setTopic($topic);

                $manager->persist($post);
            }

            $manager->persist($historic);
            $manager->persist($topic);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le topic à bien été verrouillé.'
            );
        }
        return $this->redirectToRoute('forum.topic', ['id' => $topic->getId()]);
    }

    #[Route('/forum/moderation/move/{id}', name: 'forum.moderation.move', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    function move(
            ForumTopic $topic,
            ForumForumRepository $repositoryForum,
            ForumTopicRepository $repositoryTopic,
            EntityManagerInterface $manager,
            Request $request
        ): Response
    {
        if($request->isMethod('post'))
        {
            if($request->request->get('forum_id'))
            {   
                $forum = $repositoryForum->find($request->request->get('forum_id'));

                $topic->setForum($forum);

                $historic = new HistoricModeration();
                
                $historic->setUser($this->getUser());
                $historic->setIpAdress($request->getClientIp());
                $historic->setContent("à déplacer le sujet \"" . $topic->getName() . "\" vers \"" . $forum->getName() . "\"");
                $manager->persist($historic);
                $manager->persist($topic);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le topic à bien été déplacé.'
                );
            }
        }

        return $this->redirectToRoute('forum.topic', ['id' => $topic->getId()]);
    }
}
