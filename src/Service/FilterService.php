<?php

namespace App\Service;

use App\Entity\Filter;
use App\Form\FilterType;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FilterService extends AbstractController
{


    public function __construct(protected RequestStack $requestStack)
    {
    }


    public function filterActivities($min, $max, $dataFilter )
    {

        $request = $this->requestStack->getCurrentRequest();

        $formFilter = $this->createForm(FilterType::class, $dataFilter, [
            'default_min' => $min,
            'default_max' => $max,
        ]);
        $formFilter->handleRequest($request);
        return $formFilter;
    }
}
