<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Stripe;
use App\Entity\Company;
use App\Entity\Locales;
use App\Entity\ExtraPayment;
use App\Service\RequestInfo;
use Money\Money;
use App\Service\MoneyParser;
use App\Service\MoneyFormatter;
use App\Service\FieldsValidator;

class ExtraPaymentController extends AbstractController
{

    public function create(Stripe $stripe, Request $request, TranslatorInterface $translator, RequestInfo $reqInfo)
    {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        
        $company = $em->getRepository(Company::class)->find(1);

        $local = $request->getLocale();

        $locale = $em->getRepository(Locales::class)->findOneby(['name' => $local]);

        $locales = $em->getRepository(Locales::class)->findAll();
        
        $text = [
            'validate' => $translator->trans('validate', array(), 'messages', $locale->getName()),
            'local' => $translator->trans('local', array(), 'messages', $locale->getName()), 
            'required' => $translator->trans('required', array(), 'messages', $locale->getName()), 
            'next' => $translator->trans('next', array(), 'messages', $locale ->getName()), 
            'description' => $translator->trans('description', array(), 'messages', $locale->getName()), 
            'payment' => $translator->trans('payment', array(), 'messages', $locale->getName()),
            'phone' => $translator->trans('phone', array(), 'messages', $locale->getName()),
            'name' => $translator->trans('name', array(), 'messages', $locale->getName()),
            'amount' => $translator->trans('amount', array(), 'messages', $locale ->getName()),
            'insert_card_n' => $translator->trans('insert_card_n', array(), 'messages', $locale->getName()),
            'pay_now' => $translator->trans('pay_now', array(), 'messages',$locale->getName()),
            'error' => $translator->trans('error', array(), 'messages', $locale->getName()),
            'check' => $translator->trans('check', array(), 'messages', $locale->getName()),
            'success' => $translator->trans('success', array(), 'messages', $locale->getName()),
            'wifi_error' => $translator->trans('wifi_error', array(), 'messages', $locale->getName()),
            'receipt' => $translator->trans('receipt', array(), 'messages', $locale->getName()),
        ];
        
        //$price_by_event->setAmount($moneyParser->parse($price->amount));
        //'price' => $moneyFormatter->format($package->getPrice())

        return $this->render('admin/create-pay-online.html',
            [
                'company' => $company,
                'translator' => $text,
                'locales' => $locales,
                'payment_intent' => $stripe->createUpdatePaymentIntent($company, $request, null),
                'host' => $reqInfo->getHost($request)
            ]);
    }



    public function list(Request $request, TranslatorInterface $translator, RequestInfo $reqInfo, MoneyFormatter $moneyFormatter)
    {

        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $extraPayment = $em->getRepository(ExtraPayment::class)->findBy([],['id' => 'DESC']);

        $r = [];
        
        foreach ( $extraPayment as $e)
            if($e->getObjectType() == 'charge'){
                $r[] = [
                    'id' => $e->getLogObj()->id,
                    'id_' => $e->getId(),
                    'amount' => $moneyFormatter->format(Money::EUR($e->getLogObj()->amount)),
                    'currency' => $company->getCurrency()->getCurrency(),
                    'description' => $e->getLogObj()->description,
                    'email' => $e->getLogObj()->billing_details->email,
                    'phone' => $e->getLogObj()->billing_details->phone,
                    'name' => $e->getLogObj()->billing_details->name,
                    'date' => \DateTime::createFromFormat('U', $e->getLogObj()->created)->format('d/m/Y H:i')
                ];
            }
        
        return $this->render('admin/extra-payment-list.html',
            [
                'company' => $company,
                'host' => $reqInfo->getHost($request),
                'extra_payments' => $r
            ]);
    }

    private function sendEmail(Booking $booking, TranslatorInterface $translator){

        $em = $this->getDoctrine()->getManager();

        $category = $booking->getAvailable()->getCategory();
        $company = $em->getRepository(Company::class)->find(1);
        $client = $booking->getClient();
        $locale = $client->getLocale();

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());       
        
        $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn();
        
        $mailer = new \Swift_Mailer($transport);
                    
        $subject =  $translator->trans('booking').' #'.$booking->getId().' ('.$translator->trans('refund').')';
        
        if($booking->getStripePaymentLogs())
            if($booking->getStripePaymentLogs()->getLogObj())
                $receipt_url = $booking->getStripePaymentLogs()->getLogObj()->receipt_url;

        $message = (new \Swift_Message($subject))
            ->setFrom([$company->getEmail() => $company->getName()])
            ->setTo([$client->getEmail() => $client->getUsername(), $company->getEmail() => $company->getName()])
            ->addPart($subject, 'text/plain')
            ->setBody(
                $this->renderView(
                    'emails/refund-'.$locale ->getName().'.html.twig',
                    [
                        'id' => $booking->getId(),
                        'name' => $client->getUsername(),
                        'company_name' => $company->getName(),
                        'logo' => $company->getLinkMyDomain().'/upload/gallery/'.$company->getLogo(),
                        'receipt' => $translator->trans('receipt'),
                        'refund_txt' => $translator->trans('refund_txt'),
                        'receipt_url' => $receipt_url ? $receipt_url : ''
                    ]
                ),
                'text/html'
            );
        
        return $mailer->send($message);
    }

    public function validateExtraPayment(Request $request, TranslatorInterface $translator, MoneyFormatter $moneyFormatter, MoneyParser $moneyParser)
    {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        $err=[];
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);
        $locale = $request->request->get('locale') ? $request->request->get('locale') : 'pt_PT';
        // validate 1º part of form 
        $description = $request->request->get('description') ? $request->request->get('description') : $err[] = $translator->trans('description', array(), 'messages', $locale);
        $amount = $request->request->get('amount')  ? $request->request->get('amount') : $err[] = $translator->trans('amount', array(), 'messages', $locale);


        if($err)
            return new JsonResponse([
                'status' => 2,
                'message' => 'fail',
                'data' => $err
            ]);

        $charge = $moneyParser->parse($amount);

        $charge->getAmount() < 50 ? $err[] = $translator->trans('min_amount', array(), 'messages', $locale) : false;
        $charge->getAmount() > 500000 ? $err[] = $translator->trans('max_amount', array(), 'messages', $locale) : false;

        if($err)
            return new JsonResponse([
                'status' => 2,
                'message' => 'fail',
                'data' => $err
            ]);

        //$moneyFormatter->format($amount);
        //Money::EUR(0)

        $text = [
            'validate' => $translator->trans('validate', array(), 'messages', $locale),
            'local' => $translator->trans('local', array(), 'messages', $locale), 
            'required' => $translator->trans('required', array(), 'messages', $locale), 
            'next' => $translator->trans('next', array(), 'messages', $locale), 
            'description' => $translator->trans('description', array(), 'messages', $locale), 
            'payment' => $translator->trans('payment', array(), 'messages', $locale),
            'phone' => $translator->trans('phone', array(), 'messages', $locale),
            'name' => $translator->trans('name', array(), 'messages', $locale),
            'amount' => $translator->trans('amount', array(), 'messages', $locale),
            'insert_card_n' => $translator->trans('insert_card_n', array(), 'messages', $locale),
            'pay_now' => $translator->trans('pay_now', array(), 'messages',$locale),
            'error' => $translator->trans('error', array(), 'messages', $locale),
            'check' => $translator->trans('check', array(), 'messages', $locale),
            'success' => $translator->trans('success', array(), 'messages', $locale),
            'wifi_error' => $translator->trans('wifi_error', array(), 'messages',$locale),
            'receipt' => $translator->trans('receipt', array(), 'messages', $locale),
        ];

        return new JsonResponse([
            'status' => 1,
            'message' => 'all fields valid, proceed to user validation fields.',
            'data' => [
                'text' => $text, 
                'payment' => 
                    [
                        'description' => $description,
                        'amount' => $moneyFormatter->format($charge),
                        'a' => $charge,
                        'locale' => $locale
                    ]
                ]
        ]);    

    }


    public function validateUserData(Request $request, TranslatorInterface $translator, FieldsValidator $validator, MoneyParser $moneyParser, MoneyFormatter $moneyFormatter)
    {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        $err=[];
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);
        $locale = $request->request->get('locale') ? $request->request->get('locale') : 'pt_PT';

        //check if all fields have data else push to array err
        $description = $request->request->get('description') ? $request->request->get('description') : $err[] = $translator->trans('description', array(), 'messages', $locale);
        $amount = $request->request->get('amount') ? $request->request->get('amount') : $err[] = $translator->trans('amount', array(), 'messages', $locale);
        $name = $request->request->get('name') ? $request->request->get('name') : $err[] = $translator->trans('name', array(), 'messages', $locale);
        $email = $request->request->get('email') ? $request->request->get('email') : $err[] = 'Email';
        $phone = $request->get('phone') ? $request->request->get('phone') : $err[] = $translator->trans('phone', array(), 'messages', $locale);
 
        if($err)
            return new JsonResponse([
                'status' => 2,
                'message' => 'fail',
                'data' => $err,
            ]);

        $charge = $moneyParser->parse($amount);

        $charge->getAmount() < 50 ? $err[] = $translator->trans('min_amount', array(), 'messages', $locale) : false;
        $charge->getAmount() > 500000 ? $err[] = $translator->trans('max_amount', array(), 'messages', $locale) : false;

        $phone = str_replace('+','00',$phone);
        //NO FAKE DATA
        $validator->noFakeEmails($email) == 1 ? $err[] = $translator->trans('part_seven.email_invalid', array(), 'messages', $locale) : false;

        $validator->noFakeTelephone($phone) == 1 ? $err[] = $translator->trans('part_seven.telephone_invalid', array(), 'messages', $locale) : false;
        
        $validator->noFakeName($name) == 1 ? $err[] = $translator->trans('part_seven.name_invalid', array(), 'messages', $locale) : false;

        if($err)
            return new JsonResponse([
                'status' => 2,
                'message' => 'fail',
                'data' => $err
            ]);

        $text = [
            'validate' => $translator->trans('validate', array(), 'messages', $locale),
            'local' => $translator->trans('local', array(), 'messages', $locale), 
            'required' => $translator->trans('required', array(), 'messages', $locale), 
            'next' => $translator->trans('next', array(), 'messages', $locale), 
            'description' => $translator->trans('description', array(), 'messages', $locale), 
            'payment' => $translator->trans('payment', array(), 'messages', $locale),
            'phone' => $translator->trans('phone', array(), 'messages', $locale),
            'name' => $translator->trans('name', array(), 'messages', $locale),
            'amount' => $translator->trans('amount', array(), 'messages', $locale),
            'insert_card_n' => $translator->trans('insert_card_n', array(), 'messages', $locale),
            'pay_now' => $translator->trans('pay_now', array(), 'messages',$locale),
            'error' => $translator->trans('error', array(), 'messages', $locale),
            'check' => $translator->trans('check', array(), 'messages', $locale),
            'success' => $translator->trans('success', array(), 'messages', $locale),
            'wifi_error' => $translator->trans('wifi_error', array(), 'messages',$locale),
            'receipt' => $translator->trans('receipt', array(), 'messages', $locale),
        ];

        return new JsonResponse([
            'status' => 1,
            'message' => 'all fields valid, proceed to payment.',
            'data' => [
                'text' => $text, 
                'payment' => 
                    [
                        'description' => $description,
                        'amount' => $moneyFormatter->format($charge),
                        'a' => $charge,
                        'locale' => $locale
                    ]
                ]
        ]);    

    }


    /**
    *Create Charge
    *@param $request
    *@return json response of Request
    **/
    public function chargeExtraPayment(Request $request, Stripe $stripe, TranslatorInterface $translator, FieldsValidator $validator, MoneyParser $moneyParser, MoneyFormatter $moneyFormatter)
    {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();
        $amount = $request->request->get('amount');
        $total = $moneyParser->parse($amount);

        $charge = [
            'secret' => $request->request->get('secret'),
            'description' => $request->request->get('description'),
            'amount' => $total->getAmount()
        ];

        $company = $em->getRepository(Company::class)->find(1);

        $i = $stripe->createExtraCharge($company, $charge);

        return $i['status'] == 1 ? 
            new JsonResponse([
                'status' => 1,
                'message' => 'success',
                'data' => $i,
            ]) :
            new JsonResponse([
                'status' => 0,
                'message' => 'Unable to Charge Credit Card',
                'data' => null]);
    }

    /**
    *Get the receipt url to show on email, update/create StripePaymentLog Obj
    *@param $request
    *@return json response of Request
    **/
    public function getCharge(Request $request, Stripe $stripe, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $ch = $stripe->getPaymentCharge($company, $request);

        if($ch['status'] == 1){
            $ePayment = new ExtraPayment();
            $ePayment->setLog(json_encode($ch['data']->data[0]));
            $em->persist($ePayment);
            $em->flush();

            return new JsonResponse([
                'status' => 1,
                'message' => [
                    'text' => 'deu certo', 
                    'status' => null
                ],
                'data' => $ch]);
            }

        return new JsonResponse([
            'status' => 0,
            'message' => 'Unable to Charge the Amount!',
            'data' => null]);
    }



}