<?php

declare(strict_types=1);

namespace Tests\System\Repository;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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
