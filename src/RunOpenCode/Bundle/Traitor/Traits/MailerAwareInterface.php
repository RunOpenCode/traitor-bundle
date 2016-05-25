<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

trait MailerAwareInterface
{
    protected $mailer;

    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
}
