<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Entity\Review;
use App\Entity\comment;
use App\Form\CommentType;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/film')]
final class FilmController extends AbstractController
{
    // View all films (Everyone can access)
    #[Route(name: 'app_film_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $films = $entityManager
            ->getRepository(Film::class)
            ->findAll();

        return $this->render('film/index.html.twig', [
            'films' => $films,
        ]);
    }

    // Create a new film (Only admins can access)
    #[Route('/new', name: 'app_film_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('app_film_index');
        }

        return $this->render('film/new.html.twig', [
            'film' => $film,
            'form' => $form->createView(),
        ]);
    }

// Show film details and submit a review (Anyone can access)

#[Route('/film/{id}', name: 'app_film_show', methods: ['GET', 'POST'])]
public function show(Request $request, Film $film, EntityManagerInterface $entityManager): Response
{
    // Ensure the user is logged in before allowing any review or comment
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login'); // Redirect to login if the user is not logged in
    }

    $commentForms = [];

    // Loop through each review and create a form for it
    foreach ($film->getReviews() as $review) {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        // Save comment if the form is submitted
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setUser($user);
            $comment->setDate(new \DateTime());
            $comment->setReview($review);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_film_show', ['id' => $film->getId()]);
        }

        // Store the comment form view in the array
        $commentForms[$review->getId()] = $commentForm->createView();
    }

    // Create the review form (for adding a new review)
    $reviewForm = $this->createForm(ReviewType::class, new Review());
    $reviewForm->handleRequest($request);

    // Handle review submission
    if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
        $review = $reviewForm->getData();
        $review->setUser($user); // Set the current logged-in user
        $review->setFilm($film);
        $review->setPublicationDate(new \DateTime());

        $entityManager->persist($review);
        $entityManager->flush();

        return $this->redirectToRoute('app_film_show', ['id' => $film->getId()]);
    }

    return $this->render('film/show.html.twig', [
        'film' => $film,
        'reviews' => $film->getReviews(),
        'commentForms' => $commentForms,
        'reviewForm' => $reviewForm->createView(),
    ]);
}


    // Edit an existing film (Only admins can access)
    #[Route('/{id}/edit', name: 'app_film_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Film $film, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_film_index');
        }

        return $this->render('film/edit.html.twig', [
            'film' => $film,
            'form' => $form->createView(),
        ]);
    }

    // Delete a film (Only admins can access)
    #[Route('/{id}', name: 'app_film_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Film $film, EntityManagerInterface $entityManager): Response
    {
        // Validate CSRF token before deleting
        if ($this->isCsrfTokenValid('delete' . $film->getId(), $request->request->get('_token'))) {
            $entityManager->remove($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_film_index');
    }
}
