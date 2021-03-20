<?php


namespace App\DataProvider;



use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Compte;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CompteCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
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
     * TransactionCollectionDataProvider constructor.
     * @param AgenceRepository $agenceRepository
     * @param TokenStorageInterface $tokenStorage
     * @param CompteRepository $compteRepository
     */
    public function __construct(AgenceRepository  $agenceRepository,TokenStorageInterface $tokenStorage, CompteRepository $compteRepository)
    {
        $this->agenceRepository = $agenceRepository;
        $this->compteRepository = $compteRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Compte::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): JSONResponse
    {

        if($operationName === "getSolde"){
            $idagence = $this->tokenStorage->getToken()->getUser()->getAgence()->getId();
            $solde = $this->agenceRepository->find($idagence)->getCompte()->getSolde();
            return new JSONResponse(['solde'=>(int)$solde]);
        }

        $data = $this->compteRepository->findAll();
        return new JSONResponse($data);

        // gné kanolé
    }


}
