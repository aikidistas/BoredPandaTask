<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        return $this->render(
            'dashboard.html.twig', array(
                'variable' => 'value'
            ));
    }

    public function ajax()
    {
        return $this->json([
            'status' => 'OK',
            'data' => ['a', 'b', 'c']
        ]);
    }
}
