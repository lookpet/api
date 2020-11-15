<?php

declare(strict_types=1);

namespace Tests\System\Repository;

use App\Entity\User;
use App\Entity\UserEvent;
use App\PetDomain\VO\EventType;
use App\PetDomain\VO\Id;
use App\PetDomain\VO\Utm;
use App\Repository\UserEventRepository;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\DataFixtures\ORM\UserFixtureWithApiToken;
use Tests\DataFixtures\ORM\UserFixtureWithNoPet;
use Tests\DataFixtures\ORM\UserFixtureWithPetComments;

/**
 * @group system
 */
class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    private ?EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;

    public function testFindUsersToNotifyNewPetComments(): void
    {
        $timeInPast = new \DateTimeImmutable('- 1 minute');
        $this->loadFixtures([UserFixtureWithPetComments::class]);

        /** @var User $user */
        $user = $this->userRepository->find(UserFixtureWithPetComments::ID_USER_WITH_PET);
        $user->updateNotificationDate($timeInPast);
        $user->updateNotificationAfterDate($timeInPast);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $result = $this->userRepository->findUsersToNotifyNewPetComments();
        self::assertCount(1, $result);
        self::assertTrue($user->equals($result[0]));
    }

    public function testNotifyNewPetCommentsReturnsEmptyArrayBecauseAfterNotificationDateIsInFuture(): void
    {
        $this->loadFixtures([UserFixtureWithPetComments::class]);

        /** @var User $user */
        $user = $this->userRepository->find(UserFixtureWithPetComments::ID_USER_WITH_PET);
        $user->updateNotificationDate(new \DateTimeImmutable('now'));
        $user->updateNotificationAfterDate(new \DateTimeImmutable('+ 1 day'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $result = $this->userRepository->findUsersToNotifyNewPetComments();
        self::assertCount(0, $result);
    }

    public function testNotifyNewPetCommentsReturnsEmptyArrayBecauseCommentDateIsEarlierThanLastNotificationDate(): void
    {
        $this->loadFixtures([UserFixtureWithPetComments::class]);

        /** @var User $user */
        $user = $this->userRepository->find(UserFixtureWithPetComments::ID_USER_WITH_PET);
        $user->updateNotificationDate(new \DateTimeImmutable('+1 minute'));
        $user->updateNotificationAfterDate(new \DateTimeImmutable('- 1 minute'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $result = $this->userRepository->findUsersToNotifyNewPetComments();
        self::assertCount(0, $result);
    }

    public function testFindUsersToNotifyNoPets(): void
    {
        $timeInPast = new \DateTimeImmutable('-1 minute');
        $this->loadFixtures([UserFixtureWithNoPet::class]);

        /** @var User $user */
        $user = $this->userRepository->find(UserFixtureWithNoPet::ID_USER_WITH_NO_PET);
        $user->updateNotificationAfterDate($timeInPast);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $result = $this->userRepository->findUsersToNotifyNoPets();
        self::assertCount(1, $result);
        self::assertTrue($user->equals($result[0]));
    }

    public function testFindUsersToNotifyNoPetsReturnEmptyArrayBecauseAfterNotificationDateIsInFuture(): void
    {
        $timeInPast = new \DateTimeImmutable('+1 minute');
        $this->loadFixtures([UserFixtureWithNoPet::class]);

        /** @var User $user */
        $user = $this->userRepository->find(UserFixtureWithNoPet::ID_USER_WITH_NO_PET);
        $user->updateNotificationAfterDate($timeInPast);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $result = $this->userRepository->findUsersToNotifyNoPets();
        self::assertCount(0, $result);
    }

    public function testFindUsersToNotifyNoPetsReturnEmptyArrayBecauseMessageHasAlreadyBeenSent(): void
    {
        $timeInPast = new \DateTimeImmutable('-1 minute');
        $this->loadFixtures([UserFixtureWithNoPet::class]);

        /** @var User $user */
        $user = $this->userRepository->find(UserFixtureWithNoPet::ID_USER_WITH_NO_PET);
        $user->updateNotificationAfterDate($timeInPast);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var UserEventRepository $userEventRepository */
        $userEventRepository = $this->entityManager->getRepository(UserEvent::class);
        $userEventRepository->log(new EventType(EventType::NO_PET_NOTIFICATION),
            $user,
            new Utm());

        $result = $this->userRepository->findUsersToNotifyNoPets();
        self::assertCount(0, $result);
    }

    public function testFindUsersToNotifyPollReturnUser(): void
    {
        $this->loadFixtures([UserFixtureWithApiToken::class]);

        $userForTest = $this->userRepository->findByUuid(
            new Id(UserFixtureWithApiToken::ID_USER_WITH_API_TOKEN)
        );
        $activeApiToken = $userForTest->getActiveApiToken();
        $activeApiToken->setCreatedAt(new \DateTimeImmutable('yesterday'));
        $this->entityManager->persist($activeApiToken);
        $this->entityManager->flush();

        $result = $this->userRepository->findUsersToNotifyPoll();
        self::assertTrue($userForTest->equals($result[0]));
    }

    public function testFindUsersToNotifyPollReturnEmptyResultBecauseTimeHavePassed(): void
    {
        $this->loadFixtures([UserFixtureWithApiToken::class]);

        $userForTest = $this->userRepository->findByUuid(
            new Id(UserFixtureWithApiToken::ID_USER_WITH_API_TOKEN)
        );
        $activeApiToken = $userForTest->getActiveApiToken();
        $activeApiToken->setCreatedAt(new \DateTimeImmutable('-4 days'));
        $this->entityManager->persist($activeApiToken);
        $this->entityManager->flush();

        $result = $this->userRepository->findUsersToNotifyPoll();
        self::assertEmpty($result);
    }

    public function testFindUsersToNotifyPollReturnEmptyResultBecauseMessageHasAlreadyBeenSent(): void
    {
        $this->loadFixtures([UserFixtureWithApiToken::class]);

        $userForTest = $this->userRepository->findByUuid(
            new Id(UserFixtureWithApiToken::ID_USER_WITH_API_TOKEN)
        );
        $activeApiToken = $userForTest->getActiveApiToken();
        $activeApiToken->setCreatedAt(new \DateTimeImmutable('-2 hours 59 minutes'));
        $this->entityManager->persist($activeApiToken);
        $this->entityManager->flush();

        /** @var UserEventRepository $userEventRepository */
        $userEventRepository = $this->entityManager->getRepository(UserEvent::class);
        $userEventRepository->log(new EventType(EventType::POLL_NOTIFICATION),
            $userForTest,
            new Utm());

        $result = $this->userRepository->findUsersToNotifyPoll();
        self::assertEmpty($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager
            ->getRepository(User::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
