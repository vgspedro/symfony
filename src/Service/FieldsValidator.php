<?php
namespace App\Service;

/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;

class FieldsValidator
{

    public function noFakeEmails($email) {
        $invalid = 0;        
        if($email){
            $validator = new \EmailValidator\Validator();
            $validator->isEmail($email) ? false : $invalid = 1;
            $validator->isSendable($email) ? false : $invalid = 1;
            $validator->hasMx($email) ? false : $invalid = 1;
            $validator->hasMx($email) != null ? false : $invalid = 1;
            $validator->isValid($email) ? false : $invalid = 1;
        }
        return $invalid;
    }

    public function noFakeName($a){
        $invalid = 0;
        $matches = array();
        if($a){
            $pattern = '/[º«»!@#$%^&*(),.?":{}|<>0-9\/]/';
            preg_match($pattern, $a, $matches);
        $invalid = count($matches) < 1 ? false : 1;
        }        
        return $invalid;
    }

    public function onlyImageFiles($extension){
        $invalid = 0;
        $allowedExtension = array('jpg', 'jpeg', 'png', 'gif');
        $invalid = in_array($extension, $allowedExtension ) ? false: 1;
    
        return $invalid;
    }

    public function onlyPdfFiles($extension){
        $invalid = 0;
        $allowedExtension = array('pdf');
        $invalid = in_array($extension, $allowedExtension ) ? false: 1;
    
        return $invalid;
    }

    public function noFakeTelephone($telephone){
        $invalid = 0;
        $matches = array();
        if($a){
            $pattern = '/[>0-9]{9,14}/';
            preg_match($pattern, $a, $matches);
        $invalid = count($matches) < 1 ? false : 1;
        }        
        return $invalid;
    }
    
    public function noFakeCvv($cvv){
        $invalid = 0;
        $matches = array();
        if($a){
            $pattern = '/[0-9]{3}/';
            preg_match($pattern, $a, $matches);
        $invalid = count($matches) < 1 ? false : 1;
        }        
        return $invalid;
    }


}
