<?php


namespace App\Service;


use App\Repository\CommissionRepository;
use App\Repository\TarifRepository;

class TransactionService
{
    /**
     * @var TarifRepository
     */
    private $tarifRepository;
    /**
     * @var CommissionRepository
     */
    private $commissionRepository;

    /**
     * TransactionService constructor.
     * @param TarifRepository $tarifRepository
     * @param CommissionRepository $commissionRepository
     */
    public function __construct(TarifRepository $tarifRepository, CommissionRepository $commissionRepository)
    {
        $this->commissionRepository = $commissionRepository;
        $this->tarifRepository = $tarifRepository;
    }



    public function calculFrais($montantDepot)
    {
        $data = $this->tarifRepository->findAll();

        $frais = 0;
        foreach ($data as $value){
            if($montantDepot>= 2000000){
                $frais = ($value->getFraisEnvoie()*$montantDepot)/100;
            }else{
                switch ($montantDepot){
                    case $montantDepot>$value->getMontantMin() && $montantDepot<=$value->getMontantMax():
                        $frais = $value->getFraisEnvoie();
                        break;
                }
            }
        }

        return $frais;
    }

    public function decalculFrais($montant)
    {
        $datas = $this->tarifRepository->findAll();
        $data = [];
        $frais = 0;
        foreach ($datas as $value){
            if($montant>= 2000000){
                $frais = ($value->getFraisEnvoie()*$montant)/100;
            }else{
                switch ($montant){
                    case $montant>$value->getMontantMin() && $montant<=$value->getMontantMax():
                        $frais = $value->getFraisEnvoie();
                        break;
                }
            }
        }
        $data['frais'] = $frais;
        $data['montantEnvoi'] = $montant - $frais;
        return $data;
    }


    public function calculPart($montant)
    {
        $data = $this->commissionRepository->findAll();

        $part = array();
        foreach ($data as $value){
            $part['etat']=($montant * $value->getEtat())/100;
            $part['transfert'] = ($montant*$value->getTransfertArgent());
            $part['depot'] = ($montant*$value->getOperateurDepot());
            $part['retrait'] = ($montant*$value->getOperateurRetrait());
        }
        return $part;
    }
}
