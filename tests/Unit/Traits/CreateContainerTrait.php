<?php

namespace Tests\Unit\Traits;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

trait CreateContainerTrait
{
    private function createTokenContainer(): ContainerInterface
    {
        return $this->createContainer('security.token_storage',
            $this->createMock(TokenStorageInterface::class)
        );
    }

    private function createContainer($serviceId, $serviceObject): ContainerInterface
    {
        $container = $this->createMock(ContainerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container
            ->expects(self::atLeastOnce())
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);

        /** @var MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects(self::atLeastOnce())
            ->method('getUser')
            ->willReturn($this->user);

        /** @var MockObject $tokenStorage */
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects(self::atLeastOnce())
            ->method('getToken')
            ->willReturn($token);

        $container
            ->expects(self::atLeastOnce())
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($tokenStorage);

        $container->expects($this->atLeastOnce())
            ->method('get')
            ->with($serviceId)
            ->willReturn($serviceObject);

        return $container;
    }
}
