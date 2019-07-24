<?php
namespace App\Service;

/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;
/*https://packagist.org/packages/inacho/php-credit-card-validator*/
use Inacho\CreditCard;

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

    public function noFakeTelephone($a) {
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[0-9|\+?]{0,2}[0-9]{9,14}/", "", $a);
        return $invalid;
    }
    
    public function noFakeCCard($date_card, $cvv, $card_nr) {
        $err = [];
        $card = CreditCard::validCreditCard($card_nr);
        if ($card['valid'] == 1) {
            $date = explode('/',$date_card);
            $validCvc = CreditCard::validCvc($cvv, $card['type']) == true ? false : $err[] = 'CVV_INVALID';
            $validDate = CreditCard::validDate($date[1], $date[0])  == true ? false : $err[] = 'DATE_CARD_INVALID';
        }
        
        else
            $err[] = 'CARD_NR_INVALID';
        return $err;
    }


}
