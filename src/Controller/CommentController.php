<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{

    /**
     * @Route("/{id}/new", name="comment_new", methods="GET|POST")
     */
    public function new(Request $request, Photo $photo): Response
    {
        if ($photo->getComments()->count() >= Photo::MAX_COMMENTS) {
            $this->addFlash('danger', 'Всяка снимка може да има не повече от 10 коментара.');

            return $this->redirectToRoute('photo_show', [
              'id' => $photo->getId(),
            ]);
        }

        $comment = (new Comment())->setAuthor($this->getUser());
        $photo->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Успешно добавихте коментар.');

            return $this->redirectToRoute('photo_show', [
              'id' => $photo->getId(),
            ]);
        }

        return $this->render('comment/form_error.html.twig', [
          'photo' => $photo,
          'form' => $form->createView(),
        ]);
    }

    public function form(Photo $photo): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('comment/_form.html.twig', [
          'photo' => $photo,
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods="DELETE")
     * @IsGranted(
     *     "COMMENT_DELETE",
     *     subject="comment",
     *     message="Само потребителя създал коментара може да го изтрие."
     * )
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

        return $this->redirectToRoute('photo_show', [
          'id' => $comment->getPhoto()->getId(),
        ]);
    }
}
