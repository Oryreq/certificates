<?php

namespace App\Controller\Api;

use App\Repository\CertificateRepository;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class ApiCertificateBuysController extends AbstractController
{
    #[Required]
    public CertificateRepository $certificateRepository;


    public function __invoke(#[MapRequestPayload] CreateCertificateBuyRequest $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $optionalCertificate = $this->certificateRepository->findOneBy(['id' => $request->id]);

        if (!$optionalCertificate) {
            $response = [
                'status' => 'error',
                'message' => 'Certificate with this id not exist',
            ];
            return $this->json($response, 404);
        }
        $imagePath = $optionalCertificate->getImage();
        $templateProcessor = new TemplateProcessor('images/certificates/mikki_certificate_preview.docx');
        $templateProcessor->setValue(['${NOMINAL}'], [$request->price]);
        $templateProcessor->setValue(['${VALID}'], [$request->dateTimeAt]);
        $templateProcessor->setImageValue(['IMAGE_PLACEHOLDER'],[
            'path' => 'images/certificates/' . $imagePath,
            'width'  => 350,
            'height' => 230,
            'ratio' => false
        ]);
        $templateProcessor->saveAs('1.docx');
        $phpWord = \PhpOffice\PhpWord\IOFactory::load('images/1.docx');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        return $this->json('');
    }
}