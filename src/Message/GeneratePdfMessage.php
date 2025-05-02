<?php

namespace App\Message;

class GeneratePdfMessage
{
    private string $htmlContent;

    public function __construct(string $htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }
}
