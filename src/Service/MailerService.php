<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(String $to, String $subject,String $linkTemplate,Contact $contact)
    {
        $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($linkTemplate)
            ->context([
                'firstname' => $contact->getFirstname(),
                'lastname' => $contact->getLastname(),
                'message' => $contact->getMessage(),
            ]);

        $this->mailer->send($email);
    }
}