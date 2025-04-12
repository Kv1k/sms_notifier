<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class SmsService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendSms(string $telephone, string $message): void
    {
        $this->logger->info('Envoi du SMS', [
            'telephone' => $telephone,
            'message' => $message,
            'date' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }
}
