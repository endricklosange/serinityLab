<?php

namespace App\Controller;

use App\Entity\Search;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Form\SearchFormType;
use Symfony\Component\Mime\Email;
use App\Repository\ContactRepository;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandingPageController extends AbstractController
{
    #[Route('/', name: 'app_landing_page')]
    public function sendEmail(Request $request, ActivityRepository $activityRepository, CategoryRepository $categoryRepository,ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);
        $data = new Search();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data),
                'form' => $form,
                
            ]);
        }
        if ($formContact->isSubmitted() && $formContact->isValid()) {
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
            'form' => $form,
            'formContact' => $formContact
        ]);
    }
}
