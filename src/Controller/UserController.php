<?php

namespace App\Controller;

use Stripe;
use DateTime;
use App\Entity\Order;
use App\Entity\Filter;
use App\Entity\Search;
use App\Form\FilterType;
use App\Form\EditUserFormType;
use App\Service\FilterService;
use App\Service\SearchFormService;
use App\Repository\OrderRepository;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Stripe\Exception\ApiErrorException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


#[Route('/user')]
#[IsGranted('ROLE_USER')]

class UserController extends AbstractController
{

    #[Route('/favorite', name: 'app_user_favorite')]
    public function favorite(Request $request, CategoryRepository $categoryRepository, ActivityRepository $activityRepository, FilterService $filterService, SearchFormService $searchFormService): Response
    {
        $user = $this->getUser();

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
        return $this->render('/activity/favorites.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'activities' =>  $user->getFavorite(),
            'searchForm' => $searchForm,

        ]);
    }
    #[Route('/reservation', name: 'app_user_reservation')]
    public function reservation(Request $request, CategoryRepository $categoryRepository, ActivityRepository $activityRepository, FilterService $filterService, SearchFormService $searchFormService): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();
        $currentDateTime = new DateTime();
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
        return $this->render('/order/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'orders' =>  $user->getOrders(),
            'searchForm' => $searchForm,
            'currentDateTime' => $currentDateTime,
        ]);
    }
    #[Route('/reservation/{id}', name: 'app_user_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
            try {
                $stripe = new \Stripe\StripeClient($_ENV["STRIPE_SECRET"]);
                $stripe->refunds->create([
                    'charge' => base64_decode($order->getIdpayment()),
                ]);
                $this->addFlash(
                    'success',
                    'Le remboursement a réussi'
                );
                $order->getReservation()->setStatus(false);
                $orderRepository->remove($order, true);
                return $this->redirectToRoute('app_user_reservation');
            } catch (ApiErrorException) {
                $this->addFlash(
                    'error',
                    'Erreur lors du remboursement '
                );
                return $this->redirectToRoute('app_user_reservation');
            }
        }
    }
    #[Route('/edit', name: 'app_user_edit')]
    public function edit(Request $request, ActivityRepository $activityRepository, CategoryRepository $categoryRepository, FilterService $filterService, SearchFormService $searchFormService, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);
        $session = $request->getSession();
        $data = new Search();
        $dataFilter = new Filter();
        [$min, $max] = $activityRepository->findMinMax($dataFilter, $data);
        $userLocation = [
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        ];
        $filterForm = $filterService->filterActivities($min, $max, $dataFilter);
        $searchForm = $searchFormService->createFormSearch($data);
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            [$min, $max] = $activityRepository->findMinMax($dataFilter, $data);
            $filterForm = $this->createForm(FilterType::class, $dataFilter, [
                'default_min' => $min,
                'default_max' => $max,
            ]);
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findSearch($data, $dataFilter),
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
            // Hash the password if it has been changed
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword !== null) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre compte a été mis à jour.');
    
            return $this->redirectToRoute('app_user_edit');
        }
    
        return $this->render('user/edit.html.twig', [
            'formEdit' => $form->createView(),
            'searchForm' => $searchFormService->createFormSearch($data),
            'formFilter' => $filterForm,
        ]);
    }
    
    
    
}
