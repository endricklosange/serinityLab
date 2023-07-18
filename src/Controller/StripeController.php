<?php

namespace App\Controller;

use Stripe;
use App\Entity\Search;
use App\Form\SearchFormType;
use App\Repository\OrderRepository;
use Stripe\Exception\ApiErrorException;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ReferenceGeneratorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $order = $session->get('order');
        $data = new Search();
        $form = $this->createForm(SearchFormType::class, $data);
        // Store a variable in the session that indicates if the code should be executed.
        $session->set('should_execute_code', true);
        $form->handleRequest($request);
        if ($order) {
            return $this->render('stripe/index.html.twig', [
                'stripe_key' => $_ENV["STRIPE_KEY"],
                'orderSession' => $order,
                'searchForm' => $form,

            ]);
        } else {
            return $this->redirectToRoute('app_home_page');
        }
    }   
    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager, ReferenceGeneratorService $referenceGeneratorService): Response
    {
        $session = $request->getSession();
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        try {
            $order = $orderRepository->find($session->get('order')->getId());
            $order->setLastname($request->request->get('lastname'));
            $order->setFirstname($request->request->get('firstname'));
            $order->setPhone($request->request->get('phone'));
            $order->setReference($referenceGeneratorService->generateReference());
            $order->setPay(true);
            $charge = Stripe\Charge::create([
                "amount" => $order->getService()->getPrice() * 100,
                "currency" => "eur",
                "source" => $request->request->get('stripeToken'),
                "description" => "Payment Test"
            ]);
            $order->setIdpayment($charge->id);
            $entityManager->persist($order);
            $entityManager->flush();
        } catch (ApiErrorException) {
            $order->getReservation()->setStatus(false);
            $orderRepository->remove($order, true);
            $this->addFlash(
                'error',
                'Échec du paiement'
            );
            return $this->redirectToRoute('app_activity_show', ['id' => $order->getReservation()->getActivity()->getId()], Response::HTTP_SEE_OTHER);
        }
        $this->addFlash(
            'success',
            'Le paiement réussit'
        );
        $session->remove('order');
        return $this->redirectToRoute('app_user_reservation', [], Response::HTTP_SEE_OTHER);
    }
}
