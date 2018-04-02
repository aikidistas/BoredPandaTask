<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\VersionedLike;
use App\Entity\Video;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        // TODO: add autocomplete tag in form

        list($form, $videos) = $this->handleFilterForm($request);

        return $this->render('dashboard.html.twig', array(
            'videos' => $videos,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function handleFilterForm(Request $request): array
    {
        $form = $this->createFormBuilder(array())
            ->add('tag', TextType::class, array(
                'required' => false
            ))
            ->add('video_performance', NumberType::class, array(
                'required' => false
            ))
            ->add('submitFilter', SubmitType::class, array('label' => 'Filter videos'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findByTagAndPerformance($data['tag'], $data['video_performance']);
        } else {
            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findAll();
        }
        return array($form, $videos);
    }
}
