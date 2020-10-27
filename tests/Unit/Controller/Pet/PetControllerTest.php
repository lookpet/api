<?php

namespace Tests\Unit\Controller\Pet;

use App\Controller\Pet\PetController;
use App\Dto\Event\RequestUtmBuilderInterface;
use App\Dto\Pet\PetDto;
use App\Dto\Pet\PetDtoBuilderInterface;
use App\Entity\Pet;
use App\Entity\User;
use App\PetDomain\VO\EventContext;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Utm;
use App\Repository\UserEventRepositoryInterface;
use App\Service\PetResponseBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Fixture\PetFixture;
use Tests\Unit\Fixture\UserFixture;
use Tests\Unit\Traits\CreateContainerTrait;

/**
 * @group unit
 * @covers \App\Controller\Pet\PetController
 */
class PetControllerTest extends TestCase
{
    use CreateContainerTrait;

    private PetController $petController;
    private PetResponseBuilderInterface $petResponseBuilder;
    private PetDtoBuilderInterface $petDtoBuilder;
    private EntityManagerInterface $entityManager;
    private RequestUtmBuilderInterface $requestUtmBuilder;
    private UserEventRepositoryInterface $userEventRepository;
    private Request $request;
    private User $user;
    private Utm $utm;

    public function testItCreatesPet(): void
    {
        $petDto = new PetDto();
        $petDto->setType(PetFixture::TYPE);
        $petDto->setSlug(PetFixture::SLUG);
        $petDto->setId(PetFixture::ID);

        $pet = new Pet(
            PetFixture::TYPE,
            PetFixture::SLUG,
                PetFixture::ID
        );

        $this->petDtoBuilder
            ->expects(self::once())
            ->method('build')
            ->with($this->request)
            ->willReturn($petDto);

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('persist');

        $this->entityManager
            ->expects(self::atLeastOnce())
            ->method('flush');

        $this->requestUtmBuilder
            ->expects(self::atLeastOnce())
            ->method('build')
            ->with($this->request)
            ->willReturn($this->utm);

        $this->userEventRepository
            ->expects(self::atLeastOnce())
            ->method('log')
            ->with(
                new EventType(EventType::PET_CREATE),
                $this->user,
                $this->utm,
                EventContext::createByPet($pet)
            );

        $result = json_decode($this->petController->create($this->request)->getContent(), true);
        self::assertSame(PetFixture::SLUG, $result['slug']);
        self::assertSame(PetFixture::TYPE, $result['type']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->petResponseBuilder = $this->createMock(PetResponseBuilderInterface::class);
        $this->petDtoBuilder = $this->createMock(PetDtoBuilderInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->requestUtmBuilder = $this->createMock(RequestUtmBuilderInterface::class);
        $this->userEventRepository = $this->createMock(UserEventRepositoryInterface::class);
        $this->user = new User(UserFixture::ID);
        $this->petController = new PetController(
            $this->petResponseBuilder,
            $this->petDtoBuilder,
            $this->entityManager,
            $this->requestUtmBuilder,
            $this->userEventRepository
        );

        $this->petController->setContainer(
            $this->createTokenContainer()
        );
        $this->request = new Request();
        $this->utm = new Utm();
    }
}
