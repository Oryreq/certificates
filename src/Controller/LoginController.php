<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Service\Attribute\Required;

class LoginController extends AbstractController
{
    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public UserRepository $userRepository;

    #[Required]
    public AuthenticationUtils $authenticationUtils;


    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        $userAdmin = $this->userRepository->findOneBy(['username' => 'admin']);
        if (!$userAdmin) {
            $userAdmin = (new User())
                ->setUsername('admin')
                ->setRoles(array_values(User::ROLES))
                ->setPassword('$2y$13$dKHroammGwy5m..V51QWzeoMwdltwX.sn2kU.xwa1Z52wrZ4qAqya');
            $this->entityManager->persist($userAdmin);
            $this->entityManager->flush();
        }

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,

            'translation_domain' => 'admin',

            'csrf_token_intention' => 'authenticate',

            // the URL users are redirected to after the login (default: '/admin')
            'target_path' => $this->generateUrl('admin'),

            'username_label' => 'Логин',
            'password_label' => 'Пароль',
            'sign_in_label' => 'Вход',

            'remember_me_enabled' => true,
            'remember_me_checked' => true,
            'remember_me_label' => 'Запомнить',
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
