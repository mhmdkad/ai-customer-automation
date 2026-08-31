<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private string $apiKey,
    ) {
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $authorization = $request->headers->get('Authorization');

        if ($authorization === null || !str_starts_with($authorization, 'Bearer ')) {
            throw new AuthenticationException('Invalid authorization header');
        }

        $token = substr($authorization, 7);

        if ($token !== $this->apiKey) {
            throw new AuthenticationException('Invalid API key');
        }

        return new SelfValidatingPassport(
            new UserBadge(
                'api-client',
                fn () => new InMemoryUser('api-client', null, ['ROLE_API']),
            ),
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName,
    ): ?Response {
        return null;
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception,
    ): ?Response {
        return new Response(
            json_encode([
                'error' => 'Invalid API key',
            ]),
            Response::HTTP_UNAUTHORIZED,
            ['Content-Type' => 'application/json'],
        );
    }
}
