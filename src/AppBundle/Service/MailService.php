<?php

namespace AppBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class MailService
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var string
     */
    private $mailFrom;

    /**
     * @param KernelInterface $kernel
     * @param $mailFrom
     */
    public function __construct(KernelInterface $kernel, $mailFrom)
    {
        $this->kernel = $kernel;
        $this->mailFrom = $mailFrom;
    }

    /**
     * @param $subject
     * @param $body
     * @param $to
     * @param null   $from
     */
    public function send($subject, $body, $to, $from = null)
    {
        if ('dev' === $this->kernel->getEnvironment()) {
            $from = 'julien.thomas0@gmail.com';
            $to = 'julien.thomas0@gmail.com';
        } else {
            $from = $from ?: $this->mailFrom;
        }

        $headers = sprintf('From: "Contact Ontap" <%s>', $from);

        mail($to, $subject, $body, $headers);
    }
}
