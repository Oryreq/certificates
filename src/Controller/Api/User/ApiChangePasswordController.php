<?php

namespace App\Controller\Api\User;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class ApiChangePasswordController extends AbstractController
{
    #[Required]
    public UserRepository $userRepository;

    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public UserPasswordHasherInterface $passwordHasher;

    private array $PASSWORDS_NOT_MATCH_RESPONSE = [
        'status' => 'error',
        'details' => 'Passwords does not match.',
    ];

    private array $PASSWORD_CHANGING_SUCCESS = [
        'status' => 'success',
        'details' => 'Password changed successfully.',
    ];

    public function __invoke(#[MapRequestPayload] ChangePasswordRequest $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /* @var User $user */
        $user = $this->getUser();

        if ($request->password != $request->passwordConfirmation) {
            return $this->json($this->PASSWORDS_NOT_MATCH_RESPONSE);
        }

        $this->changePassword($user->getEmail(), $request->password);
        return $this->json($this->PASSWORD_CHANGING_SUCCESS);
    }

    private function changePassword(string $email, string $password): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();
    }
}