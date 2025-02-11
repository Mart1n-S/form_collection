<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendPdfByEmail(string $recipientEmail, string $pdfContent, string $fileName)
    {
        // Créer l'email avec le PDF en pièce jointe
        $email = (new Email())
            ->from('expediteur@example.com')
            ->to($recipientEmail)
            ->subject('Votre document PDF')
            ->text('Veuillez trouver en pièce jointe votre document.')
            ->attach($pdfContent, $fileName, 'application/pdf');

        // Envoyer l'email
        $this->mailer->send($email);
    }
}
