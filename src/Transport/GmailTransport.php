<?php

namespace Iagofelicio\LaravelGmailOauth2\Transport;

use Exception;
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use MailchimpTransactional\ApiClient;
use League\OAuth2\Client\Provider\Google;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class GmailTransport extends AbstractTransport
{
    /**
     * The Gmail Client ID
     *
     * @var string
     */
    protected $clientId;

    /**
     * The Gmail Client Secret
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The Gmail Client Refresh Token
     *
     * @var string
     */
    protected $clientRefreshToken;


    /**
     * The Gmail Client Username (email)
     *
     * @var string
     */
    protected $clientMail;

    /**
     * Default env Mail from address
     *
     * @var string
     */
    protected $fromAddress;

    /**
     * Default env Mail from name
     *
     * @var string
     */
    protected $fromName;

    /**
     * Create a new Gmail transport instance.
     *
     * @param  mixed  $params
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $env = Dotenv::createArrayBacked(base_path())->load();
        $this->clientId = $env['GMAIL_API_CLIENT_ID'];
        $this->clientSecret = $env['GMAIL_API_CLIENT_SECRET'];
        $this->clientRefreshToken = $env['GMAIL_API_CLIENT_REFRESH_TOKEN'];
        $this->clientMail = $env['GMAIL_API_CLIENT_MAIL'];

        if(isset($env['MAIL_FROM_ADDRESS'])){
            $this->fromAddress = $env['MAIL_FROM_ADDRESS'];
        } else {
            $this->fromAddress = $this->clientMail;
        }

        if(isset($env['MAIL_FROM_NAME'])){
            $this->fromName = $env['MAIL_FROM_NAME'];
        } else {
            $this->fromName = $this->clientMail;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {

        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $mail = new PHPMailer(true);
        $provider = new Google(
            [
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret
            ]
        );

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;
            $mail->AuthType = 'XOAUTH2';
            $mail->setOAuth(
                new OAuth(
                    [
                        'provider'          => $provider,
                        'clientId'          => $this->clientId,
                        'clientSecret'      => $this->clientSecret,
                        'refreshToken'      => $this->clientRefreshToken,
                        'userName'          => $this->clientMail
                    ]
                )
            );


            $fromAddresses = $email->getFrom();
            if(isset($fromAddresses)){
                foreach($fromAddresses as $from){
                    $mail->setFrom($from->getAddress(),$from->getName());
                }
            } else {
                $mail->setFrom($this->fromAddress, $this->fromName);
            }

            foreach($email->getTo() as $to){
                $mail->addAddress($to->getAddress(),$to->getName());
            }


            $cc = $email->getCc();
            if(isset($cc)){
                foreach($cc as $cc){
                    $mail->addCC($cc->getAddress(), $cc->getName());
                }
            }

            $bcc = $email->getBcc();
            if(isset($bcc)){
                foreach($bcc as $bcc){
                    $mail->addBCC($bcc->getAddress(), $bcc->getName());
                }
            }

            $mail->Subject = $email->getSubject();
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $body = $email->getHtmlBody();
            $mail->msgHTML($body);

            $attachments = $email->getAttachments();
            if(isset($attachments)){
                foreach($attachments as $attachment){
                    $filename = $attachment->getFilename();
                    $attachmentData = $attachment->getBody();
                    $mail->addStringAttachment($attachmentData, $filename);
                }
            }

            if( $mail->send() ) {
            } else {
                throw new Exception("Failed to send email.");
            }
        } catch(Exception $e) {
            throw new Exception("Failed to send email. Exception: ". $e->getMessage());
        }

    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'gmail';
    }
}
