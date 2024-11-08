<?php

namespace App\Controller\Api\CertificateBuy;

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
class PreviewCertificateBuyController extends AbstractController
{
    #[Required]
    public CertificateRepository $certificateRepository;

    #[Required]
    public PdfConverter $pdfConverter;

    private string $CERTIFICATES_PATH = 'images/certificates/';
    private string $CERTIFICATE_NAME = 'mikki_certificate_preview.docx';

    private array $CERTIFICATE_NOT_FOUND_RESPONSE = [
        'status'  => 'error',
        'message' => 'certificate with this id not exist',
    ];


    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function __invoke(#[MapRequestPayload] PreviewCertificateBuyRequest $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $optionalCertificate = $this->certificateRepository->findOneBy(['id' => $request->id]);
        if (!$optionalCertificate) {
            return $this->json($this->CERTIFICATE_NOT_FOUND_RESPONSE, 400);
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
    private function createCertificate(PreviewCertificateBuyRequest $request, string $imageName): string
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