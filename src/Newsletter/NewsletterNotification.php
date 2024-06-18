<?php
namespace App\Newsletter;

use App\Entity\NewsletterEmail;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class NewsletterNotification
{
    public function __construct(
        private MailerInterface $mailer,
        private string $adminEmail,
        private LoggerInterface $logger
    ){}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendConfirmationEmail(NewsletterEmail $newEmail): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->adminEmail, 'Bulletin d\'information'))
            ->to($newEmail->getEmail())
            ->subject('Merci de vous être inscrit !')
            ->htmlTemplate('newsletter/subscribe_success.html.twig')
            ->context([
                'userEmail' => $newEmail->getEmail(),
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send email: ' . $e->getMessage());
        }
    }
}