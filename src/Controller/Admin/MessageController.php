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
     * @Route("/", name="message_show", methods="GET")
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

        return $this->render('message/index.html.twig', [
          'messages' => $pagination,
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
