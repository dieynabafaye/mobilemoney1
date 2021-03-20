<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Admin;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManagerInterface
     */
    private $manager;


    /**
     * UserController constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder=$encoder;
    }


    /**
     * @Route(
     * name="post_users",
     * path="api/admin/users",
     * methods= {"POST"},
     *     defaults={
    "_controller"="app/controller/UserController::addUser",
     *          "_api_collection_operation_name"="add_user"
     *       },
     * )
     * @param UserService $service
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function addUser(UserService $service, Request $request, serializerInterface $serializer, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $profil=$request->request->get("profil");
        $userObj=$service->addUser($profil, $request);

        if (!empty($service->validate($userObj))){
            return new JsonResponse($service->validate($userObj), Response::HTTP_BAD_REQUEST,[]);
        }
        $entityManager->persist($userObj);
        //dd($userObj);
        $entityManager->flush();

        return $this->json('Success', Response::HTTP_OK);
    }


    /**
     * @Route(
     *     path="api/admin/users/{id}",
     *      name="putUserId",
     *     methods={"PUT"},
     *     defaults={
     *      "_api_resource_class"=User::class,
     *      "_api_item_operation_name"="putUserId"
     *     }
     *     )
     * @param UserService $service
     * @param Request $request
     * @return JsonResponse
     */
    public function putUserId(UserService $service, Request $request)
    {
        //dd($request);
        $userUpdate = $service->PutUser($request,'avatar');
        //dd($userUpdate);
        $utilisateur = $request ->attributes->get('data');
        //dd($userUpdate["profil"]);

        //dd($utilisateur);
        foreach ($userUpdate as $key=> $valeur){
            $setter = 'set'. ucfirst(strtolower($key));
            //dd($setter);
            if(method_exists(User::class, $setter)){
                if($setter=='setProfil'){
                    $utilisateur->setProfil($userUpdate["profil"]);
                }
                else{
                    $utilisateur->$setter($valeur);
                }

            }
            if ($setter=='setPassword'){
                $utilisateur->setPassword($this->encoder->encodePassword($utilisateur,$userUpdate['password']));

            }
        }
        //dd($utilisateur);
        $this->manager->persist($utilisateur);
        $this->manager->flush();

        return new JsonResponse("success",200,[],true);


    }
}

