<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Event\UserEvent;
use App\Form\UserEditType;
use App\Form\UserGeneralType;
use App\Form\UserNewPasswordType;
use App\Form\UserResetPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserLoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    public function index(UserRepository $repo): Response
    {
        return $this->render('user/index.html.twig', [
          'users' => $repo->findByPhotosCount(),
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

    public function show(User $user): Response {
        return $this->render('user/show.html.twig', [
          'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/edit", name="user_edit", methods={"GET|POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function update(
      Request $request,
      UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $user->getPlainPassword();

            if (null !== $plainPassword) {
                $password = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($password);

                $this->addFlash('success', 'Успешно променихте паролата си');
            }

            $this->em->flush();

            return $this->guardAuthenticatorHandler
              ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->userLoginAuthenticator,
                'main'
              );
        }

        $this->em->refresh($user);

        return $this->render('user/edit.html.twig', [
          'form' => $form->createView(),
        ]);
    }
}
