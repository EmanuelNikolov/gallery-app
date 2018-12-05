<?php

namespace App\Controller;


use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home_index", methods={"GET"})
     */
    public function index(PhotoRepository $repo): Response
    {
        return $this->render('home/index.html.twig', [
//          'photos' => $repo->findLatest(),
        ]);
    }
}
