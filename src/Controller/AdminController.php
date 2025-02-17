<?php
// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/film/{id}/edit', name: 'admin_film_edit')]
    public function edit(Film $film, Request $request): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Use injected entityManager to persist the changes
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_film_index');
        }

        return $this->render('admin/film/edit.html.twig', [
            'form' => $form->createView(),
            'film' => $film,
        ]);
    }

    #[Route('/admin/film/{id}/delete', name: 'admin_film_delete')]
    public function delete(Film $film): Response
    {
        // Use injected entityManager to remove the film
        $this->entityManager->remove($film);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_film_index');
    }
}
