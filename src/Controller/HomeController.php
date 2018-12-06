<?php

namespace App\Controller;


use App\Repository\PhotoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home_index", methods={"GET"})
     */
    public function index(
      Request $request,
      PhotoRepository $repo,
      PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
          $repo->findBy([], ['uploadedOn' => 'DESC']),
          $request->query->getInt('page', 1),
          PhotoRepository::PAGE_LIMIT
        );

        return $this->render('home/index.html.twig', [
          'pagination' => $pagination,
        ]);
    }
}
