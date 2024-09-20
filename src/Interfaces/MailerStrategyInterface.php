<?php

namespace Luminar\Component\Mailer\Mail;

use Luminar\Component\Mailer\ValueObjects\AttachmentsData;
use Luminar\Component\Mailer\ValueObjects\AuthenticationCredentials;
use Luminar\Component\Mailer\ValueObjects\AuthorData;
use Luminar\Component\Mailer\ValueObjects\ReplyData;
use Luminar\Component\Mailer\ValueObjects\TargetsData;

interface MailerStrategyInterface
{
    /**
     * @param string $templatesPath
     * @param array $options
     */
    public function __construct(string $templatesPath, array $options = []);

    /**
     * @param AuthorData $authorData
     * @param TargetsData $targetsData
     * @param AuthenticationCredentials $authenticationCredentials
     * @param string $subject
     * @param string $templateName
     * @param array $templateVariables
     * @param string $altMessage
     * @param AttachmentsData|null $attachmentsData
     * @param ReplyData|null $replyData
     * @param bool $debug
     * @return true
     */
    public function send(AuthorData $authorData, TargetsData $targetsData, AuthenticationCredentials $authenticationCredentials,string $subject, string $templateName, array $templateVariables, string $altMessage = '', AttachmentsData $attachmentsData = null, ReplyData $replyData = null, bool $debug = false): true;
}