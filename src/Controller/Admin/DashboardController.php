<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('MusiqueSynfony');
    }

    public function configureMenuItems(): iterable
    {
        // Lien vers la page d'accueil du dashboard
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Lien vers le CRUD Article
        yield MenuItem::linkToCrud('Articles', 'fas fa-music', Article::class);

        // Si tu veux gérer les commentaires ou les utilisateurs en backoffice :
        // yield MenuItem::linkToCrud('Comments', 'fas fa-comments', Comment::class);
        // yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
    }
}
