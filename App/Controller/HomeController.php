<?php

namespace App\Controller;

use App\Helper\TwigHelper;
use App\Router\Request;
use App\Validator\ContactMailSendValidator;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class HomeController
{

    public static function showHome($message = null)
    {
        $twig = new TwigHelper();

        $twig->loadTwig()->display('index.html.twig', ['message' => $message]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public static function contactByMail($data)
    {
        $transport = Transport::fromDsn('smtp://aa61d97b907da3:e322f509ddb3ee@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login');
        $mailer = new Mailer($transport);
        $request = new Request();
        $contactMailValidator = new ContactMailSendValidator();

        $contentMail = [
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message']
        ];

        if ($contactMailValidator->validate($contentMail)){
            $name = $contentMail['name'];
            $email = (new Email())
                ->from('vincentorru@gmail.com')
                ->to($contentMail['email'])
                ->priority(Email::PRIORITY_HIGH)
                ->subject("Contact par le blog de $name")
                ->text($contentMail['message']);

            $mailer->send($email);
            $request->redirectToRoute('home', ['success' => "Le mail a bien été envoyé"]);
        }
    }

}