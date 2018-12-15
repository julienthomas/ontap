<?php

namespace AppBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class MailService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var string
     */
    private $mailFrom;

    /**
     * @param \Swift_Mailer   $mailer
     * @param KernelInterface $kernel
     * @param $mailFrom
     */
    public function __construct(\Swift_Mailer $mailer, KernelInterface $kernel, $mailFrom)
    {
        $this->mailer = $mailer;
        $this->kernel = $kernel;
        $this->mailFrom = $mailFrom;
    }

    /**
     * @param $subject
     * @param $body
     * @param $to
     * @param string $bodyType
     * @param null   $from
     */
    public function send($subject, $body, $to, $bodyType = 'text/html', $from = null)
    {
        if ('dev' === $this->kernel->getEnvironment()) {
            $from = 'julien.thomas0@gmail.com';
            $to = 'julien.thomas0@gmail.com';
        } else {
            $from = $from ?: $this->mailFrom;
        }

        $headers = sprintf('From: "contact Ontap" <%s>', $from);

        mail($to, $subject, $body, $headers);
    }
}
