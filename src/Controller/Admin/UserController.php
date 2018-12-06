<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/users", name="admin_user_index", methods={"GET"})
     */
    public function index(
      Request $request,
      UserRepository $repo,
      PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
          $repo->findBy([], ['createdOn' => 'DESC']),
          $request->query->getInt('page', 1),
          UserRepository::PAGE_LIMIT
        );

        return $this->render('admin/user/index.html.twig', [
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/user/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
          'user' => $user,
        ]);
    }
}
