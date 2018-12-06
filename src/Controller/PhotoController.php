<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\User;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/photo")
 */
class PhotoController extends AbstractController
{

    /**
     * @Route("/", name="photo_index", methods="GET")
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

        return $this->render('photo/index.html.twig', [
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="photo_new", methods="GET|POST")
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, LoggerInterface $logger): Response
    {
        $user = $this->getUser();

        if ($user->getPhotos()->count() >= User::MAX_PHOTOS) {
            $this->addFlash('danger', 'Всеки потребител може да качва не повече от 10 снимки.');

            return $this->redirectToRoute('photo_index');
        }

        $photo = (new Photo())->setUser($user);
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $photo->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            try {
                $file->move(
                  $this->getParameter('photos_dir'),
                  $fileName
                );

                $photo->setPath($fileName);

                $em = $this->getDoctrine()->getManager();
                $em->persist($photo);
                $em->flush();

                $this->addFlash('success', 'Снимката беше добавена успешно.');

                return $this->redirectToRoute('photo_show', [
                  'id' => $photo->getId(),
                ]);
            } catch (FileException $e) {
                $form->addError(new FormError('Възникна грешка при качването на файла.'));
                $logger->critical($e->getMessage(), ['file' => $e->getFile()]);
            }
        }

        return $this->render('photo/new.html.twig', [
          'photo' => $photo,
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="photo_show", methods="GET")
     */
    public function show(Photo $photo): Response
    {
        return $this->render('photo/show.html.twig', ['photo' => $photo]);
    }

    /**
     * @Route("/{id}", name="photo_delete", methods="DELETE")
     * @IsGranted("ROLE_USER")
     * @IsGranted(
     *     "PHOTO_DELETE",
     *     subject="photo",
     *     message="Само потребителя качил снимката може да я изтрие."
     * )
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

        return $this->redirectToRoute('photo_index');
    }
}
