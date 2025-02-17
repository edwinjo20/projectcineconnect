<?php

namespace App\Controller;

use App\Entity\Favorites;
use App\Form\FavoritesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favorites')]
final class FavoritesController extends AbstractController{
    #[Route(name: 'app_favorites_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $favorites = $entityManager
            ->getRepository(Favorites::class)
            ->findAll();

        return $this->render('favorites/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/new', name: 'app_favorites_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $favorite = new Favorites();
        $form = $this->createForm(FavoritesType::class, $favorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($favorite);
            $entityManager->flush();

            return $this->redirectToRoute('app_favorites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('favorites/new.html.twig', [
            'favorite' => $favorite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_favorites_show', methods: ['GET'])]
    public function show(Favorites $favorite): Response
    {
        return $this->render('favorites/show.html.twig', [
            'favorite' => $favorite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_favorites_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Favorites $favorite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FavoritesType::class, $favorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_favorites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('favorites/edit.html.twig', [
            'favorite' => $favorite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_favorites_delete', methods: ['POST'])]
    public function delete(Request $request, Favorites $favorite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$favorite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($favorite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_favorites_index', [], Response::HTTP_SEE_OTHER);
    }
}
