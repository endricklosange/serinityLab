<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchFormType;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class UserController extends AbstractController
{
   
    #[Route('/favorite', name: 'app_user_favorite')]
    public function favorite(Request $request, CategoryRepository $categoryRepository, ActivityRepository $activityRepository): Response
    {
        $data = new Search();
        $user = $this->getUser();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data),
                'form' => $form,

            ]);
        }
        return $this->render('/activity/favorites.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'activities' =>  $user->getFavorite(),
            'form' => $form,

        ]);
    }
    #[Route('/reservation', name: 'app_user_reservation')]
    public function reservation(Request $request, CategoryRepository $categoryRepository, ActivityRepository $activityRepository): Response
    {
        $data = new Search();
        $user = $this->getUser();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render('/activity/search.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'activities' =>  $activityRepository->findSearch($data),
                'form' => $form,

            ]);
        }
        return $this->render('/order/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'orders' =>  $user->getOrders(),
            'form' => $form,

        ]);
    }
}
