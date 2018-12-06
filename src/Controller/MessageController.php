<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{

    /**
     * @Route("/new", name="message_new", methods="GET|POST")
     */
    public function new(Request $request, \Swift_Mailer $mailer): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            if ($this->sendEmail($mailer, $message)) {
                $this->addFlash('success', 'Успешно изпратихте съобщение.');
            } else {
                $this->addFlash('danger', 'Нещо се обърка. Опитайте отново.');
                $this->redirectToRoute('message_new');
            }

            return $this->redirectToRoute('home_index');
        }

        return $this->render('message/new.html.twig', [
          'message' => $message,
          'form' => $form->createView(),
        ]);
    }

    private function sendEmail(\Swift_Mailer $mailer, Message $message): int
    {
        $message = (new \Swift_Message())
          ->setSubject('Съобщение от ' . $message->getName())
          ->setFrom($this->getParameter('system_email'))
          ->setTo($this->getParameter('system_support_email'))
          ->setBody(
            $this->renderView('email/message.html.twig', [
                'message' => $message,
              ]
            ),
            'text/html'
          );

        return $mailer->send($message);
    }
}
