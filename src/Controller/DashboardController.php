<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\VersionedLike;
use App\Entity\Video;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        list($form, $videos) = $this->handleFilterForm($request);

        return $this->render('dashboard.html.twig', array(
            'videos' => $videos,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/search_tag", name="search_tag")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchTagAction(Request $request)
    {
        $q = $request->query->get('term'); // use "term" for jquery-ui
        $results = $this->getDoctrine()->getRepository(Tag::class)->findLikeText($q);

        return $this->render('searchTag.json.twig', ['results' => $results]);
    }

    /**
     * @Route("/get_tag/{id}", name="get_tag")
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTagAction($id = null)
    {
        if (is_null($id))
        {
            return new Response("");
        }
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);

        return new Response($tag->getText());
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function handleFilterForm(Request $request): array
    {
        $form = $this->createFormBuilder(array())
            ->add('tag', AutocompleteType::class, array(
                'class' => Tag::class,
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
