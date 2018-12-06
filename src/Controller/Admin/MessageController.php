<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/message")
 */
class MessageController extends AbstractController
{

    /**
     * @Route("/", name="admin_message_index", methods="GET")
     */
    public function index(
      Request $request,
      MessageRepository $repo,
      PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
          $repo->findBy([], ['id' => 'DESC']),
          $request->query->getInt('page', 1),
          MessageRepository::PAGE_LIMIT
        );

        return $this->render('admin/message/index.html.twig', [
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_message_delete", methods="DELETE")
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(),
          $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        $this->addFlash('success', 'Съобщението беше успешно изтрито.');

        return $this->redirectToRoute('admin_message_index');
    }
}
