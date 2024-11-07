<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\CertificateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class ApiApplyCertificatesController extends AbstractController
{
    #[Required]
    public UserRepository $userRepository;

    #[Required]
    public CertificateRepository $certificateRepository;

    #[Required]
    public EntityManagerInterface $entityManager;


    public function __invoke(): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /* @var User $user */
        $contextUser = $this->getUser();

        $user = $this->userRepository->findOneBy(['username' => $contextUser->getUserIdentifier()]);

        $certificates = $this->certificateRepository->findAll();
        foreach ($certificates as $certificate) {
            $user->setCoins($user->getCoins() + $certificate->getPrice());
        }
        $this->certificateRepository->removeAll();
        $this->entityManager->flush();
        return $this->json('');
    }
}