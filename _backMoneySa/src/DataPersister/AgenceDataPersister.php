<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Agence;
use App\Entity\Profil;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AgenceDataPersister implements ContextAwareDataPersisterInterface
{

    /**
     * ProfilDataPersister constructor.
     */
    private EntityManagerInterface $manager;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager,UserRepository $userRepository)
    {
        $this->manager=$entityManager;
        $this->userRepository=$userRepository;
    }

    public function supports($data, array $context = []): bool
        {
        return $data instanceof Agence;
        }

        public function persist($data, array $context = [])
        {
        }

        public function remove($data, array $context = [])
        {

        // call your persistence layer to delete $data
            $data->setStatus(true);
            $this->manager->persist($data);

            foreach ($data->getUsers() as $user){
                $user->setStatus(true);
            }
            $this->manager->flush();

            return $data;
        }
}
