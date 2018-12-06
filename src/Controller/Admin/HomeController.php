<?php

namespace App\Controller\Admin;


use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class HomeController extends AbstractController
{

    /**
     * @Route("/", name="admin_home_index", methods={"GET"})
     */
    public function index(
      UserRepository $userRepo,
      PhotoRepository $photoRepo
    ): Response {
        return $this->render("admin/home/index.html.twig", [
          'users' => $userRepo->findBy([], ['createdOn' => 'DESC'], UserRepository::ADMIN_LIMIT),
          'photos' => $photoRepo->findBy([], ['uploadedOn' => 'DESC'], PhotoRepository::ADMIN_LIMIT),
        ]);
    }
}
