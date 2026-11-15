<?php

namespace App\Services;

use \Exception;
use \App\Services\EmailProvider as Provider;
use \App\Services\EmailContent as Content;
use \App\Services\EmailSMime as SMime;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSender {

    private Provider $Provider;
    private string | array $Sender;
    private array $Recipients;
    private Content $Content;
    private ?SMime $SMime;
    private string $XMailer;

    public function __construct(Provider $Provider, string | array $Sender, array $Recipients, Content $Content, ?SMime $SMime = null, string $XMailer = ' ') {

        $this->Provider = $Provider;
        $this->Sender = $Sender;
        $this->Recipients = $Recipients;
        $this->Content = $Content;
        $this->SMime = $SMime;
        $this->XMailer = $XMailer;

    }

    public function buildMailer() : PHPMailer {

        $Mailer = new PHPMailer(true);
        $Mailer->isSMTP();

        //Encoding

        $Mailer->CharSet = 'UTF-8';
        $Mailer->Encoding = 'base64';

        //Provider

        $Mailer->Host = $this->Provider->getHost();
        $Mailer->Port = $this->Provider->getPort();

        if($this->Provider->requireAuthentication()){

            $Mailer->SMTPAuth = true;
            $Mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $Mailer->Username   = $this->Provider->getUser();
            $Mailer->Password   = $this->Provider->getPassword();

        }

        //Content

        $ContainsHtml = $this->Content->containsHtml();
        $Mailer->isHTML($ContainsHtml);

        $Mailer->Subject = $this->Content->getSubject();
        $Mailer->Body = $this->Content->getBody();

        if($ContainsHtml)
            $Mailer->AltBody = $this->Content->getAltBody();

        //SMime

        if(!is_null($this->SMime))
            $Mailer->sign($this->SMime->getCertificatePath(), $this->SMime->getKeyPath(), $this->SMime->getKeyPassword());


        //Sender

        if(is_string($this->Sender))
            $Mailer->setFrom($this->Sender);
        else
            $Mailer->setFrom($this->Sender['Email'], $this->Sender['Name']);

        //XMailer

        $Mailer->XMailer = $this->XMailer;

        return $Mailer;

    }

    public function sendForAll() : void {

        $Mailer = $this->buildMailer();

        foreach($this->Recipients as $Recipient){

            if(is_string($Recipient))
                $Mailer->addAddress($Recipient);
            else
                $Mailer->addAddress($Recipient['Email'],$Recipient['Name']);

        }

        $Mailer->send();

    }

    public function sendOneByOne() : void {

        foreach($this->Recipients as $Recipient){

            $Mailer = $this->buildMailer();

            if(is_string($Recipient))
                $Mailer->addAddress($Recipient);
            else
                $Mailer->addAddress($Recipient['Email'],$Recipient['Name']);

            $Mailer->send();

        }

    }

}