<?php


namespace App\Service;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;

class GenererNum
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;
    /**
     * @var DepotRepository
     */
    private DepotRepository $depotRepository;


    public function __construct(CompteRepository $compteRepository,DepotRepository $depotRepository,TransactionRepository $transactionRepository)
    {
        $this->compteRepository = $compteRepository;
        $this->transactionRepository = $transactionRepository;
        $this->depotRepository = $depotRepository;
    }

    public function genrecode($type): string
    {
        $an = Date('Y');
        $an = str_shuffle(((int)$an -  106));
        $cont = $this->getLastCompte($type);
        $long = strlen($cont);
        return str_pad($an, 9-$long, "0").$cont;

    }

    private function getLastCompte($val): int
    {
        if($val === 'compte'){
            $repository = $this->compteRepository;
        }elseif ($val === 'transaction'){
            $repository = $this->transactionRepository;
        }
        $compte = $repository->findBy([], ['id'=>'DESC']);
        if(!$compte){
            $cont= 1;
        }else{
            $cont = ($compte[0]->getId()+1);
        }
        return $cont;
    }

    public function getLastIdDepot(): ?int{
        $ids = $this->depotRepository->findBy([], ['id'=>'DESC']);
        if(!$ids){
            $id= 1;
        }else{
            $id = ($ids[0]->getId());
        }
        return $id;
    }

}
