<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\DTO\UserLoginDTO;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.audit')]
        private LoggerInterface $logger,
        private UserRepository $userRepository
    ) {}

    #[Route('', name: 'user_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $users = $this->userRepository->findBy([], null, $limit, ($page - 1) * $limit);

        return $this->json([
            'status' => true,
            'users' => $users
        ]);
    }

    #[Route('/create', name: 'user_store', methods: ['POST'])]
    public function store(
        #[MapRequestPayload] UserDTO $dto,
        UserPasswordHasherInterface $passwordHasher,
        #[CurrentUser] ?User $user
    ): JsonResponse
    {
        try {
            $newUser = $this->userRepository->saveFromDto($dto, $passwordHasher);

            $isNew = $dto->getId() === null;
            $msg = $isNew ? 'Create user id' : 'Update user id';
            $this->logger->info($msg .  $newUser->getId(), [
                'user_id' => $newUser->getId(),
                'email' => $newUser->getEmail(),
                'initiator' => !$user || $user->getId() === $newUser->getId()
                    ? $newUser->getId()
                    : 'id ' . $user->getId() . ' email ' . $user->getEmail()
            ]);

            return $this->json([
                'status' => true,
                'message' => 'Success'
            ], $isNew ? 201 : 200);
        } catch (\Exception $e) {
            $this->logger->error('User save failed: ' . $e->getMessage());
            return $this->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json([
            'status' => true,
            'user' => $user
        ]);
    }

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] UserLoginDTO $dto,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        RateLimiterFactoryInterface $loginLimiter,
    ): JsonResponse
    {
        $limiter = $loginLimiter->create($request->getClientIp());
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'status' => false,
                'error' => 'Too many attempts'
            ]);
        }

        $user = $this->userRepository->findOneBy(['email' => $dto->email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $dto->password)) {
            return $this->json([
                'status' => false,
                'error' => 'Invalid credentials'
            ]);
        }

        $session = $request->getSession();
        $session->migrate();

        $session->set('user_id', $user->getId());

        $this->logger->info('Login user id ' . $user->getId(), [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);

        return $this->json([
            'status' => true,
            'user' => [
                'id' => $user->getId(),
                'full_name' => $user->getFullName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ],
            'message' => 'Login successful'
        ]);
    }

    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
    public function profile(#[CurrentUser] ?User $user): JsonResponse
    {
        return $this->json([
            'status' => true,
            'user' => $user
        ]);
    }

    #[Route('/logout', name: 'user_logout', methods: ['POST'])]
    public function logout(
        Request $request,
        TokenStorageInterface $tokenStorage,
        #[CurrentUser] ?User $user
    ): JsonResponse
    {
        $this->logger->info('Logout user id ' . $user->getId(), [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);

        $request->getSession()->invalidate();
        $tokenStorage->setToken(null);

        return $this->json([
            'status' => true
        ]);
    }

    #[Route('/user/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(User $user, #[CurrentUser] ?User $current): JsonResponse
    {
        $this->logger->info('Delete user id ' . $user->getId(), [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'initiator' => $current->getId() === $user->getId()
                ? $user->getId()
                : 'id ' . $current->getId() . ' email ' . $current->getEmail()
        ]);

        $this->userRepository->remove($user, true);

        return $this->json([
            'status' => true
        ]);
    }
}
