<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Filter;
use App\Entity\Search;
use App\Entity\Company;
use App\Form\FilterType;
use App\Form\CompanyType;
use App\Service\FilterService;
use App\Form\RegistrationFormType;
use App\Service\SearchFormService;
use App\Security\LoginAuthenticator;
use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager, ActivityRepository $activityRepository, CategoryRepository $categoryRepository, FilterService $filterService, SearchFormService $searchFormService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
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
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'searchForm' => $searchForm,
        ]);
    }
    #[Route('admin/entreprise', name: 'app_register_company')]
    public function registerAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Access the plainPassword field from the RegistrationFormType form
            $plainPassword = $form->get('user')['plainPassword']->getData();

            // encode the plain password
            $user = $company->getUser();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_COMPANY']);

            $entityManager->persist($company);
            $entityManager->flush();

            // do anything else you need here, like send an email
        }

        return $this->render('admin/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
