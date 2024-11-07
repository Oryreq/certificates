<?php

namespace App\Controller\Api;

use App\Repository\CertificateRepository;
use App\Service\PdfConverter;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
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

    #[Required]
    public PdfConverter $pdfConverter;

    private string $CERTIFICATES_PATH = 'images/certificates/';
    private string $CERTIFICATE_NAME = 'mikki_certificate_preview.docx';


    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
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

        $imageName = $optionalCertificate->getImage();
        $fileName = $this->createCertificate($request, $imageName);

        $pdfFile = $this->pdfConverter->convertFromDocx($fileName);
        $pdfFile->save($this->CERTIFICATES_PATH);

        $response = [
            'pdfUrl' => '/' . $this->CERTIFICATES_PATH . $pdfFile->getFileName(),
        ];
        return $this->json($response);
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    private function createCertificate(CreateCertificateBuyRequest $request, string $imageName): string
    {
        $templateProcessor = new TemplateProcessor( $this->CERTIFICATES_PATH . $this->CERTIFICATE_NAME);

        $templateProcessor->setValue(['${NOMINAL}'], [$request->price]);
        $templateProcessor->setValue(['${VALID}'], [$request->dateTimeAt]);
        $templateProcessor->setImageValue(['IMAGE_PLACEHOLDER'],[
            'path' => $this->CERTIFICATES_PATH . $imageName,
            'width'  => 350,
            'height' => 230,
            'ratio' => false
        ]);

        $fileName = 'certificate_' . explode('.', explode('-', $imageName)[1])[0];
        $templateProcessor->saveAs("$fileName".'.docx');
        return $fileName;
    }
}