<?php

declare(strict_types=1);

namespace Tests\Functional\V1\Authentication;

use App\Entity\User;
use App\Entity\UserEvent;
use App\PetDomain\VO\EventType;
use App\Repository\UserEventRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserFixture;

/**
 * @group functional
 * @IgnoreAnnotation("dataProvider")
 */
final class RegistrationTest extends WebTestCase
{
    use FixturesTrait;

    private const REGISTER_URL = '/api/v1/authentication/register';

    private ?EntityManager $entityManager;
    private UserRepositoryInterface $userRepository;
    private UserEventRepositoryInterface $userEventRepository;
    private KernelBrowser $client;

    public function testRegistrationSuccess(): void
    {
        $this->loadFixtures();

        $this->client->request(
            Request::METHOD_POST,
            self::REGISTER_URL,
            [
                'firstName' => UserFixture::TEST_USER_FIRST_NAME,
                'email' => UserFixture::TEST_USER_EMAIL,
                'password' => '123456',
            ],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);
        var_dump($content);

        $user = $this->userRepository->findByEmail(UserFixture::TEST_USER_EMAIL);
        self::assertNotEmpty($user->getId());

        /** @var UserEvent $userEvent */
        $userEvent = $user->getEvents()->first();

        self::assertCount(1, $user->getEvents());
        self::assertSame(EventType::REGISTRATION, $userEvent->getType());
        self::assertEqualsWithDelta(
            (new \DateTimeImmutable())->getTimestamp(),
            $userEvent->getCreatedAt()->getTimestamp(),
            5
        );

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(UserFixture::TEST_USER_FIRST_NAME, $content['user']['firstName']);
        self::assertNotEmpty($content['token']);
        self::assertNotEmpty($content['expires_at']);
        self::assertEqualsWithDelta(new \DateTimeImmutable('+ 1 week'), new \DateTimeImmutable($content['expires_at']['date']), 3);
        self::assertSame(3, $content['expires_at']['timezone_type']);
    }

    public function testRegistrationFailsBecauseUserWithSameEmailExists(): void
    {
        $this->loadFixtures([
            UserFixture::class,
        ]);

        $this->client->request(
            Request::METHOD_POST,
            self::REGISTER_URL,
            [
                'email' => UserFixture::TEST_USER_EMAIL,
                'password' => '1234',
            ],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEquals('User already exist', $content['message']);
    }

    /**
     * @dataProvider dataTestRegistrationFailsBecauseInputDataIsNotSet
     *
     * @param array $requestData
     * @param string $responseMessage
     */
    public function testRegistrationFailsBecauseInputDataIsNotSet(array $requestData, string $responseMessage): void
    {
        $this->loadFixtures();

        $this->client->request(
            Request::METHOD_POST,
            self::REGISTER_URL,
            $requestData,
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEquals($responseMessage, $content['message']);
    }

    public function dataTestRegistrationFailsBecauseInputDataIsNotSet(): array
    {
        return [
            [
                [
                    'password' => UserFixture::PASSWORD_GOOD,
                ],
                'Empty email',
            ],
            [
                [
                    'email' => UserFixture::TEST_USER_EMAIL,
                ],
                'Empty password',
            ],
            [
                [
                    'email' => UserFixture::TEST_USER_BAD_EMAIL,
                    'password' => UserFixture::PASSWORD_GOOD,
                ],
                'Invalid email',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = $this->bootKernel()->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->userEventRepository = $this->entityManager->getRepository(UserEvent::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
