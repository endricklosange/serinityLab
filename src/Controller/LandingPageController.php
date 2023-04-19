<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use App\Repository\ContactRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandingPageController extends AbstractController
{
    #[Route('/', name: 'app_landing_page')]
    public function sendEmail(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /*$data = $form->getData();
            $email = (new Email())
                ->from($data->getEmail())
                ->to('contact@serinitylab.fr')
                ->subject('Envoie email')
                ->text('sfdsdfsdfdsfd');
                //dump($email). die;
            $mailer->send($email);*/
            
            $contactRepository->save($contact, true);
            $this->addFlash('success', 'Votre demande a été prise en compte.');
            return $this->redirectToRoute('app_landing_page', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('landing_page/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
