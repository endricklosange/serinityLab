<?php

namespace App\Service;

use App\Entity\Search;
use App\Form\SearchFormType;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchFormService extends AbstractController
{
    
    public function __construct(protected RequestStack $requestStack)
    {

    }
    
    public function createFormSearch($searchData)
    {
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm(SearchFormType::class, $searchData);
        $form->handleRequest($request);
        return $form;
    }   
}
