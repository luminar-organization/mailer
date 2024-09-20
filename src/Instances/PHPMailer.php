<?php

namespace Luminar\Component\Mailer\Instances;

use Luminar\Component\Mailer\Exceptions\InstanceConfigurationException;
use Luminar\Component\Mailer\Mail\MailerStrategyInterface;
use Luminar\Component\Mailer\MailerAttributes;
use Luminar\Component\Mailer\ValueObjects\AttachmentsData;
use Luminar\Component\Mailer\ValueObjects\AuthenticationCredentials;
use Luminar\Component\Mailer\ValueObjects\AuthorData;
use Luminar\Component\Mailer\ValueObjects\ReplyData;
use Luminar\Component\Mailer\ValueObjects\TargetsData;
use Luminar\RenderEngine\Engine\TwigEngine;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer as PHPMailerInstance;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PHPMailer implements MailerStrategyInterface
{
    /**
     * @var TwigEngine $renderEngine
     */
    protected TwigEngine $renderEngine;

    /**
     * @param string $templatesPath
     * @param array $options
     */
    public function __construct(string $templatesPath, array $options = [])
    {
        $this->renderEngine = new TwigEngine($templatesPath, $options);
    }

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
     * @throws Exception
     * @throws InstanceConfigurationException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function send(AuthorData $authorData, TargetsData $targetsData, AuthenticationCredentials $authenticationCredentials,string $subject, string $templateName, array $templateVariables, string $altMessage = '', AttachmentsData $attachmentsData = null, ReplyData $replyData = null, bool $debug = false): true
    {
        $mailInstance = new PHPMailerInstance();

        // Debug
        if($debug) {
            $mailInstance->SMTPDebug = $debug;
        }

        // Authentication
        $mailInstance->SMTPAuth = $authenticationCredentials->isUseSMTP();
        $mailInstance->Host = $authenticationCredentials->getHost();
        $mailInstance->Port = $authenticationCredentials->getPort();
        $mailInstance->Username = $authenticationCredentials->getUsername();
        $mailInstance->Password = $authenticationCredentials->getUsername();
        $encryptionType = $authenticationCredentials->getEncryptionType();
        if($encryptionType !== 'ssl' && $encryptionType !== 'tls') {
            throw new InstanceConfigurationException("Invalid Encryption Type");
        }
        $mailInstance->SMTPSecure = $authenticationCredentials->getEncryptionType();

        // From
        $mailInstance->setFrom($authenticationCredentials->getEmail(), $authorData->getAuthorName());

        // Targets
        $targets = $targetsData->getTargets();
        if(count($targets) == 0) {
            throw new InstanceConfigurationException("You need to pass minimum one Target via TargetsData");
        }
        foreach($targets as $target) {
            $mailInstance->addAddress($target->getEmail(), $target->getName() ?? '');
        }

        // Reply
        if($replyData) {
            foreach($replyData as $reply) {
                $mailInstance->addReplyTo($reply->getEmail(), $reply->getName() ?? '');
            }
        }

        // Attachments
        if($attachmentsData) {
            foreach($attachmentsData as $attachment) {
                $mailInstance->addAttachment($attachment->getPath(), $attachment->getName(), $attachment->getEncoding());
            }
        }

        // Main Body
        $mailInstance->isHTML(true);
        $mailInstance->Subject = $subject;
        $mailInstance->Body = $this->renderEngine->render($templateName, $templateVariables)->getResponse();
        $mailInstance->AltBody = $altMessage ?? MailerAttributes::HTML_RENDER_ERROR;

        // Sent Email
        $mailInstance->send();
        return true;
    }
}