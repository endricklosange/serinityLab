<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Review;
use App\Entity\Search;
use App\Form\ReviewType;
use App\Service\FilterService;
use App\Service\ReviewService;
use App\Service\SearchFormService;
use App\Repository\ReviewRepository;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    public function index(Request $request,ActivityRepository $activityRepository, CategoryRepository $categoryRepository,ReviewRepository $reviewRepository, FilterService $filterService, SearchFormService $searchFormService): Response
    {
        $session = $request->getSession();
        $dataFilter = new Filter();
        $data = new Search;
        [$min, $max] = $activityRepository->findMinMax($dataFilter,$data);
        $userLocation = array(
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        );
        $filterForm = $filterService->filterActivities($min, $max, $dataFilter);
        $searchForm = $searchFormService->createFormSearch($data);
        if ($searchFormService->createFormSearch($data)->isSubmitted() && $searchFormService->createFormSearch($data)->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data,$dataFilter),
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

        return $this->render('review/index.html.twig', [
            'reviews' => $reviewRepository->all(),
            'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,
        ]);
    }

    #[Route('/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReviewRepository $reviewRepository, FilterService $filterService, SearchFormService $searchFormService, ActivityRepository $activityRepository, CategoryRepository $categoryRepository, ReviewService $reviewService): Response
    {
        $review = new Review();
        $reviewForm = $reviewService->createFormReview($review);
        $session = $request->getSession();
        $dataFilter = new Filter();
        $data = new Search;
        [$min, $max] = $activityRepository->findMinMax($dataFilter,$data);
        $userLocation = array(
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        );
        $filterForm = $filterService->filterActivities($min, $max, $dataFilter);
        $searchForm = $searchFormService->createFormSearch($data);
        if ($searchFormService->createFormSearch($data)->isSubmitted() && $searchFormService->createFormSearch($data)->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data,$dataFilter),
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
        $user = $this->getUser();
        
        foreach ($user->getOrders() as $order) {
            if ($order->ispay() === true && $order->getReservation()->getActivity()->getId()) {
                if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {

                    $review->setUser($user);
                    $review->setActivity($order->getReservation()->getActivity());

                    $reviewRepository->createReviewWithScore($review);
        
                    return $this->redirectToRoute('app_activity', [], Response::HTTP_SEE_OTHER);
                }
            }
        }


        return $this->renderForm('review/new.html.twig', [
            'review' => $review,
            'form' => $reviewForm,
            'searchForm' => $searchForm,
            'formFilter' => $filterForm,
        ]);
    }

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReviewRepository $reviewRepository, FilterService $filterService, SearchFormService $searchFormService, ActivityRepository $activityRepository, CategoryRepository $categoryRepository, Review $review): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        $session = $request->getSession();
        $dataFilter = new Filter();
        $data = new Search;
        [$min, $max] = $activityRepository->findMinMax($dataFilter,$data);
        $userLocation = array(
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        );
        $filterForm = $filterService->filterActivities($min, $max, $dataFilter);
        $searchForm = $searchFormService->createFormSearch($data);
        if ($searchFormService->createFormSearch($data)->isSubmitted() && $searchFormService->createFormSearch($data)->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data,$dataFilter),
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
        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->updateReviewWithScore($review);
            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
            'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $reviewRepository->deleteReview($review);
        }

        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }
}
