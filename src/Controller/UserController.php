<?php

namespace App\Controller;

use App\DTO\UserDTO;
use App\Entity\User;
use App\DTO\UserLoginDTO;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users')]
class UserController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.audit')]
        private LoggerInterface $logger,
        private UserRepository $userRepository
    ) {}

    #[Route('', name: 'user_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(Request $request): array
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $users = $this->userRepository->findBy([], null, $limit, ($page - 1) * $limit);

        return [
            'users' => $users,
            'page' => $page,
            'per_page' => $limit
        ];
    }

    #[Route('/create', name: 'user_store', methods: ['POST'])]
    public function store(
        #[MapRequestPayload(validationFailedStatusCode: 200)] UserDTO $dto,
        UserPasswordHasherInterface $passwordHasher,
        #[CurrentUser] ?User $user
    ): array
    {
        try {
            if (empty($dto->getId()) && $user) {
                $dto->setId($user->getId());
            }
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

            return [
                'status' => true,
                'message' => 'Success'
            ];
        } catch (\Exception $e) {
            $this->logger->error('User save failed: ' . $e->getMessage());
            throw new HttpException(200, $e->getMessage());
        }
    }

    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(User $user): array
    {
        return [
            'user' => $user
        ];
    }

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] UserLoginDTO $dto,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        RateLimiterFactoryInterface $loginLimiter,
    ): array
    {
        $limiter = $loginLimiter->create($request->getClientIp());
        if (!$limiter->consume(1)->isAccepted()) {
            throw new HttpException(200, 'Too many attempts');
        }

        $user = $this->userRepository->findOneBy(['email' => $dto->email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $dto->password)) {
            throw new HttpException(200, 'Invalid credentials');
        }

        $session = $request->getSession();
        $session->migrate();

        $session->set('user_id', $user->getId());

        $this->logger->info('Login user id ' . $user->getId(), [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);

        return [
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'full_name' => $user->getFullName(),
                'email' => $user->getEmail()
            ]
        ];
    }

    #[Route('/me', name: 'user_login_check', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(#[CurrentUser] ?User $user): array
    {
        return [
            'roles' => $user->getRoles(),
            'balance' => $user->getBalance()
        ];
    }

    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function profile(#[CurrentUser] ?User $user): array
    {
        return [
            'user' => $user
        ];
    }

    #[Route('/logout', name: 'user_logout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function logout(
        Request $request,
        TokenStorageInterface $tokenStorage,
        #[CurrentUser] ?User $user
    ): array
    {
        $this->logger->info('Logout user id ' . $user->getId(), [
            'user_id' => $user->getId(),
            'email' => $user->getEmail()
        ]);

        $request->getSession()->invalidate();
        $tokenStorage->setToken(null);

        return [
            'message' => 'Logged out'
        ];
    }

    #[Route('/user/{id}', name: 'user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user, #[CurrentUser] ?User $current): array
    {
        $this->logger->info('Delete user id ' . $user->getId(), [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'initiator' => $current->getId() === $user->getId()
                ? $user->getId()
                : 'id ' . $current->getId() . ' email ' . $current->getEmail()
        ]);

        $this->userRepository->remove($user, true);

        return [
            'message' => 'Successfully deleted'
        ];
    }
}
