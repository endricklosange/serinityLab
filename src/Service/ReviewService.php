<?php

namespace App\Service;

use App\Entity\Review;
use App\Form\ReviewType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewService extends AbstractController
{
    
    public function __construct(protected RequestStack $requestStack)
    {

    }
    
    public function createFormReview($review)
    {
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        return $form; 
    }   
}
