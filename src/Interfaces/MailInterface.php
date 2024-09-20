<?php

namespace Luminar\Component\Mailer\Mail;

interface MailInterface
{
    public function setRecipient(string $to): self;
    public function setSubject(string $subject): self;
    public function setBody(string $body): self;
    public function send(): bool;
}