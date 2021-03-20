<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Transaction;
use App\Repository\AgenceRepository;
use App\Repository\ClientRepository;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use App\Service\GenererNum;
use App\Service\TransactionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TransactionController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var AgenceRepository
     */
    private $agenceRepository;
    /**
     * @var TransactionService
     */
    private $calculFraisService;
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var CompteRepository
     */
    private $compteRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var GenererNum
     */
    private GenererNum $generator;

    public function __construct(EntityManagerInterface $manager, TransactionService $calculFraisService,
                                TokenStorageInterface $tokenStorage,
                                GenererNum $generator,
                                TransactionRepository $transactionRepository,
                                CompteRepository $compteRepository, AgenceRepository $agenceRepository,
                                SerializerInterface $serializer, ClientRepository $clientRepository
    )
    {

        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
        $this->generator = $generator;
        $this->agenceRepository = $agenceRepository;
        $this->calculFraisService = $calculFraisService;
        $this->compteRepository = $compteRepository;
        $this->transactionRepository = $transactionRepository;
        $this->serializer = $serializer;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/transaction", name="transaction")
     */
    public function addTransaction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->tokenStorage->getToken()->getUser();


        $transaction = new Transaction();
        if ($data['type'] === 'depot') {
            $agence = $this->agenceRepository->findOneBy(['id' => $user->getAgence()->getId()]);
            $compte = $this->compteRepository->findOneBy(['id' => $agence->getCompte()->getId()]);
            if ($data['montant'] > $compte->getSolde()) {
                return new JsonResponse("Impossible d'effectuer le depot", 400, [], true);
            }
            if ($resp = $this->clientRepository->findOneBy(['tel' => $data['clientenvoi']['tel']])) {
                $clientEnvoi = $resp;
            } else {
                $clientEnvoi = $this->serializer->denormalize($data['clientenvoi'], Client::class);
                $nom = $data['clientenvoi']['prenom'].' '.$data['clientenvoi']['nom'];
                $clientEnvoi->setStatus(false);
                $clientEnvoi->setNomComplet($nom);
            }
            if ($resp = $this->clientRepository->findOneBy(['tel' => $data['clientRetrait']['tel']])) {
                $clientRetrait = $resp;
            } else {
                $clientRetrait = $this->serializer->denormalize($data['clientRetrait'], Client::class);
                $nom = $data['clientRetrait']['prenom'].' '.$data['clientRetrait']['nom'];
                $clientRetrait->setStatus(false);
                $clientRetrait->setNomComplet($nom);
            }

            $totalFrais = $this->calculFraisService->calculFrais($data['montant']);

            $tarif = $this->calculFraisService->calculPart($totalFrais);

            $this->manager->persist($clientEnvoi);
            $this->manager->persist($clientRetrait);
            $transaction->setCodeTransaction((int)$this->generator->genrecode('transaction'));
            $transaction->setMontant($data['montant']);
            $transaction->setTTC($totalFrais);
            $transaction->setFraisSystem($tarif['depot']);
            $transaction->setFraisEtat($tarif['etat']);
            $transaction->setFraisRetrait($tarif['retrait']);
            $transaction->setFraisEnvoie($tarif['transfert']);
            $transaction->setDateDepot(new DateTime('now'));

            $transaction->setUserDepot($user);
            $transaction->setClientRetrait($clientRetrait);
            $transaction->setClientEnvoi($clientEnvoi);
            $transaction->setCompte($compte);

            $transaction->setStatus(false);
            $compte->setSolde($compte->getSolde() - $data['montant']);

        } elseif ($data['type'] === 'retrait') {
            $transaction = $this->transactionRepository->findOneBy(['codeTransaction' => $data['codeTransaction']]);
            if ($transaction->getDateRetrait() === null) {
                if ($transaction->getDateAnnulation() === null) {
                    if ($transaction->getClientRetrait()->getTel() === $data['client']['tel']) {
                        $compte = $this->compteRepository->findOneBy(['id' => $transaction->getCompte()->getId()]);
                        $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                        $transaction->setDateRetrait(new \DateTime('now'));
                        $transaction->setUserRetrait($user);
                        $this->manager->persist($transaction);
                        $this->manager->flush();
                        return new JsonResponse(["sms"=>"Ce transfert a  été retirer avec succè "], 200);

                    } else {
                        return new JsonResponse("Ce transfert n'est pas destiner à ce numero de telephone ", 400, [], true);
                    }
                } else {
                    return new JsonResponse("Ce transfert a été annule ", 400, [], true);
                }

            } else {
                return new JsonResponse("Ce transfert a déja été retirer ", 400, [], true);
            }

        }


        $this->manager->persist($transaction);

        $this->manager->flush();
        return new JsonResponse(["data"=>$this->getLastTRansaction()], 200);

    }


    public function DeleteTransaction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $transaction = $this->transactionRepository->findOneBy(['codeTransaction' => $data['codeTransaction']]);
        $idAgenceEnvoi = $transaction->getUserDepot()->getAgence()->getId();
        $idAgenceAnnulation = $this->tokenStorage->getToken()->getUser()->getAgence()->getId();
        if ($idAgenceAnnulation === $idAgenceEnvoi) {
            if ($transaction->getDateRetrait() === null) {
                if ($transaction->getDateAnnulation() === null) {
                    $transaction->setDateAnnulation(new \DateTime);
                    $compte = $transaction->getCompte();
                    $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                    $this->manager->persist($transaction);
                    $this->manager->flush();
                    return new JsonResponse(" transaction annulle  avec succée", 200, [], true);

                } else {
                    return new JsonResponse("Impossible d'annuler le depot car celle ci a deja ete annuler", 400, [], true);
                }

            } else {
                return new JsonResponse("Impossible d'annuler le depot car l,argent a été déja retirer", 400, [], true);
            }
        } else {
            return new JsonResponse("Impossible d'annuler le depot car la transaction n'as pas été effectuer dans cette agence", 400, [], true);


        }
    }


    private function getLastTRansaction(): array
    {
        $data= array();
        $transaction = $this->transactionRepository->findBy([], ['id'=>'DESC'])[0];
        $clientEnvoi = $transaction->getClientEnvoi();
        $clientRetrait = $transaction->getClientRetrait();
        $data['montant'] = $transaction->getMontant();
        $data['code'] = $this->code($transaction->getCodeTransaction());
        $data['dateEnvoi'] = $transaction->getDateDepot()->format('Y-m-d');
        $data['clientEnvoi']['tel'] = $clientEnvoi->getTel();
        $data['clientEnvoi']['nomComplet'] = $clientEnvoi->getNomComplet();
        $data['clientRetrait']['tel'] = $clientEnvoi->getTel();
        $data['clientRetrait']['nomComplet'] = $clientRetrait->getNomComplet();
        return $data;
    }

    private function code($str): string
    {
        if(strlen($str) == 9) {
            $res = substr($str, 0, 3) .'-';
            $res .= substr($str, 3, 3) .'-';
            $res .= substr($str, 6, 9) ;

        }
        return $res;
    }

    public function CalculerFrais(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($data['montant'] > 0){
            $montant = $this->calculFraisService->calculFrais($data['montant']);
            return $this->json(['data'=>$montant], 200);
        }else{
            return $this->json(['sms'=>'Le montant doit etre supérieur à 0'], 400);
        }
    }
    public function DeCalculerFrais(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($data['total'] > 0){
            $montant = $this->calculFraisService->decalculFrais($data['total']);
            return $this->json(['data'=>$montant], 200);
        }else{
            return $this->json(['sms'=>'Le montant doit etre supérieur à 0'], 400);
        }
    }

    public function findTransaction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $result = array();
        $transaction = $this->transactionRepository->findOneBy(['codeTransaction'=>$data['code']]);
        $clientEnvoi = $this->clientRepository->findOneBy(['id'=>$transaction->getClientEnvoi()->getId()]);
        $clientRecepteur = $this->clientRepository->findOneBy(['id'=>$transaction->getClientRetrait()->getId()]);
        $result["clientEnvoi"] = $clientEnvoi;
        $result["clientRecepteur"] = $clientRecepteur;
        $result["montant"] = $transaction->getMontant();
        $result["dateDepot"] = $transaction->getDateDepot()->format('Y-m-d');
        return $this->json(['data'=>$result],200);
    }

}
