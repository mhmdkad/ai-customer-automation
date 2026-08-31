<?php

namespace App\Tests\Security;

use App\Security\ApiKeyAuthenticator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class ApiKeyAuthenticatorTest extends KernelTestCase
{
    public function testItSupportsRequestWithAuthorizationHeader(): void
    {
        $authenticator = static::getContainer()->get(ApiKeyAuthenticator::class);

        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer test-api-key');

        $this->assertTrue($authenticator->supports($request));
    }

    public function testItDoesNotSupportRequestWithoutAuthorizationHeader(): void
    {
        $authenticator = static::getContainer()->get(ApiKeyAuthenticator::class);

        $request = Request::create('/');

        $this->assertFalse($authenticator->supports($request));
    }
}
