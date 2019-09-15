<?php
namespace App\Service;
use App\Entity\Company;
use App\Entity\Booking;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\StripePaymentLogs;
use App\Entity\StripeRefundLogs;


class Stripe
{	
    /**
    *Create or update a Payment Intent
    *@param Company, Request Obj
    *@return PaymentIntent Obj
    **/
    public function createUpdatePaymentIntent(Company $company, Request $request, Booking $booking = null){

        if($booking)
            $product = '#'.$booking->getId().'-'.$booking->getAvailable()->getCategory()->getNamePt();
        
        $stripe = new \Stripe\Stripe();
        $intent = new \Stripe\PaymentIntent();
        $stripe->setApiKey($company->getStripeSK());

        try{

            if($request->request->get('secret')){

                $intentId = explode("_secret_", $request->request->get('secret'));
                
                $p_i = $intent->update($intentId[0],[
                    'amount' => $booking->getAmount()->getAmount(),
                    'currency' => $company->getCurrency()->getCurrency(),
                    'description' => $product
                ]);
            }
            else{
                $p_i = $intent->create([
                    'amount' => 50,//$booking->getAmount()->getAmount(),
                    'currency' => $company->getCurrency()->getCurrency(),
                    'description' => 'try_to_buy',
                    'payment_method_types' => ['card']
                ]);
            }

            return [
                'status' => 1,
                'message' => 'success',
                'data' => $p_i
            ];
        }
        
        catch (\Stripe\Error\Authentication $e) {
            
            $body = $e->getJsonBody();
            $err  = $body['error'];
            return [
                'status' => 2,
                'message' => $err,
                'data' => $err['message']
            ];
        }
        
        catch (\Stripe\Error\InvalidRequest $e) {

            $body = $e->getJsonBody();
            $err  = $body['error'];
            return [
                'status' => 2,
                'message' => $err,
                'data' => $err['message']
            ];
        }

    }


    /**
    *Get the receipt url to put on client email
    *@param Company, Request Obj
    *@return Charge Obj
    **/
    public function getPaymentCharge(Company $company, Request $request){

        $paymentIntentId = $request->request->get('pi_id');
        $stripe = new \Stripe\Stripe();
        $charge = new \Stripe\Charge();

        $stripe->setApiKey($company->getStripeSK());
        
        try{
            $c = $charge->all(['payment_intent' => $paymentIntentId]);
            return [
                'status' => 1,
                'message' => 'success',
                'data' => $c
            ];
        }
        catch (\Stripe\Error\Authentication $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            return new JsonResponse([
                'status' => 2,
                'message' => $err,
                'data' => $err['message']
            ]);
        }
        catch (\Stripe\Error\InvalidRequest $e) {

            $body = $e->getJsonBody();
            $err  = $body['error'];
            return new JsonResponse([
                'status' => 2,
                'message' => $err,
                'data' => $err['message']
            ]);
        }
    }

    /**
    *Create a refund, send money back to client
    *@param Company, Request Obj
    *@return Refund Obj
    **/

    public function createRefund(Company $company, Request $request, $paylog){

        $stripe = new \Stripe\Stripe();
        $refund = new \Stripe\Refund();
        $stripe->setApiKey($company->getStripeSK());

        //LETS DO THE REFUND
        try{            
            //CREATE THE REFUND
            $obj = $refund->create([
                "charge" => $paylog,
                "amount" => $request->request->get('amount')*100,
                "reason" => $request->request->get('reason')
            ]);

            return [
                'status' => 1,
                'message' => $obj->status,
                'data' => $obj
            ];

        }
        catch (\Stripe\Error\Authentication $e) {

            $body = $e->getJsonBody();
            $err  = $body['error'];

            return [ 'status' => 0,
                'message' => $err,
                'data' => $err['message']
            ];

        }
        catch (\Stripe\Error\InvalidRequest $e) {

            $body = $e->getJsonBody();
            $err  = $body['error'];
            
            return [  'status' => 0,
                'message' => $err,
                'data' => $err['message']
            ];
        }    
    }



 /**
    *Create refund to client
    *@param OnlinePayments Obj, $charge_id String
    *@return Charge Obj
    **/
    public function retrieveCharge(Company $company, $charge_id){

        $stripe = new \Stripe\Stripe(); 
        $charge = new \Stripe\Charge();
        $stripe->setApiKey($company->getStripeSK());
        
        try{ 
            $chargeObj = $charge->retrieve($charge_id);

            return [
                'status' => 1,
                'message' => 'success',
                'data' => $chargeObj,
            ];
        }
        catch (\Stripe\Error\Authentication $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            return [
                'status' => 2,
                'message' => $err,
                'data' => $err['message']
            ];
        }
        catch (\Stripe\Error\InvalidRequest $e) {

            $body = $e->getJsonBody();
            $err  = $body['error'];

            return [
                'status' => 2,
                'message' => $err,
                'data' => $err['message']
            ];
        } 
    }


    public function refundReasons(TranslatorInterface $translator){
        $reasons = array();

        $reasons[] = ['value' => 'duplicate', 'text' => $translator->trans('duplicate')];
        $reasons[] = ['value' => 'fraudulent', 'text' => $translator->trans('fraudulent')];
        $reasons[] = ['value' => 'requested_by_customer', 'text' => $translator->trans('requested_by_customer')];

        return $reasons;
    }

}
