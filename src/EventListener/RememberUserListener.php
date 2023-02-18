<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RememberUserListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Vérifie si la session contient des informations de connexion
        if ($request->hasPreviousSession()) {
            // Récupère le token de l'utilisateur
            $token = $this->tokenStorage->getToken();

            // Vérifie si l'utilisateur est authentifié
            if (!$token || !($user = $token->getUser())) {
                return;
            }

            // Vérifie si un cookie de session est présent
            $sessionDuration = $request->getSession()->get('duration');
            if (!$sessionDuration) {
                return;
            }

            // Récupère la durée de validité du cookie de session
            $sessionExpiration = time() + $sessionDuration;

            // Vérifie si la session est encore valide
            if ($sessionExpiration < time()) {
                return;
            }

            // Définit un nouveau token avec la durée de validité du cookie de session
            $newToken = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());

            // Stocke le nouveau token
            $this->tokenStorage->setToken($newToken);
        }
    }
}
