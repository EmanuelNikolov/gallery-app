<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/photo")
 */
class PhotoController extends AbstractController
{

    /**
     * @Route("/", name="admin_photo_index", methods="GET")
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

        return $this->render('admin/photo/index.html.twig', [
          'base' => 'admin',
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_photo_show", methods="GET")
     */
    public function show(Photo $photo): Response
    {
        return $this->render('admin/photo/show.html.twig', ['photo' => $photo]);
    }

    /**
     * @Route("/{id}", name="admin_photo_delete", methods="DELETE")
     */
    public function delete(Request $request, Photo $photo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $photo->getId(),
          $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($photo);
            $em->flush();
        }

        $this->addFlash('success', 'Снимката беше успешно изтрита.');

        return $this->redirectToRoute('admin_photo_index');
    }
}
