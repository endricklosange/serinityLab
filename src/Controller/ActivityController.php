<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/activity')]
class ActivityController extends AbstractController
{
    #[Route('/', name: 'app_activity')]
    public function index(ActivityRepository $activityRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('/activity/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'activities' => $activityRepository->findBy(
                [],
                ['created_at' => 'DESC']
            ),
        ]);
    }

    #[Route('/category/{id}', name: 'app_activity_category', methods: ['GET'])]
    public function showByCategory(CategoryRepository $categoryRepository, int $id): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
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
