<?php

namespace App\Controller\Admin;

use App\Entity\Certificate;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Админ-панель')
            ->renderContentMaximized()
            ->setDefaultColorScheme('dark');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Сертификаты', 'fa-solid fa-certificate', Certificate::class);
        
        yield MenuItem::section('Пользователи');
        yield MenuItem::linkToCrud('Пользователи', 'fa-solid fa-user-gear', User::class);

        yield MenuItem::section('Настройки');
        yield MenuItem::linkToUrl('API', 'fa fa-link', 'api')
                      ->setLinkTarget('_blank');
    }
}
