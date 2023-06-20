<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Search;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\FilterService;
use App\Service\SearchFormService;
use App\Repository\ContactRepository;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LandingPageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function sendEmail(Request $request, ActivityRepository $activityRepository, CategoryRepository $categoryRepository,ContactRepository $contactRepository, FilterService $filterService, SearchFormService $searchFormService): Response
    {
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);
        $session = $request->getSession();
        $dataFilter = new Filter();
        [$min, $max] = $activityRepository->findMinMax($dataFilter);
        $data = new Search;
        $userLocation = array(
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        );
        $filterForm = $filterService->filterActivities($min, $max, $dataFilter);
        $searchForm = $searchFormService->createFormSearch($data);
        if ($searchFormService->createFormSearch($data)->isSubmitted() && $searchFormService->createFormSearch($data)->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data),
                'searchForm' => $searchFormService->createFormSearch($data),
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,
                
            ]);
        }
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findFilter($dataFilter, $userLocation),
                'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'searchForm' => $searchFormService->createFormSearch($data),
            'formContact' => $formContact,
            'formFilter' => $filterForm
        ]);
    }
}
