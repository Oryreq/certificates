<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class ApiChangeEmailController extends AbstractController
{
    #[Required]
    public UserRepository $userRepository;

    #[Required]
    public EntityManagerInterface $entityManager;

    public function __invoke(#[MapRequestPayload] ChangeEmailRequest $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /* @var User $user */
        $user = $this->getUser();

        if ($request->email != $request->emailConfirmation) {
            $response = [
                'status' => 'error',
                'details' => 'Email addresses does not match.',
            ];
            return $this->json($response);
        }

        $this->changeEmail($user->getEmail(), $request->email);
        $response = [
            'status' => 'success',
            'details' => 'Email address changed successfully.',
        ];
        return $this->json($response);
    }

    private function changeEmail(string $oldEmail, string $newEmail): void
    {
        $user = $this->userRepository->findOneBy(['email' => $oldEmail]);
        $user->setEmail($newEmail);
        $this->entityManager->flush();
    }
}