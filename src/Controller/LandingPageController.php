<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandingPageController extends AbstractController
{
    #[Route('/', name: 'app_landing_page')]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = (new Email())
                ->from($data->getEmail())
                ->to('contact@serinitylab.fr')
                ->subject('Envoie email')
                ->text('sfdsdfsdfdsfd');
                //dump($email). die;
            $mailer->send($email);
        }

        return $this->render('landing_page/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
