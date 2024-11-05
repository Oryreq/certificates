<?php

namespace App\Controller\Api;

use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class ApiMeController extends AbstractController
{
    #[Required]
    public SerializerInterface $serializer;


    public function __invoke(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /* @var User $user */
        $user = $this->getUser();
        return $this->json($user, 200, [], ['groups' => 'user:item']);
    }
}