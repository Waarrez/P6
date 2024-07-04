<?php

namespace App\Services;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(private MailerInterface $mailer)
    {
    }


    /**
     * Function for sending mail
     *
     * @throws TransportExceptionInterface
     */
    public function sendEmail(string $to, string $subject, string $content, string $token): void
    {
        $email = (new Email())
            ->from('thimote.cabotte6259@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($content)
            ->html('<p>'.$content.'</p> : <a href="http://127.0.0.1:8000/confirmAccount/'.$token.'">Lien de confirmation</a>');

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendResetPasswordMail(string $dst, string $subject, string $content, string $code): void
    {
        $email = (new Email())
            ->from('thimote.cabotte6259@gmail.com')
            ->to($dst)
            ->subject($subject)
            ->text($content)
            ->html('<p>'.$content.'</p> : <b>'.$code.'</b>');

        $this->mailer->send($email);
    }
}
