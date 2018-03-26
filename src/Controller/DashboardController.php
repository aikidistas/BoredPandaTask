<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\VersionedLike;
use App\Entity\Video;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        $form = $this->createFormBuilder(array())
            ->add('tags', TextType::class)
            ->add('video_performance', NumberType::class)
            ->add('submitFilter', SubmitType::class, array('label' => 'Filter videos'))
            ->getForm();

        return $this->render('dashboard.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
