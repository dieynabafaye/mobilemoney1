<?php

namespace App\Service;


use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ErrorService
{

    /**
     * ErrorService constructor.
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serialize = $serializer;
    }

    public function error($tabError)
    {
        $error = $this->validator->validate($tabError);
        if (count($error)>0)
        {
            foreach ($error as $errors)
            {
                $tabE[] = $errors->getMessage();
            }
            $tabE = $this->serialize->encode($tabE, 'json');
            dd($tabE);
        }
        return true;
    }

}