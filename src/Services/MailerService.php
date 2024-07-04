<?php

namespace App\Services;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    /**
     * Function for sending mail
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param string $token
     * @return void
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
     * @param string $dst
     * @param string $subject
     * @param string $content
     * @param string $code
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendResetPasswordMail(string $dst, string $subject, string $content, string $code): void
    {
        $email = (new Email())
            ->from('thimote.cabotte6259@gmail.com')
            ->to($dst)
            ->subject($subject)
            ->text($content)
            ->html('<p>' . $content . '</p> : <b>'.$code.'</b>');

        $this->mailer->send($email);
    }
}
