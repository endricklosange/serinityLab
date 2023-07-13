<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Search;
use App\Entity\Contact;
use App\Form\FilterType;
use App\Form\ContactType;
use App\Service\FilterService;
use App\Service\MailerService;
use App\Service\SearchFormService;
use App\Repository\ContactRepository;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerService $mailerService, ActivityRepository $activityRepository, CategoryRepository $categoryRepository, FilterService $filterService, SearchFormService $searchFormService): Response
    {
        $session = $request->getSession();
        $contactData = new Contact;
        $form = $this->createForm(ContactType::class, $contactData);
        $form->handleRequest($request);

        $data = new Search;
        $dataFilter = new Filter();
        [$min, $max] = $activityRepository->findMinMax($dataFilter, $data);
        $userLocation = array(
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        );
        $filterForm = $filterService->filterActivities($min, $max, $dataFilter);
        $searchForm = $searchFormService->createFormSearch($data);
        if ($searchFormService->createFormSearch($data)->isSubmitted() && $searchFormService->createFormSearch($data)->isValid()) {
            [$min, $max] = $activityRepository->findMinMax($dataFilter, $data);
            $filterForm = $this->createForm(FilterType::class, $dataFilter, [
                'default_min' => $min,
                'default_max' => $max,
            ]);
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data, $dataFilter),
                'searchForm' => $searchFormService->createFormSearch($data),
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,

            ]);
        }
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            [$min, $max] = $activityRepository->findMinMax($dataFilter, $data);
            $filterForm = $this->createForm(FilterType::class, $dataFilter, [
                'default_min' => $min,
                'default_max' => $max,
            ]);
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findFilter($dataFilter, $userLocation),
                'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,
            ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $subject = 'Sérinitylab Formulaire de contact';
            $to = 'contact@sérinitylab.com';
            $linkTemplate = 'emails/contact.html.twig';
            $mailerService->sendEmail($to, $subject,$linkTemplate, $contactData);
            $this->addFlash('success', "Votre message est reçu");
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form,
            'searchForm' => $searchFormService->createFormSearch($data),
        ]);
    }
}
