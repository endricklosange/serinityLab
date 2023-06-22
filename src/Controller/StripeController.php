<?php

namespace App\Controller;

use Stripe;
use App\Entity\Search;
use App\Form\SearchFormType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $form->handleRequest($request);
        if ($order) {
            return $this->render('stripe/index.html.twig', [
                'stripe_key' => $_ENV["STRIPE_KEY"],
                'orderSession' => $order,
                'searchForm' => $form,

            ]);
        }
        else{
            return $this->redirectToRoute('app_home_page');
        }
    }


    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $order = $orderRepository->find($session->get('order')->getId());

        $order->setLastname($request->request->get('lastname'));
        $order->setFirstname($request->request->get('firstname'));
        $order->setPhone($request->request->get('phone'));
        $order->setPay(true);
        $entityManager->persist($order);
        $entityManager->flush();
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create([
            "amount" => $order->getService()->getPrice() * 100,
            "currency" => "eur",
            "source" => $request->request->get('stripeToken'),
            "description" => "Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        $session->remove('order');

        return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    }
}
