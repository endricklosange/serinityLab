<?php

namespace App\Controller;

use App\Entity\Activity;
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
                'activities' => $activityRepository->findByLocation($userLocation['latitude'], $userLocation['longitude'])
            ]);
        }
        // Passez les coordonnées à votre vue Twig
    }

    #[Route('/category/{id}', name: 'app_activity_category', methods: ['GET'])]
    public function showByCategory(CategoryRepository $categoryRepository, int $id): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->redirectToRoute('app_activity', [], Response::HTTP_SEE_OTHER);
        }

        $activities = $category->getActivities();

        return $this->render('/activity/category.html.twig', [
            'category' => $category,
            'categories' => $categoryRepository->findAll(),
            'activities' => $activities,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function show(Activity $activity): Response
    {

        return $this->render('/activity/show.html.twig', [
            'activity' => $activity,
        ]);
    }
}
