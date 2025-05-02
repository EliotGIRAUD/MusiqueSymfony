<?php

namespace App\MessageHandler;

use App\Message\GeneratePdfMessage;
use App\Service\PdfGeneratorService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GeneratePdfMessageHandler implements MessageHandlerInterface
{
    private PdfGeneratorService $pdfGeneratorService;

    public function __construct(PdfGeneratorService $pdfGeneratorService)
    {
        $this->pdfGeneratorService = $pdfGeneratorService;
    }

    public function __invoke(GeneratePdfMessage $message)
    {
        $htmlContent = $message->getHtmlContent();
        $this->pdfGeneratorService->generateArticlePdf($htmlContent);
    }
}
