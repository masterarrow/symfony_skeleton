<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SessionAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->getSession() && $request->getSession()->has('user_id');
    }

    public function authenticate(Request $request): Passport
    {
        $userId = $request->getSession()->get('user_id') ?? '';
        $user = $this->userRepository->find($userId);

        if (!$user) {
            $request->getSession()->invalidate();
            throw new CustomUserMessageAuthenticationException('User not found');
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), fn() => $user)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Continue the request
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'status' => false,
            'data' => [
                'message' => 'Authentication required'
            ]
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException|null $authException = null): Response
    {
        return new JsonResponse([
            'status' => false,
            'data' => [
                'message' => 'Authentication required'
            ]
        ], Response::HTTP_UNAUTHORIZED);
    }
}
