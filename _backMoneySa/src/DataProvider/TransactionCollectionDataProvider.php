<?php


namespace App\DataProvider;



use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Compte;
use App\Entity\Transaction;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{


    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;
    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;
    /**
     * @var UserRepository
     */
    private UserRepository $utilisateurRepository;

    public function __construct(TransactionRepository  $transactionRepository, TokenStorageInterface $tokenStorage, UserRepository $utilisateurRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Transaction::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): JSONResponse
    {
        $data = [];
        $t = 0;
        if($this->tokenStorage->getToken()->getUser()->getRoles()[0] === "ROLE_AdminSystem"){
            $transactions =  $this->transactionRepository->findAll();
            $i =0 ;
            foreach($transactions as $key => $transaction){

                if($transaction->getDateDepot() !=null){
                    $data[$i]['ttc'] = $transaction->getTTC();
                    $data[$i]['montant'] = $transaction->getMontant();
                    $data[$i]['id'] = $transaction->getId();
                    $data[$i]['date'] = $transaction->getDateDepot()->format('Y-m-d ');
                    $data[$i]['commission'] = $transaction->getFraisSystem();
                    $data[$i]['type'] = "depot";
                    $user = $this->utilisateurRepository->findOneBy(['id'=>$transaction->getUserDepot()->getId()]);
                    $data[$i]['nom'] = $user->getPrenom().' '.$user->getNom();
                }

                $i++;
                $t++;
            }
            foreach($transactions as $key => $transaction){

                if($transaction->getDateRetrait() !=null){
                    $data[$i]['ttc'] = $transaction->getTTC();
                    $data[$i]['montant'] = $transaction->getMontant();
                    $data[$i]['id'] = $transaction->getId();
                    // dd($transaction);
                    $data[$i]['date'] = $transaction->getDateRetrait()->format('Y-m-d');
                    $data[$i]['commission'] = $transaction->getFraisRetrait();
                    $data[$i]['type'] = "retrait";
                    $user = $this->utilisateurRepository->findOneBy(['id'=>$transaction->getUserRetrait()->getId()]);
                    $data[$i]['nom'] = $user->getPrenom().' '.$user->getNom();

                }
                $i++;
                $t++;
            }

        }else{

            $user = $this->tokenStorage->getToken()->getUser()->getId();
            $transactions = $this->transactionRepository->findBy(['userDepot'=>$user]);

            $transactionsR = $this->transactionRepository->findBy(['userRetrait'=>$user]);
            foreach($transactions as $key => $transaction){
                $data[$t]['montant'] = $transaction->getMontant();
                if($transaction->getDateDepot() !=null){
                    $data[$t]['date'] = $transaction->getDateDepot()->format('Y-m-d');
                    $data[$t]['commission'] = $transaction->getTTC();
                    $data[$t]['type'] = "depot";
                }
                $t++;

            }
            foreach($transactionsR as $key => $trans){
                $data[$t]['montant'] = $trans->getMontant();
                if($trans->getDateRetrait() !=null){
                    $data[$t]['commission'] = $transaction->getTTC();
                    $data[$t]['date'] = $trans->getDateRetrait()->format('Y-m-d');
                    $data[$t]['type'] = "retrait";
                }
            }
        }


        return new JSONResponse(['data'=>$data],200);
    }


}
