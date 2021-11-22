<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportException;

class MailerService
{

    /**
     * @var MailerInterface
     */
    private $mailer;


    /**
     * @var Environment
     */
    private $twig;

    /**
     * MailerService constructor.
     * 
     * @param MailerInterface $mailer
     * @param Environment $twig
     */
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendMail(
        string $subject,
        string $mailEnvoi,
        string $mailReception,
        string $template,
        array $params = NULL
    ): void {

        try {
            $email = (new Email())
                ->from($mailEnvoi)
                ->to($mailReception)
                ->subject($subject)
                ->html($this->twig->render($template, $params), 'UTF-8');

            $this->mailer->send($email);
        } catch (TransportException $e) {
        }
    }
}
