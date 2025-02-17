<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class UserRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, User::class);
        $this->logger = $logger;
    }

    public function findOneByEmail(string $email): ?User
    {
        $this->logger->info('Searching for user with email: ' . $email);
        $user = $this->findOneBy(['email' => $email]);

        if (!$user) {
            $this->logger->error('User not found for email: ' . $email);
        }

        return $user;
    }

    public function findOneByUsername(string $username): ?User
    {
        $this->logger->info('Searching for user with username: ' . $username);
        $user = $this->findOneBy(['username' => $username]);

        if (!$user) {
            $this->logger->error('User not found for username: ' . $username);
        }

        return $user;
    }
}
