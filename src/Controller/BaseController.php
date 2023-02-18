<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Bundle\SecurityBundle\Security;

use App\Service\LevelService;

class BaseController extends AbstractController
{

    private $levelService;

    public function __construct(Security $security)
    {
      //$this->getBreadcrumbs();
      if($security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
      {
        $user = $security->getUser();
        $this->theme = $user->getTheme();
      }
      else
      {
        $this->theme = 'default';
      }
    }

    public function getBreadcrumbs()
    {
        // Récupérer l'URL de la page en cours
        $currentUrl = $_SERVER['REQUEST_URI'];

        // Diviser l'URL en segments
        $urlSegments = explode('/', $currentUrl);

        // Retirer les segments vides
        $urlSegments = array_filter($urlSegments);

        // Créer les liens pour chaque segment
        $breadcrumbs = array('<a href="/">Accueil</a>');

        $path = '';
        foreach ($urlSegments as $segment) {
            $path .= '/' . $segment;
            $breadcrumbs[] = '<a href="' . $path . '">' . ucfirst($segment) . '</a>';
        }

        // Afficher les liens
        $breadcrumbs = implode(' > ', $breadcrumbs);
        //return $this->render('partial/_breadcrumbs.html.twig');
    }
}
