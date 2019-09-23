<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

use App\Entity\Company;
use App\Entity\Feedback;
use App\Entity\Locales;
use App\Entity\Booking;

use App\Form\FeedbackType;

use App\Service\RequestInfo;
use App\Service\FieldsValidator;

class FeedbackController extends AbstractController
{
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }
    

    public function list(Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        $feedbacks = $em->getRepository(Feedback::class)->findAll();
        return $this->render('admin/feedback-list.html', [
            'feedbacks' => $feedbacks,
        ]);
    }




    public function sendFeedback(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator, FieldsValidator $fieldsValidator)
    {
        $locale = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);
        $form = $this->createForm(FeedbackType::class);
        
        $form->handleRequest($request);
        # check if form is submitted and Recaptcha response is success
        $err = array();

        if($form->isSubmitted() && $form->isValid() ){

            //IF FIELDS IS NULL PUT IN ARRAY AND SEND BACK TO USER

            $form['name']->getData() ? $name = $form['name']->getData() : $err[] = 'name';
            $form['email']->getData() ? $email = $form['email']->getData() : $err[] = 'email';
            $form['booking']->getData() ? $booking_nr = $form['booking']->getData() : $err[] = 'booking_nr';
            $form['rate']->getData() ? $rate = $form['rate']->getData() : $err[] = 'rate';
            $observations = $form['observations']->getData();
             
            if($err){
                $response = array(
                    'status' => 0,
                    'message' => 'fields empty',
                    'data' => $err,
                    'mail' => null
                );
                return new JsonResponse($response);
            }

            $booking = $em->getRepository(Booking::class)->findOneBy(['id' => $booking_nr, 'status'=> 'confirmed']);
            
            if(!$booking)
                $err[] = 'booking_not_valid';
            else{
                if($booking->getClient()->getEmail() != $email)
                    $err[] = 'booking_email_invalid';
            }

            $fieldsValidator->noFakeName($name) ? $err[] = 'invalid_name' : false;
            $fieldsValidator->noFakeEmails($email)? $err[] = 'invalid_email' : false;
            
            $feedback = $em->getRepository(Feedback::class)->findOneBy(['booking' => $booking]);
             
            if($feedback)
                $err[] = 'already_left_feedback';

            if($err){
                $response = array(
                    'status' => 2,
                    'message' => 'invalid fields',
                    'data' => $err,
                    'mail' => null,
                );
                return new JsonResponse($response);
            }
            else
            {

                $rate = $rate >= 5 ? 5 : $rate;
                $feedback = new Feedback();
                $feedback->setBooking($booking);
                $feedback->setRate($rate);
                $feedback->setActive(0);
                $feedback->setVisible(0);
                $feedback->setObservations($observations);
                $em->persist($feedback);
                $em->flush();

                $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
                    ->setUsername($company->getEmail())
                    ->setPassword($company->getEmailPass());            
                
                $mailer = new \Swift_Mailer($transport);
                $subject = $translator->trans('send_feedback');

                $message = (new \Swift_Message($subject))
                    ->setFrom([$company->getEmail() => $company->getName()])
                    ->setTo(['test@tarugabenagiltours.pt' => $company->getName()])
                    ->addPart($subject, 'text/plain')
                    ->setBody(
                
                    $this->renderView(
                        'emails/feedback-'.$locale.'.html.twig',
                        array(
                            'id' => $feedback->getBooking()->getId(),
                            'name' => $name,
                            'email' => $email,
                            'rate' =>  $feedback->getRate(),
                            'logo' => 'https://'.$request->getHost().'/upload/gallery/'.$company->getLogo(),
                            'observations' => $feedback->getObservations()
                        )
                    ),
                    'text/html'
                );
                
                $send = $mailer->send($message);
            }
            
            $response = array(
                'status' => 1,
                'message' => 'all valid',
                'data' =>  'success',
                'mail' => $send,
                'locale' => $locale
            );
               return new JsonResponse($response);   
          } 
        $response = array(
                'status' => 0,
                'message' => 'fail_submit',
                'data' =>  'fail',
                'mail' => null,
                'locale' => null);
        return new JsonResponse($response);      
    }
    
}

?>