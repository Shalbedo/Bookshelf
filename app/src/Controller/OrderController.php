<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Order;
use App\Repository\BookRepository;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, BookRepository $bookRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(OrderType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getBalance() <= ( $book = $bookRepository->findOneBy(['id' => $request->query->get('book_id')]) )->getPrice()) {
                throw new Exception("You don't have enough balance");
            }

            $order = new Order;
            $order->setOwner($user);
            $order->addBook($book);
            $order->setPrice($book->getPrice());
            $order->setStatus(Order::STATUS_IN_STOCK);
            $order->setAddress($form->getData()->getAddress());
            $user->addBook($book);
            $user->setBalance($user->getBalance() - $book->getPrice());

            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('order/index.html.twig', [
            'orderForm' => $form->createView()
        ]);
    }
}
