<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\BalanceType;
use App\Form\ProfileType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(OrderRepository $orderRepository, Request $request): Response
    {
        $page = $request->get('page', 1);
        $user = $this->getUser();
        
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'orders' => $orderRepository->getList($page, $user)
        ]);
    }

    #[Route('/replenish-balance', name: 'app_replenish_balance', methods: ['GET', 'POST'])]
    public function updateBalance(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(BalanceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setBalance($user->getBalance() + $form->getData()['balance']);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/balance.html.twig', [
            'user' => $user,
            'balanceForm' => $form->createView()
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->getData()->getUsername()) {
                $user->setUsername($form->getData()->getUsername());
            }

            if($form->get('newPassword')->getData()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );
            }
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $this->getUser()
        ]);
    }
}
