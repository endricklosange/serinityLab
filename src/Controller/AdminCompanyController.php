<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin/company', name: 'app_admin_company')]
class AdminCompanyController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(CompanyRepository $companyRepository): Response
    {
        return $this->render('admin/company/index.html.twig', [
            'companies' => $companyRepository->findAll(),
        ]);
    }
    #[Route('/new', name: '_new')]
    public function registerAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
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

            return $this->redirectToRoute('app_admin_company_index');
        }

        return $this->render('admin/company/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('admin/company/show.html.twig', [
            'company' => $company,
        ]);
    }
    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, CompanyRepository $companyRepository): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $companyRepository->save($company, true);

            return $this->redirectToRoute('app_admin_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/company/edit.html.twig', [
            'company' => $company,
            'registrationForm' => $form,
        ]);
    }
    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, CompanyRepository $companyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $companyRepository->remove($company, true);
        }

        return $this->redirectToRoute('app_admin_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
