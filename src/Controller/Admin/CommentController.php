<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/comment")
 */
class CommentController extends AbstractController
{

    /**
     * @Route("/{id}", name="admin_comment_delete", methods="DELETE")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(),
          $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        $this->addFlash('success', 'Коментара беше успешно изтрит.');

        return $this->redirectToRoute('admin_photo_show', [
          'id' => $comment->getPhoto()->getId(),
        ]);
    }
}
