<?php
namespace App\Security;

use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TokenRegistrationProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Récupérer le membre en fonction du nom d'utilisateur
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['display_name' => $identifier]);

        // Vérifier si le champ tokenValid de la table member est égal à 1
        if (!$member || !$member->isTokenValid()) {
            throw new AuthenticationException();
        }

        // Retourner l'utilisateur
        return $member;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        // Vérifier que l'utilisateur est bien une instance de Member
        if (!$user instanceof Member) {
            throw new AuthenticationException();
        }

        // Rechercher l'utilisateur à partir de son nom d'utilisateur (username)
        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['display_name' => $user->getDisplayName()]);

        if (!$member) {
            throw new AuthenticationException();
        }

        // Retourner l'utilisateur
        return $member;
    }

    public function supportsClass($class)
    {
        return Member::class === $class;
    }
}