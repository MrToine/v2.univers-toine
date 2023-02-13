<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;

use App\Entity\ForumForum;
use App\Repository\ForumForumRepository;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\PostType;
use App\Form\TopicType;

use Doctrine\ORM\EntityManagerInterface;

use App\Services\HtmlSanitizer;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bundle\SecurityBundle\Security as Security;

class ForumTopicController extends BaseController
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min = 4)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(min = 4)
     */
    private $content;

    private $htmlSanitizer;
    private $security;

    public function __construct(HtmlSanitizer $htmlSanitizer, Security $security)
    {
        $this->htmlSanitizer = $htmlSanitizer;
        $this->security = $security;

        parent::__construct();
    }

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

        // Sanitize each ForumPost object in the paginated results
        foreach ($posts as $key => $post) {
            $posts[$key] = $this->htmlSanitizer->sanitizeObj($post);
        }

        $new_post = new ForumPost();

        $form = $this->createForm(PostType::class, $new_post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $new_post = $form->getData();
            $new_post->setAuthor($this->getUser());
            $new_post->setTopic($topic);
            $manager->persist($new_post);

            $topic->setUpdateAt(new \DateTimeImmutable());
            $manager->persist($topic);

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

    #[Route('/forum/{id}/create/topic', name: 'forum.create.topic', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function createTopic( 
            Request $request,
            EntityManagerInterface $manager,
        ): Response
    {        
        if($request->isMethod('POST'))
        {
            $this->name = $request->request->get('name');
            $this->content = $request->request->get('content');
            
            if($request->request->get('type'))
            {
                if($request->request->get('type') === "pinned")
                {
                    $type = "pinned";
                }
                else
                {
                    $type = "normal";
                }
            }
            else
            {
                $type = "normal";
            }

            if($request->request->get('name') != null && $request->request->get('name') != null)
            {
                $forumTopic = new ForumTopic();
                $forumPost = new ForumPost();

                $forum = $manager->getRepository(ForumForum::class)->find($request->attributes->get('id'));

                $forumTopic->setName($this->name);
                $forumTopic->setAuthor($this->security->getUser());
                $forumTopic->setType($type);
                $forumTopic->setState(1);
                $forumTopic->setForum($forum);
                $forumPost->setContent($this->content);
                $forumPost->setAuthor($this->security->getUser());
                $forumPost->setTopic($forumTopic);

                $manager->persist($forumTopic);
                $manager->persist($forumPost);
                $manager->flush();
                
                return $this->redirectToRoute('forum.topic.list', [
                    'id' => $request->attributes->get('id')
                ]);
            }
            else
            {
                $this->addFlash(
                    'error',
                    'Il faut obligatoirement un titre et un contenu'
                );
            }


        }

         return $this->render('forum/create_topic.html.twig');
    }    

    #[Route('/forum/edit/post/{id}', name: 'forum.post.edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit( 
            ForumPost $forumPost,
            Request $request,
            EntityManagerInterface $manager
        ): Response
    {

        if($forumPost->getAuthor()->getId() != $this->security->getUser()->getId()){
            return $this->redirectToRoute('forum.topic', ['id' => $forumPost->getTopic()->getId(), '_fragment' => $forumPost->getId()]);
        }

        $form = $this->createForm(PostType::class, $forumPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $forumPost = $form->getData();

            $manager->persist($forumPost);
            $manager->flush();

            $this->addFlash(
                'success',
                'Message Mise à jour avec succès'
            );

            return $this->redirectToRoute('forum.topic', ['id' => $forumPost->getTopic()->getId(), '_fragment' => $forumPost->getId()]);
        }

         return $this->render('forum/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
