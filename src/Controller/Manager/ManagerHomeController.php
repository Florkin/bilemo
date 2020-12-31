<?php

namespace App\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagerHomeController extends AbstractController
{
    /**
     * @Route("/manager", name="manager_home")
     */
    public function index(): Response
    {
        return $this->render('manager_home/index.html.twig', [
            'controller_name' => 'ManagerHomeController',
        ]);
    }
}
