<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Filter;
use App\Entity\Search;
use App\Data\SearchData;
use App\Entity\Activity;
use App\Form\FilterType;
use App\Form\SearchFormType;
use App\Form\ServiceBookType;
use App\Service\FilterService;
use App\Service\SearchFormService;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\FormError;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/activités')]
class ActivityController extends AbstractController
{
    #[Route('/', name: 'app_activity')]
    public function index(Request $request, ActivityRepository $activityRepository, CategoryRepository $categoryRepository, FilterService $filterService, SearchFormService $searchFormService): Response
    {
        if ($request->isMethod('POST')) {
            // Stocker les coordonnées dans les variables de session
            $session = $request->getSession();
            $session->set('latitude', $request->request->get('latitude'));
            $session->set('longitude', $request->request->get('longitude'));
        }
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
            [$min, $max] = $activityRepository->findMinMax($dataFilter,$data);
            $filterForm = $this->createForm(FilterType::class, $dataFilter, [
                'default_min' => $min,
                'default_max' => $max,
            ]);
            dump($min.' '.$max);
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
            [$min, $max] = $activityRepository->findMinMax($dataFilter,$data);
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
        if ($userLocation['latitude'] === null || $userLocation['longitude'] === null) {
            return $this->render('/activity/index.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findBy(
                    [],
                    ['created_at' => 'ASC']
                ),
                'searchForm' => $searchForm,
                'min' => $min,
                'max' => $max,
                'formFilter' => $filterForm
            ]);
        } else {
            return $this->render('/activity/index.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findByLocation($userLocation['latitude'], $userLocation['longitude']),
                'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max
            ]);
        }
    }
    #[Route('/favoris', name: 'app_activity_favorite')]
    public function favorite(Request $request,Filter $filter , CategoryRepository $categoryRepository, ActivityRepository $activityRepository): Response
    {
        $data = new Search();
        $user = $this->getUser();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data,$filter),
                'form' => $form,

            ]);
        }
        return $this->render('/activity/favorites.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'activities' =>  $user->getFavorite(),
            'form' => $form,

        ]);
    }
    #[Route('/recherche/', name: 'app_activity_search')]
    public function search(ActivityRepository $activityRepository, CategoryRepository $categoryRepository, Request $request): Response
    {

        $data = new Search();
        $dataFilter = new Filter();
        $session = $request->getSession();
        $userLocation = array(
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        );

        $searchForm = $this->createForm(SearchFormType::class, $data);
        $searchForm->handleRequest($request);
        [$min, $max] = $activityRepository->findMinMax($dataFilter,$data);
        $formFilter = $this->createForm(FilterType::class, $dataFilter, [
            'default_min' => $min,
            'default_max' => $max,
        ]);
        $formFilter->handleRequest($request);

        if ($formFilter->isSubmitted() && $formFilter->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findFilter($dataFilter, $userLocation),
                'searchForm' => $searchForm,
                'formFilter' => $formFilter,
                'min' => $min,
                'max' => $max,
            ]);
        }
    }

    #[Route('/categorie/{id}', name: 'app_activity_category', methods: ['GET'])]
    public function showByCategory(Request $request, CategoryRepository $categoryRepository, ActivityRepository $activityRepository, FilterService $filterService, SearchFormService $searchFormService, int $id): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->redirectToRoute('app_activity', [], Response::HTTP_SEE_OTHER);
        }
        $session = $request->getSession();
        $data = new Search;
        $dataFilter = new Filter();
        [$min, $max] = $activityRepository->findMinMax($dataFilter,$data,$category);

        $activities = $category->getActivities();
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
                'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,

            ]);
        }
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findFilter($dataFilter, $userLocation, false, $category),
                'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,
            ]);
        }
        return $this->render('/activity/category.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findAll(),
            'activities' => $activities,
            'searchForm' => $searchForm,
            'formFilter' => $filterForm,
            'min' => $min,
            'max' => $max,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        CategoryRepository $categoryRepository,
        ActivityRepository $activityRepository,
        Activity $activity,
        ReservationRepository $reservationRepository,
        ServiceRepository $serviceRepository,
        EntityManagerInterface $entityManager,
        FilterService $filterService,
        SearchFormService $searchFormService
    ): Response {
        $session = $request->getSession();
        $eventId = $session->get('eventId');

        if ($request->isMethod('POST')) {
            $service = $request->request->get('service');
            $setEventId = $request->request->get('eventId');
            $session->set('eventId', $setEventId);
            $service = $request->request->get('service');

            if (!empty($service) && !empty($eventId)) {
                $reservation = $reservationRepository->find($eventId);
                $reservation->setStatus(true);
                $order = new Order();
                $order->setService($serviceRepository->find($service));
                $order->setReservation($reservation);
                $order->setUser($this->getUser());
                $order->setPay(false);
                $session->set('order', $order);
                $entityManager->persist($reservation);
                $entityManager->persist($order);
                $entityManager->flush();
                return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
            }if (empty($service)){
                $this->addFlash('error', "Veuillez choisir une prestation");
            }
            if (empty($eventId)){
                $this->addFlash('error', "Veuillez choisir une date de réservation");
            }
        }
        $data = new Search;
        $dataFilter = new Filter();
        [$min, $max] = $activityRepository->findMinMax($dataFilter,$data);
        $userLocation = [
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        ];
        $filterForm = $filterService->filterActivities($min, $max, $dataFilter);
        $searchForm = $searchFormService->createFormSearch($data);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data,$dataFilter),
                'searchForm' => $searchForm,
                'formFilter' => $filterForm,
                'min' => $min,
                'max' => $max,
            ]);
        }

        $reservations = $activity->getReservations();
        $reservationsFormat = [];
        $currentDateTime = new DateTime();
        foreach ($reservations as $reservation) {
            if (!$reservation->isStatus() && $reservation->getReservationStart() > $currentDateTime) {
                $reservationsFormat[] = [
                    'id' => $reservation->getId(),
                    'start' => $reservation->getReservationStart()->format('Y-m-d H:i'),
                    'end' => $reservation->getReservationEnd()->format('Y-m-d H:i'),
                    'status' => $reservation->isStatus(),
                    'activity_id' => $reservation->getActivity()->getId(),
                    'backgroundColor' => '#759D88',
                    'textColor' => '#FFFFFF',
                ];
            }
        }

        $reservationsJson = json_encode($reservationsFormat);

        return $this->render('/activity/show.html.twig', [
            'activity' => $activity,
            'searchForm' => $searchForm,
            'reservationsJson' => $reservationsJson
        ]);
    }

    #[Route('favorite/add/{id}', name: 'app_activity_add_favorite')]
    public function addFavorite(Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        $activity->addFavorite($user); // Ajouter l'activité aux favoris de l'utilisateur

        $entityManager->persist($user); // Persistez les modifications de l'utilisateur en base de données
        $entityManager->flush();

        return $this->redirectToRoute('app_activity');
        //return new Response('Activité ajoutée en favori avec succès');
    }
    #[Route('favorite/delete/{id}', name: 'app_activity_delete_favorite')]
    public function deleteFavorite(Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        $activity->removeFavorite($user); // Ajouter l'activité aux favoris de l'utilisateur

        $entityManager->persist($user); // Persistez les modifications de l'utilisateur en base de données
        $entityManager->flush();

        return $this->redirectToRoute('app_activity');
        //return new Response('Activité ajoutée en favori avec succès');
    }
}
