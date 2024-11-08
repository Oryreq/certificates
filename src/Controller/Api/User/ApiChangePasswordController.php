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


    public function __invoke(#[MapRequestPayload] ChangePasswordRequest $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /* @var User $user */
        $user = $this->getUser();

        if ($request->password != $request->passwordConfirmation) {
            $response = [
                'status' => 'error',
                'details' => 'Passwords does not match.',
            ];
            return $this->json($response);
        }

        $this->changePassword($user->getEmail(), $request->password);
        $response = [
            'status' => 'success',
            'details' => 'Password changed successfully.',
        ];
        return $this->json($response);
    }

    private function changePassword(string $email, string $password): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();
    }
}