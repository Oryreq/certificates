<?php

namespace App\Controller\Api\CertificateBuy;

use App\Entity\User;
use App\Repository\CertificateBuyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class ActivateCertificateBuyController extends AbstractController
{
    #[Required]
    public CertificateBuyRepository $certificateBuyRepository;

    #[Required]
    public UserRepository $userRepository;

    #[Required]
    public EntityManagerInterface $entityManager;

    private array $CERTIFICATE_NOT_FOUND_RESPONSE = [
        'status'  => 'error',
        'message' => 'certificate with this id not exist',
    ];


    public function __invoke(#[MapQueryParameter] string $code): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $optionalCertificateBuy = $this->certificateBuyRepository->findOneBy(['code' => $code]);
        if (!$optionalCertificateBuy) {
            return $this->json($this->CERTIFICATE_NOT_FOUND_RESPONSE, 400);
        }

        /* @var User $contextUser */
        $contextUser = $this->getUser();
        $user = $this->userRepository->findOneBy(['username' => $contextUser->getUserIdentifier()]);

        $user->setCoins($user->getCoins() + $optionalCertificateBuy->getPrice());
        $this->entityManager->remove($optionalCertificateBuy);
        $this->entityManager->flush();

        return $this->json('');
    }
}