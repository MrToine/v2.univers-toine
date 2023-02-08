<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Entity\NewsCats;
use App\Entity\Member;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('V2 Univers Toine - Admin')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Revenir au site', 'fa-solid fa-rotate-left', 'home.index');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Membres', 'fa-solid fa-newspaper', Member::class);
        yield MenuItem::linkToCrud('Actualités', 'fa-solid fa-newspaper', News::class);
        yield MenuItem::linkToCrud('Catégories d\'actualités', 'fa-solid fa-list', NewsCats::class);
    }
}
