<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Search;
use App\Entity\Contact;
use App\Form\FilterType;
use App\Form\ContactType;
use App\Service\FilterService;
use App\Service\SearchFormService;
use App\Repository\ContactRepository;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReviewRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(Request $request, ActivityRepository $activityRepository, CategoryRepository $categoryRepository, ContactRepository $contactRepository, FilterService $filterService, SearchFormService $searchFormService,ReviewRepository $reviewRepository): Response
    {
        $session = $request->getSession();
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
        return $this->render('home/index.html.twig', [
            'searchForm' => $searchFormService->createFormSearch($data),
            'formFilter' => $filterForm
        ]);
    }
}
