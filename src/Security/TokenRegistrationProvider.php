<?php

namespace App\Security;

use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TokenRegistrationProvider implements UserProviderInterface
{
    private $entityManager;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
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
        // ...
        return $user;
    }

    public function supportsClass($class)
    {
        return Member::class === $class;
    }
}
