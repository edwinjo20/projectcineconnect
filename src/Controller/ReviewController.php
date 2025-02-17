<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review')]
final class ReviewController extends AbstractController
{
    // Index: List all reviews
    #[Route(name: 'app_review_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reviews = $entityManager
            ->getRepository(Review::class)
            ->findAll();

        return $this->render('review/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    // New review: Create a new review
    #[Route('/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    // Show a single review
    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    // Edit review: Update an existing review
// Inside ReviewController

#[Route('/review/{id}/edit', name: 'app_review_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Review $review, EntityManagerInterface $entityManager): Response
{
    // Ensure the logged-in user is the owner of the review
    if ($review->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException('You are not allowed to edit this review.');
    }

    $form = $this->createForm(ReviewType::class, $review);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_film_show', ['id' => $review->getFilm()->getId()]);
    }

    return $this->render('review/edit.html.twig', [
        'review' => $review,
        'form' => $form->createView(),
    ]);
}

    
    // Delete review: Remove an existing review
    #[Route('/review/{id}/delete', name: 'app_review_delete', methods: ['POST'])]
    public function deleteReview(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        // Validate the CSRF token
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_film_show', ['id' => $review->getFilm()->getId()]);
    }
    
    
}    