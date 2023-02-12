<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;
use App\Form\PostType;

use Doctrine\ORM\EntityManagerInterface;

class ForumTopicController extends AbstractController
{
    #[Route('/forum/topic/{id}', name: 'forum.topic', methods: ['GET', 'POST'])]
        public function index(
        ForumTopic $topic,
        ForumTopicRepository $repositoryTopic,
        ForumPostRepository $repositoryPost,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager,
        Request $request
        ): Response 
    {   
        /**
         * On récupère la liste des topics dans un arrray en fixant une limite à 6 
         * @var array
         */

        $topic = $repositoryTopic->find(['id' => $topic]);

        $posts = $paginator->paginate(
            $repositoryPost
            ->createQueryBuilder('t')
            ->orderBy('t.createAt')
            ->where('t.topic = :id')
            ->setParameter('id', $topic->getId())
            ->getQuery()
            ->getResult(), 
            $request->query->getInt('page', 1), 20);

        $new_post = new ForumPost();

        $form = $this->createForm(PostType::class, $new_post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $new_post = $form->getData();
            $new_post->setAuthor($this->getUser());
            $new_post->setTopic($topic);
            $manager->persist($new_post);
            $manager->flush();

            $this->addFlash(
                'success',
                'Message créer avec succès'
            );

            return $this->redirectToRoute('forum.topic', ['id' => $topic->getId()]);
        }

        return $this->render('forum/posts_list.html.twig', [
            'topic' => $topic,
            'posts' => $posts,
            'form' => $form,
        ]);
    }
}
