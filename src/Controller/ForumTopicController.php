<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Member;
use App\Repository\MemberRepository;

use App\Entity\ForumForum;
use App\Repository\ForumForumRepository;

use App\Entity\ForumTopic;
use App\Repository\ForumTopicRepository;

use App\Entity\ForumPost;
use App\Repository\ForumPostRepository;

use App\Form\PostType;
use App\Form\TopicType;

use App\Entity\Reading;
use App\Repository\ReadingRepository;

use Doctrine\ORM\EntityManagerInterface;

use App\Services\HtmlSanitizer;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Bundle\SecurityBundle\Security as Security;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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

        parent::__construct($security);
    }

    #[Route('/forum/topic/{id}', name: 'forum.topic', methods: ['GET', 'POST'])]
    public function index(
        ForumTopic $topic,
        MemberRepository $memberRepository,
        ReadingRepository $readingRepository,
        ForumForumRepository $repositoryCategory,
        ForumForumRepository $repositoryForum,
        ForumTopicRepository $repositoryTopic,
        ForumPostRepository $repositoryPost,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager,
        Request $request
    ): Response 
    {   

        if($this->getUser())
        {
            $user = $this->getUser();
            $reading = $readingRepository->findOneBy(['user' => $user, 'topic' => $topic]);
            
            if ($reading === null) {
                $reading = new Reading();
                $reading->setUser($user);
                $reading->setTopic($topic);
            }

            $reading->setReadAt((new \DateTimeImmutable()));

            $manager->persist($reading);
            $manager->flush();

            $roles = $this->getUser()->getRoles();
            
            if (in_array('ROLE_MODERATOR', $roles)) {
                $categories = $repositoryCategory->findAll();
                $forums = $repositoryForum->findAll();
            }
        }


        /**
         * On récupère la liste des topics dans un arrray en fixant une limite à 6 
         * @var array
         */
        $categories = null;
        $forums = null;

        // t = topic m = member mi = member_item
        $posts = $paginator->paginate(
            $repositoryPost
            ->createQueryBuilder('t')
            ->select('t', 'mi', 'm')
            ->leftJoin('t.author', 'm')
            ->leftJoin('m.item', 'mi')
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

            $memberRepository = $this->getUser();
            $memberRepository->setExperience($memberRepository->getExperience() + 5);
            $manager->persist($memberRepository);

            $this->addFlash(
                'info-rpg',
                '+5xp (post forum)'
            );

            $manager->flush();

            $this->addFlash(
                'success',
                'Message créer avec succès'
            );


            return $this->redirectToRoute('forum.topic', ['id' => $topic->getId()]);
        }

        return $this->render($this->theme . '/forum/posts_list.html.twig', [
            'categories' => $categories,
            'forums' => $forums,
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
            MemberRepository $memberRepository,
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

            if($request->request->get('name') != null && $request->request->get('content') != null)
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
                
                $memberRepository = $this->getUser();
                $memberRepository->setExperience($memberRepository->getExperience() + 10);
                $manager->persist($memberRepository);

                $this->addFlash(
                    'info-rpg',
                    '+10xp (post forum)'
                );

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

         return $this->render($this->theme . '/forum/create_topic.html.twig');
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

         return $this->render($this->theme . '/forum/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
