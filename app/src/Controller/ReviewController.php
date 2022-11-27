<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewFormType;
use App\Repository\BookRepository;
use App\Repository\OrderRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review')]
class ReviewController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
        private readonly ReviewRepository $reviewRepository,
        private readonly OrderRepository $orderRepository,
    ) {
    }

    #[Route('/', name: 'app_review')]
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ReviewFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!($bookId = $request->query->get('book_id'))) {
                throw new HttpException(Response::HTTP_BAD_REQUEST, 'Missing book_id parameter');
            }
            $this->validateReview($bookId);

            $review = new Review;
            $review->setText($form->getData()->getText());
            $review->setOwner($user);
            $review->setRating($form->getData()->getRating());
            $review->setBook($this->bookRepository->find($bookId));
            
            $this->entityManager->persist($review);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('review/review.html.twig', [
            'reviewForm' => $form->createView(),
            'reviews' => $this->reviewRepository->getList(
                $request->query->get('book_id'),
                $request->query->get('page', 1)
            )
        ]);
    }

    private function validateReview($bookId): void
    {
        if (!$this->bookRepository->find($bookId)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Book not found');
        }
        if (!$this->orderRepository->findDeliveredOrderByUserAndBook($this->getUser()->getId(), $bookId)){
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'You have not received this book yet');
        }
        if ($this->reviewRepository->findOneBy(['owner' => $this->getUser(), 'book' => $bookId])){
            throw new Exception('You are already have a review for this book');
        }
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
