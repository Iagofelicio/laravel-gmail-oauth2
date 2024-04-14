<?php

namespace Iagofelicio\LaravelGmailOauth2\Transport;

use Swift;
use Exception;
use Dotenv\Dotenv;
use App\Models\Emails;
use Psr\Log\LoggerInterface;
use PHPMailer\PHPMailer\SMTP;
use Swift_Mime_SimpleMessage;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Mail\Transport\Transport;
use League\OAuth2\Client\Provider\Google;

class GmailTransport extends Transport
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
     * Send emails using Gmail API.
     *
     * @param  mixed  $params
     * @return void
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
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

            $mail->setFrom($this->fromAddress, $this->fromName);
            foreach($message->getTo() as $toMail => $toName){
                $mail->addAddress($toMail,$toName);
            }
            $cc = $message->getCc();
            if(isset($cc)){
                foreach($cc as $ccMail => $ccName){
                    $mail->addCC($ccMail, $ccName);
                }
            }

            $bcc = $message->getBcc();
            if(isset($bcc)){
                foreach($bcc as $bccMail => $bccName){
                    $mail->addBCC($bccMail, $bccName);
                }
            }
            $mail->Subject = $message->getSubject();
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $body = $message->getBody();
            $mail->msgHTML($body);
            $messageChildren = $message->getChildren();
            if(isset($messageChildren)){
                foreach ($messageChildren as $child) {
                    if (method_exists($child, 'getDisposition')) {
                        if($child->getDisposition() == "attachment"){
                            $filename = $child->getFilename();
                            $attachmentData = $child->getBody();
                            $mail->addStringAttachment($attachmentData, $filename);
                        }
                    }
                }
            }

            if( $mail->send() ) {
                return $this->numberOfRecipients($message);
            } else {
                throw new Exception("Failed to send email.");
            }
        } catch(Exception $e) {
            throw new Exception("Failed to send email. Exception: ". $e->getMessage());
        }
    }

}
