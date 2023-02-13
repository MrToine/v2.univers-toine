<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseController extends AbstractController
{

    public function __construct()
    {
        $this->getBreadcrumbs();
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
