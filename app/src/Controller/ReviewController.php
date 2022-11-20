<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewFormType;
use App\Repository\BookRepository;
use App\Repository\OrderRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'app_review')]
    public function index(Request $request, EntityManagerInterface $em, BookRepository $bookRepository, ReviewRepository $reviewRepository, OrderRepository $orderRepository): Response
    {
        $page = $request->get('page', 1);
        $user = $this->getUser();
        $form = $this->createForm(ReviewFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if(!$orderRepository->isUserHaveBook($user, $bookRepository->find($request->get('book_id')))){
                throw new Exception('You have not received this book yet');
            }
            
            if($reviewRepository->findOneBy(['owner' => $user, 'book' => $request->get('book_id')])){
                throw new Exception('You are already have a review for this book');
            }

            $review = new Review;
            $review->setText($form->getData()->getText());
            $review->setOwner($user);
            if($form->getData()->getRating()){
                $review->setRating($form->getData()->getRating());
            }
            $review->setBook($bookRepository->find($request->query->get('book_id')));
            
            $em->persist($review);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('review/review.html.twig', [
            'reviewForm' => $form->createView(),
            'reviews' => $reviewRepository->getList($request->query->get('book_id'), $page)
        ]);
    }

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        $user = $this->getUser();

        return $this->render('review/show.html.twig', [
            'review' => $review,
            'user' => $user
        ]);
    }
}
