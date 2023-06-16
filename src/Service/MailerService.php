<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
//use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendEmail(
        $to = 'mohamedhedi1hamdi@gmail.com',
        $content = '<p>Consulter nos series pour plus des informations</p>',
        $subject = 'Une nouvelle serie a été ajouté'
    ): void {
        $email = (new TemplatedEmail())
            ->from('info@sportconnect.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            //->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);

        // ...
    }
}
