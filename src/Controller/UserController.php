<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserLoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{

    private $em;

    private $guardAuthenticatorHandler;

    private $userLoginAuthenticator;

    private $eventDispatcher;

    /**
     * UserController constructor.
     *
     * @param EntityManagerInterface $em
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param UserLoginAuthenticator $userLoginAuthenticator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
      EntityManagerInterface $em,
      GuardAuthenticatorHandler $guardAuthenticatorHandler,
      UserLoginAuthenticator $userLoginAuthenticator,
      EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
        $this->userLoginAuthenticator = $userLoginAuthenticator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/users", name="user_index", methods={"GET"})
     */
    public function index(
      Request $request,
      UserRepository $repo,
      PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
          $repo->findByPhotosCount(),
          $request->query->getInt('page', 1),
          UserRepository::PAGE_LIMIT
        );

        return $this->render('user/index.html.twig', [
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/register", name="user_register", methods={"GET", "POST"})
     */
    public function register(
      Request $request,
      UserPasswordEncoderInterface $encoder
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $encoder
              ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            $this->em->persist($user);
            $this->em->flush();

            // Set ROLE_ADMIN for first 5 real users.
            if ($user->getId() > 12 && $user->getId() < 18) {
                $user->setRoles(['ROLE_ADMIN']);
                $this->em->flush();
            }

            $this->addFlash('success', 'Успешно регистрирахте своя профил.');

            return $this->guardAuthenticatorHandler
              ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->userLoginAuthenticator,
                'main'
              );
        }

        return $this->render('user/register.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/edit", name="user_edit", methods={"GET|POST"})
     * @IsGranted("ROLE_USER")
     */
    public function update(
      Request $request,
      UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $this->getUser();
        $username = $user->getUsername();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPlainPassword();
            $isUsernameUpdated = $user->getUsername() !== $username;
            $isPasswordUpdated = null !== $plainPassword;

            if ($isUsernameUpdated || $isPasswordUpdated) {
                if ($isUsernameUpdated) {
                    $this->addFlash('success',
                      'Успешно променихте потребителското си име.');
                }

                if ($isPasswordUpdated) {
                    $password = $encoder->encodePassword($user, $plainPassword);
                    $user->setPassword($password);

                    $this->addFlash('success',
                      'Успешно променихте паролата си.');
                }

                $this->em->flush();

                return $this->guardAuthenticatorHandler
                  ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->userLoginAuthenticator,
                    'main'
                  );
            } else {
                $form->addError(new FormError('За да редактирате профила си, трябва потребителското име или паролата, да бъдат променени.'));
            }
        }

        $this->em->refresh($user);

        return $this->render('user/edit.html.twig', [
          'form' => $form->createView(),
        ]);
    }
}
