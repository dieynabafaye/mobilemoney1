<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Entity\Compte;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\UserAgenceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AgenceController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var UserAgenceRepository
     */
    private $userAgence;
    /**
     * @var AgenceRepository
     */
    private $agence;
    /**
     * @var CompteRepository
     */
    private $compteRepository;

    /**
     * AgenceController constructor.
     * @param AgenceRepository $agence
     * @param UserAgenceRepository $userAgence
     * @param EntityManagerInterface $manager
     * @param CompteRepository $compteRepository
     */
    public function __construct(AgenceRepository $agence, UserAgenceRepository $userAgence, EntityManagerInterface $manager, CompteRepository $compteRepository)
    {
        $this->agence = $agence;
        $this->userAgence = $userAgence;
        $this->manager = $manager;
        $this->compteRepository = $compteRepository;
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function AddAgence(Request $request, SerializerInterface $serializer, TokenStorageInterface $tokenStorage): Response
    {
        $req = json_decode($request->getContent(),true);
        $agence = $serializer->denormalize($req, Agence::class);

        if (isset($req['comptes']))
        {
            if (isset($req['comptes']['id']))
            {
                $compte = $this->compteRepository->findOneBy(['id'=>$req['comptes']['id']]);
                $agence->setCompte($compte);
            }
            else{

                $compte = $serializer->denormalize($req['comptes'], Compte::class);
                $compte->setAdminSystem($tokenStorage->getToken()->getUser());
                $this->manager->persist($compte);
                $agence->setCompte($compte);
            }
        }
        if (isset($req['userAgences']))
        {
            foreach ($req['userAgences'] as $user)
            {
               if ( $agent = $this->userAgence->find($user['id'])){
                   $agence->addUser($agent);
               }

            }
        }
        $this->manager->persist($agence);
        $this->manager->flush();

        return new JsonResponse(" Agence crée  avec succés", 200, [], true);

    }
}
