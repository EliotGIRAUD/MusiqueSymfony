<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratorService
{
    public function generateArticlePdf(string $htmlContent): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $output = $dompdf->output();
        $fileName = 'article_' . uniqid() . '.pdf';

        file_put_contents(
            'public/pdf/' . $fileName,
            $output                    
        );
        
        return $fileName;
    }
}
