<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;

final class ProfilDataPersister implements ContextAwareDataPersisterInterface
{

    /**
     * ProfilDataPersister constructor.
     */
    private $manager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager=$entityManager;
    }

    public function supports($data, array $context = []): bool
        {
        return $data instanceof Profil;
        }

        public function persist($data, array $context = [])
        {

            $data->setLibelle($data->getLibelle());
            $this->manager->persist($data);
            $this->manager->flush();


             return $data;
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
