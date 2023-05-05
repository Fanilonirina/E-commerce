<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexAdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_index_admin')]
    public function index(): Response
    {
        return $this->render('base_admin.html.twig', [
            'controller_name' => 'IndexAdminController',
        ]);
    }
}
