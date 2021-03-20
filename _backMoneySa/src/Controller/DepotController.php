<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use App\Service\GenererNum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DepotController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;
    /**
     * @var AgenceRepository
     */
    private AgenceRepository $agenceRepository;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var DepotRepository
     */
    private DepotRepository $depotRepository;
    /**
     * @var GenererNum
     */
    private GenererNum $genererNum;

    /**
     * DepotController constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param CompteRepository $compteRepository
     * @param AgenceRepository $agenceRepository
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param DepotRepository $depotRepository
     * @param GenererNum $genererNum
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                CompteRepository $compteRepository,
                                AgenceRepository $agenceRepository,
                                SerializerInterface $serializer,EntityManagerInterface $manager,
                                DepotRepository $depotRepository,
                                GenererNum $genererNum
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->compteRepository = $compteRepository;
        $this->agenceRepository = $agenceRepository;
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->depotRepository = $depotRepository;
        $this->genererNum = $genererNum;

    }

    public function ADDdepot(Request $request): Response
    {
        $infos = json_decode($request->getContent(),true);
        $depot = $this->serializer->denormalize($infos, Depot::class);
        $user = $this->tokenStorage->getToken()->getUser();

        if(isset($infos['comptes'])){
            $compte = $this->compteRepository->findOneBy(['id' =>$infos['comptes']]);

        }else{
            $agence = $this->agenceRepository->findOneBy(['id' => $user->getAgence()->getId()]);
            $compte = $this->compteRepository->findOneBy(['id' => $agence->getCompte()->getId()]);
        }
        if($infos['montant']> 0){
            $compte->setSolde($compte->getSolde() + $infos['montant']);

        }else{
            return new JsonResponse("le montant doit etre superieiur à 0",400,[],true);
        }

        $depot->setCreatedAt(new \DateTime('now'));
        $depot->setUser($user);
        $depot->setCompte($compte);
        $this->manager->persist($depot);
        $this->manager->flush();
        return new JsonResponse("le depot a été effectuer avec success",200,[],true);

    }

    public function Deletedepot($id){
        $userIdAnnulation = $this->tokenStorage->getToken()->getUser()->getId();
        $lastId= $this->genererNum->getLastIdDepot();
        if($id == $lastId){
            $depot = $this->depotRepository->findOneBy(['id'=>$id]);
            $userIdDepot = $depot->getUser()->getId();
            if ($userIdAnnulation == $userIdDepot){
                $compte = $depot->getCompte();
                $compte->setSolde($compte->getSolde() - $depot->getMontant());
                $this->manager->persist($compte);
                $this->manager->remove($depot);
                $this->manager->flush();
                return new JsonResponse("Depot annuler avec succee", 200, [], true);
            }else{
                return new JsonResponse("Impossible d'annuler cette depot car il a ete effectuer par quelqu'un d'autre", 500, [], true);
            }
        }else{
            return new JsonResponse(" Impossible d'annuler cette depot car il n'est pas le dernier", 500, [], true);
        }
    }


}
