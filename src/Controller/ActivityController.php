<?php

namespace App\Controller;

use App\Entity\Search;
use App\Data\SearchData;
use App\Entity\Activity;
use App\Form\SearchFormType;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/activity')]
class ActivityController extends AbstractController
{
    #[Route('/', name: 'app_activity')]
    public function index(Request $request, ActivityRepository $activityRepository, CategoryRepository $categoryRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Stocker les coordonnées dans les variables de session
            $session = $request->getSession();
            $session->set('latitude', $request->request->get('latitude'));
            $session->set('longitude', $request->request->get('longitude'));
        }
        $session = $request->getSession();
        $userLocation = array(
            'latitude' => $session->get('latitude'),
            'longitude' => $session->get('longitude')
        );
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

        if ($userLocation['latitude'] === null || $userLocation['longitude'] === null) {
            return $this->render('/activity/index.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findBy(
                    [],
                    ['created_at' => 'ASC']
                )
            ]);
        }else{
            return $this->render('/activity/index.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' => $activityRepository->findByLocation($userLocation['latitude'], $userLocation['longitude']),
                'form' => $form,

            ]);
        }
        // Passez les coordonnées à votre vue Twig
    }
    #[Route('/search', name: 'app_activity_search')]
    public function search(ActivityRepository $activityRepository, CategoryRepository $categoryRepository,Request $request): Response
    {

        $data = new Search();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        return $this->render('/activity/search.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'activities' => $activityRepository->findSearch($data),
            'form' => $form,
            
        ]);
    }

    #[Route('/category/{id}', name: 'app_activity_category', methods: ['GET'])]
    public function showByCategory(Request $request,CategoryRepository $categoryRepository,ActivityRepository $activityRepository , int $id): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->redirectToRoute('app_activity', [], Response::HTTP_SEE_OTHER);
        }

        $activities = $category->getActivities();
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
        return $this->render('/activity/category.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findAll(),
            'activities' => $activities,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function show(Request $request,CategoryRepository $categoryRepository,ActivityRepository $activityRepository,Activity $activity): Response
    {
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
        return $this->render('/activity/show.html.twig', [
            'activity' => $activity,
            'form' => $form,

        ]);
    }
}
