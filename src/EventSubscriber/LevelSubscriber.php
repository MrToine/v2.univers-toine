<?php
namespace App\EventSubscriber;

use App\Services\LevelService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LevelSubscriber implements EventSubscriberInterface
{
    private $levelService;
    private $tokenStorage;

    public function __construct(LevelService $levelService, TokenStorageInterface $tokenStorage)
    {
        $this->levelService = $levelService;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'updateLevel',
        ];
    }

    public function updateLevel(RequestEvent $event)
    {
        if(!$this->tokenStorage->getToken()) {
            // L'utilisateur n'est pas connecté
            return;
        }
        else
        {
            // Récupérez l'utilisateur connecté
            $user = $this->tokenStorage->getToken()->getUser();

            // Si un utilisateur est connecté, mettez à jour son niveau en fonction de son XP
            if ($user) {
                $this->levelService->updateLevel($user);
            }
        }
    }
}
