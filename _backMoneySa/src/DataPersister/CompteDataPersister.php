<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Compte;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


final class CompteDataPersister implements ContextAwareDataPersisterInterface
{

    /**
     * ProfilDataPersister constructor.
     */
    private $manager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->manager=$entityManager;
        $this->tokenStorage=$tokenStorage;

    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
    }

    public function persist($data, array $context = [])
    {
        $user =$this->tokenStorage->getToken()->getUser();
        $data->setNumCompte($data->getNumCompte());
        $data->setSolde($data->getSolde());
        $data->setAdminSystem($user);
        $this->manager->persist($data);
        $this->manager->flush();


        return $data;
    }

    public function remove($data, array $context = [])
    {

        // call your persistence layer to delete $data
        $data->setStatus(true);
        $this->manager->flush();


        return $data;
    }
}
