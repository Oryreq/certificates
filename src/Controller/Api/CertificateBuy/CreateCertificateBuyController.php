<?php

namespace App\Controller\Api\CertificateBuy;

use App\Entity\CertificateBuy;
use App\Repository\CertificateBuyRepository;
use App\Repository\CertificateRepository;
use App\Service\PdfConverter;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use SemiorbitSerial\SerialNumber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController]
class CreateCertificateBuyController extends AbstractController
{
    #[Required]
    public CertificateRepository $certificateRepository;

    #[Required]
    public CertificateBuyRepository $certificateBuyRepository;

    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public PdfConverter $pdfConverter;

    private string $CERTIFICATES_PATH = 'images/certificates/';
    private string $CERTIFICATE_NAME = 'mikki_certificate.docx';

    private array $CERTIFICATE_NOT_EXIST_RESPONSE = [
        'status'  => 'error',
        'message' => 'certificate with this id not exist',
    ];

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function __invoke(#[MapRequestPayload] CreateCertificateBuyRequest $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $optionalCertificate = $this->certificateRepository->findOneBy(['id' => $request->id]);
        if (!$optionalCertificate) {
            return $this->json($this->CERTIFICATE_NOT_EXIST_RESPONSE, 400);
        }

        $certificateCode = Uuid::v4()->toString();
        $serialNumber = SerialNumber::Generate();

        $imageName = $optionalCertificate->getImage();
        $fileName = $this->createCertificateFile($request, $imageName, $certificateCode, $serialNumber);

        $pdfFile = $this->pdfConverter->convertFromDocx($fileName);
        $pdfFile->save($this->CERTIFICATES_PATH);

        $certificateBuy = $this->createCertificateBuy($optionalCertificate->getImage(),
                                                      $request->name,
                                                      $request->price,
                                                      $serialNumber,
                                                      $certificateCode,
                                                      $pdfFile->getFileName());

        $this->entityManager->persist($certificateBuy);
        $this->entityManager->flush();

        return $this->json($certificateBuy, 200, [], ['groups' => 'certificateBuy:read']);
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    private function createCertificateFile(CreateCertificateBuyRequest $request,
                                           string $imageName,
                                           string $certificateCode,
                                           string $serialNumber): string
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
        $templateProcessor->setValue(['${ACTIVATION}'], [$certificateCode]);
        $templateProcessor->setValue(['${SERIAL_NUMBER}'], [$serialNumber]);

        $fileName = 'certificate_' . explode('.', explode('-', $imageName)[1])[0];
        $templateProcessor->saveAs("$fileName".'.docx');
        return $fileName;
    }

    private function createCertificateBuy(string $imageName,
                                          string $name,
                                          int $price,
                                          string $serialNumber,
                                          string $code,
                                          string $pdfFileName): CertificateBuy
    {
        $certificateBuy = new CertificateBuy();
        $certificateBuy->setName($name)
                       ->setPrice($price)
                       ->setImage($imageName)
                       ->setImageFile(new File($this->CERTIFICATES_PATH . $imageName))
                       ->setSerialNumber($serialNumber)
                       ->setCode($code)
                       ->setPdf($pdfFileName)
                       ->setPdfFile(new File($this->CERTIFICATES_PATH . $pdfFileName))
                       ->setCreatedAt(new \DateTime());
        return $certificateBuy;
    }
}