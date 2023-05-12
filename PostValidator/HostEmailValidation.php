<?php

namespace Maatify\PostValidator;
use EmailValidation\EmailValidatorFactory;

class HostEmailValidation
{
    public static function HostEmailValidation(string $email): bool
    {
        $validator = EmailValidatorFactory::create($email);
        $arrayResult = $validator->getValidationResults()->asArray();
        if($arrayResult['valid_format'] && $arrayResult['valid_mx_records'] && $arrayResult['valid_host']){
            return true;
        }
        return false;
    }

}