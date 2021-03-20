<?php


namespace App\DataProvider;



use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Agence;
use App\Entity\Compte;
use App\Entity\Depot;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DepotCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var AgenceRepository
     */
    private AgenceRepository $agenceRepository;
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;
    /**
     * @var DepotRepository
     */
    private DepotRepository $depotRepository;

    /**
     * TransactionCollectionDataProvider constructor.
     * @param DepotRepository $depotRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(DepotRepository $depotRepository,TokenStorageInterface $tokenStorage)
    {
        $this->depotRepository = $depotRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Depot::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): JSONResponse
    {
        $data = array();
        $userId = $this->tokenStorage->getToken()->getUser()->getId();
        $role = $this->tokenStorage->getToken()->getUser()->getRoles()[0];
        if($role === "ROLE_AdminSysteme"){
            $depots = $this->depotRepository->findAll();
        }else{
            $depots = $this->depotRepository->findBy(['user'=>$userId]);
        }
        foreach($depots as $key => $depot){
            $data[$key]['date'] = $depot->getCreatedAt()->format('Y-m-d à H:i:s');
            $data[$key]['montant'] = $depot->getMontant();
            $data[$key]['numero'] = $depot->getId();
            $data[$key]['compte'] = $depot->getCompte()->getNumCompte();
            $data[$key]['auteur'] = $depot->getUser()->getPrenom().' '.$depot->getUser()->getNom();
        }

        return new JsonResponse(['data'=>$data]);
    }


}
