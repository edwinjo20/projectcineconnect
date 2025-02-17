<?php
namespace App\Controller\Api;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class AuthController extends AbstractController
{
    private $entityManager;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Decode the request content
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;  // Changed to username
        $password = $data['password'] ?? null;
    
        // Validate input
        if (!$username || !$password) {
            return new JsonResponse(['error' => 'Username and password are required'], Response::HTTP_BAD_REQUEST);
        }
    
        // Find the user by username
        $user = $this->userRepository->findOneByUsername($username);  // Use username
        
        // Check if the user exists and the password is valid
        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
    
        // Create custom payload with username and email
        $payload = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(), // optional, if you need email in the payload
            'roles' => $user->getRoles(),
        ];

        // Generate a JWT token
        $token = $jwtManager->createFromPayload($user, $payload);
    
        // Return the token
        return new JsonResponse(['token' => $token]);
    }
}
