<?php

namespace App\Classes;

use Mailjet\Client;
use \Mailjet\Resources;

class MailJet
{
    private $apikey = "8679b2aca2d2243f21ad1696fcdff336";
    private $secretKey = "2dceca14da091e9ffc2e93c92ee68a35";

    public function send(string $mail,string $username, string $token) {
        $mj = new Client($this->apikey,$this->secretKey,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "thimote.cabotte6259@gmail.com",
                        'Name' => "Thimote"
                    ],
                    'To' => [
                        [
                            'Email' => $mail,
                            'Name' => $username
                        ]
                    ],
                    'Subject' => "Confirmation de votre compte",
                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => "<a href='http://127.0.0.1:8002/confirmAccount/$token'>Valider votre compte</a>",
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $mj->post(Resources::$Email, ['body' => $body]);
    }
}