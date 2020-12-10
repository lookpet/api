<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Pet;
use App\Entity\Post;
use App\PetDomain\VO\Post\PostDescription;
use App\Repository\PetRepository;
use App\Repository\PetRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PetToPostCommand extends Command
{
    private const DESCRIPTION = 'Pets to post commands';
    protected static $defaultName = 'post:pet';

    private PetRepositoryInterface $petRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        PetRepository $petRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->petRepository = $petRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription(self::DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Pet[] $allPets */
        $allPets = $this->petRepository->findAll();
        foreach ($allPets as $pet) {
            $post = new Post(
                Uuid::uuid4()->toString(),
                $pet->getUser(),
                new PostDescription(
                    $pet->getAbout()
                ),
                ... $pet->getMedia()
            );
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        }

        return 0;
    }
}
