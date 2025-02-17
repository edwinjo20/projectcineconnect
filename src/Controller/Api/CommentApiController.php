<?php
namespace App\Controller\Api;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/api/comment')]
class CommentApiController extends AbstractController
{
    // GET route to fetch all comments
    #[Route('/', name: 'api_comment_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        // Fetch all comments
        $comments = $entityManager->getRepository(Comment::class)->findAll();
        
        // Prepare data to return
        $commentData = [];
        foreach ($comments as $comment) {
            $commentData[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'approved' => $comment->getApproved(),
                'user' => $comment->getUser()->getUsername(),
                'date' => $comment->getDate()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse($commentData);
    }

    // GET route to fetch pending comments
    #[Route('/pending', name: 'api_comment_pending', methods: ['GET'])]
    public function getPendingComments(EntityManagerInterface $entityManager): JsonResponse
    {
        // Fetch only pending comments (not approved)
        $comments = $entityManager->getRepository(Comment::class)->findBy(['approved' => false]);
        
        // Prepare data to return
        $commentData = [];
        foreach ($comments as $comment) {
            $commentData[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'approved' => $comment->getApproved(),
                'user' => $comment->getUser()->getUsername(),
                'date' => $comment->getDate()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse($commentData);
    }

    // POST route to approve a comment
    #[Route('/approve/{id}', name: 'api_comment_approve', methods: ['POST'])]
    public function approve(Comment $comment, EntityManagerInterface $entityManager): JsonResponse
    {
        // Ensure the user is an admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        $comment->setApproved(true);
        $entityManager->flush();
    
        // Respond with the updated comment status
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Comment approved.',
            'comment' => [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'approved' => $comment->getApproved(),
            ]
        ]);
    }
    
    // DELETE route to delete a comment
    #[Route('/delete/{id}', name: 'api_comment_delete', methods: ['DELETE'])]
    public function delete(Comment $comment, EntityManagerInterface $entityManager): JsonResponse
    {
        // Ensure the user is an admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        $entityManager->remove($comment);
        $entityManager->flush();
    
        // Respond with the deleted comment's ID
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Comment deleted.',
            'comment_id' => $comment->getId(),
        ]);
    }
}
