<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Entity\NewsCats;

use App\Entity\Member;

use App\Entity\Item;

use App\Entity\ForumCategory;
use App\Entity\ForumForum;
use App\Entity\ForumTopic;

use App\Entity\HistoricModeration;

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
        yield MenuItem::linkToCrud('Membres', 'fa-solid fa-users', Member::class);
        yield MenuItem::linkToCrud('Items', 'fa-solid fa-briefcase', Item::class);
        yield MenuItem::linkToCrud('Actualités', 'fa-solid fa-newspaper', News::class);
        yield MenuItem::linkToCrud('Catégories d\'actualités', 'fa-solid fa-list', NewsCats::class);
        yield MenuItem::linkToCrud('Catégories Forum', 'fa-solid fa-people-line', ForumCategory::class);
        yield MenuItem::linkToCrud('Forums', 'fa-solid fa-people-roof', ForumForum::class);
        yield MenuItem::linkToCrud('Topics', 'fa-solid fa-file', ForumTopic::class);
        yield MenuItem::linkToCrud('Historique', 'fa-solid fa-hourglass-start', HistoricModeration::class);
    }
}
