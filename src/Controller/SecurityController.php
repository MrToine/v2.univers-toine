<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;

use App\Form\RegistrationType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends BaseController
{
    
    public function __construct(MailerInterface $mailer, Security $security)
    {
        $this->mailer = $mailer;

        parent::__construct($security);
    }
    
    /**
     * Permet de connecter un utilisateur à partir d'un formulaire de connexion
     * 
     * @param  AuthenticationUtils $authencationUtils []
     * @return render->(view)                         []
     */
    #[Route('/users/login', name: 'users.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, TokenStorageInterface $tokenStorage, RememberMeHandlerInterface $rememberMeServices, Request $request): Response
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            dd('youhou');
            return $this->redirectToRoute('home.index');
        }
        
        return $this->render($this->theme . '/users/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('users/logout', name: 'users.logout')]
    public function logout()
    {
        //Nothing to do here
    }


    #[Route('users/activate/{token}', name: 'users.activate', methods: ['GET'])]
    public function activate($token, Request $request, EntityManagerInterface $manager):Response
    {
        $user = $manager->getRepository(Member::class)->findOneBy(['token' => $token]);

        if($user->isTokenValid())
        {
            return $this->redirectToRoute('users.login');
        }
            $user->setTokenValid(1);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                    'success',
                    'Félécitations ! Le compte à bien été enregistré, vous pouvez dès à présent vous connecter.' 
                );

        return $this->redirectToRoute('users.login');
    }

    
    /**
     * Permet de créer un utilisateur à parti d'un formulaire d'inscription
     * 
     * @param  Request                $request 
     * @param  EntityManagerInterface $manager 
     * @return render->(view)                          
     */
    
    #[Route('users/register', name: 'users.register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $manager): Response
    {
        $token = bin2hex(random_bytes(32));
        $user = New Member();
        $user->setLevel(1);
        $user->setRoles(["ROLE_USER"]);
        $user->setAvatarName('default.png');
        $user->setExperience(0);
        $user->setLevel(0);
        $user->setTheme('default');
        $user->setToken($token);
        $user->setTokenValid(0);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $this->addFlash(
                'success',
                'Le compte à bien été créer. Cependant, veuillez vérifier votre email afin de valider votre inscription.' 
            );

            $manager->persist($user);
            $manager->flush();

            $lastUserId = $user->getId(); // Récupérer l'ID du dernier utilisateur enregistré

            $userDir = 'uploads/avatars/' . $lastUserId; // Créer le nom du dossier à partir de l'ID
            if (!file_exists($userDir)) {
                mkdir($userDir, 0777, true); // Créer le dossier s'il n'existe pas déjà
            }

            $defaultAvatarPath = 'uploads/avatars/default.png';
            $newAvatarDirPath = 'uploads/avatars/' . $lastUserId;

            // Copy the default avatar to the new directory
            copy($defaultAvatarPath, $newAvatarDirPath . '/default.png');

            // Instantiation et configuration de PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'universtoine@gmail.com';
            $mail->Password = 'zvxasnektkkeibud';
            $mail->Port = 587;

            // Envoi du message
            try {
                // Destinataire, sujet et corps du message
                $mail->setFrom('universtoine@gmail.com', 'universtoine@gmail.com');
                $mail->addAddress($user->getEmail(), $user->getDisplayName());
                $mail->Subject = 'Activation de compte pour Univers Toine';
                $mail->isHTML(true);
                $mail->Body = 'Bonjour <strong>'. $user->getDisplayName() . '</strong> !<br><br>Ton compte à bien été créer sur Univers Toine. Cependant, afin de terminer ton inscription, il est nécéssaire d\'activer ton compte en suivant le lien : <br><a href="https://univers-toine.org/users/activate/' . $token . '">Activer mon compte</a><br><br> A tout de suite sur le site !<br><br>Si le lien ne fonctionne pas, copie colle cet url dans ton navigateur web : https://univers-toine.org/users/activate/' . $token . '<br><br>L\'équipe d\'Univers Toine.';

                // Envoi du message
                $mail->send();
            } catch (Exception $e) {
                dd("Erreur lors de l'envoi du message : {$mail->ErrorInfo}");
            }

            return $this->redirectToRoute('users.login');
        }

        return $this->render($this->theme . '/users/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
