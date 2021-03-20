<?php


namespace App\Service;


use App\Entity\Admin;
use App\Entity\AdminAgence;
use App\Entity\AdminSystem;
use App\Entity\Apprenant;
use App\Entity\Caissier;
use App\Entity\Cm;
use App\Entity\Formateur;
use App\Entity\User;
use App\Entity\UserAgence;
use App\Repository\AgenceRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;

class UserService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ProfilRepository
     */
    private $profilRepository;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var AgenceRepository
     */
    private $agenceRepository;

    /**
     * UserService constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param SerializerInterface $serializer
     * @param ProfilRepository $profilRepository
     * @param ValidatorInterface $validator
     * @param AgenceRepository $agenceRepository
     */
    public function __construct( UserPasswordEncoderInterface $encoder,
                                 SerializerInterface $serializer,
                                 ProfilRepository $profilRepository,
                                 ValidatorInterface $validator,
                                AgenceRepository $agenceRepository
)
    {
        $this->encoder =$encoder;
        $this->serializer = $serializer;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->agenceRepository = $agenceRepository;
    }
    public function addUser($profil, Request $request){
        $userReq = $request->request->all();

        $uploadedFile = $request->files->get('avatar');
        if($uploadedFile){
            $file = $uploadedFile->getRealPath();
            $avatar = fopen($file,'r+');
            $userReq['avatar']=$avatar;
        }
        if(isset($userReq['agences'])){
            $agence = $this->agenceRepository->findOneBy(['nomAgence'=>$userReq['agences']]);
        }
        if($profil == "AdminSystem"){
            $user = AdminSystem::class;
        }elseif ($profil == "AdminAgence"){
            $user =AdminAgence::class;
        }elseif ($profil == "Caissier"){
            $user =Caissier::class;

        }elseif ($profil == "UserAgence"){
            $user =UserAgence::class;
        }else{
            $user = User::class;
        }
        $idprofil=$this->profilRepository->findOneBy(['libelle'=>$profil])->getId();
        $userReq["profil"]="api/admin/profils/".$idprofil;
        //dd($userReq);
        $newUser = $this->serializer->denormalize($userReq, $user);
        $newUser->setProfil($this->profilRepository->findOneBy(['libelle'=>$profil]));
        $newUser->setStatus(true);
        if(isset($userReq['agences'])){
            $newUser->setAgence($agence);
        }
        $newUser->setPassword($this->encoder->encodePassword($newUser,$userReq['password']));

        return $newUser;
    }

    /**
     * put image of user
     * @param Request $request
     * @param string|null $fileName
     * @return array
     */
    public function PutUser(Request $request,string $fileName = null){
        $raw =$request->getContent();

       // dd($raw);
        //dd($request->headers->get("content-type"));
        $delimiteur = "multipart/form-data; boundary=";
        $boundary= "--" . explode($delimiteur,$request->headers->get("content-type"))[1];
        $elements = str_replace([$boundary,'Content-Disposition: form-data;',"name="],"",$raw);
        //dd($elements);
        $elementsTab = explode("\r\n\r\n",$elements);
        //dd($elementsTab);
        $data =[];
        for ($i=0;isset($elementsTab[$i+1]);$i+=2){
            //dd($elementsTab[$i+1]);
            $key = str_replace(["\r\n",' "','"'],'',$elementsTab[$i]);
            //dd($key);
            if (strchr($key,$fileName)){
                $stream =fopen('php://memory','r+');
                //dd($stream);
                fwrite($stream,$elementsTab[$i +1]);
                rewind($stream);
                $data[$fileName] = $stream;
                //dd($data);
            }else{
                $val=$elementsTab[$i+1];
                //$val = str_replace(["\r\n", "--"],'',base64_encode($elementsTab[$i+1]));
                //dd($val);
                $data[$key] = $val;
               // dd($data[$key]);
            }
        }
            //dd($data);
        $prof=$this->profilRepository->findOneBy(['libelle'=>$data["profil"]]);
        $data["profil"] = $prof;
        //dd($data);
        return $data;

    }


    public function Validate($utilisateur)
    {
        $errorString ='';
        $error = $this->validator->validate($utilisateur);
        if(isset($error) && $error >0){
            $errorString = $this->serializer->serialize($error,'json');
        }
        return $errorString;
    }


}
