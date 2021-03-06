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
        $feedbacks = $em->getRepository(Feedback::class)->findBy([],['id' => 'DESC']);

        return $this->render('admin/feedback-list.html', [
            'feedbacks' => $feedbacks,
        ]);
    }

    //Change the status or visible of a Feedback
    public function changeStatus(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $feedback = $em->getRepository(Feedback::class)->find($request->request->get('id'));

        if(!$feedback)
            return new JsonResponse([
                'status' => 0,
                'message' => 'fail',
                'data' => 'Feedback com #'.$request->request->get('id').' não encontrado',
            ]);

        $visible = $feedback->getVisible() ? false : true;
        $active = $feedback->getActive() ? false : true;

        if($request->request->get('action') == 'status'){
            $feedback->setActive($active);
            $a = $active ? '<i class="w3-text-green fas fa-check-circle fa-fw"></i>' : '<i class="w3-text-red fas fa-times-circle fa-fw"></i>';
        }    
        else{
            $feedback->setVisible($visible);
            $a = $visible ? '<i class="w3-text-green fas fa-eye fa-fw"></i>' : '<i class="w3-text-red fa-eye-slash fas fa-fw"></i>';
        }
        
        $em->persist($feedback);
        $em->flush();

        return new JsonResponse([
            'status' => 1,
            'message' => 'success',
            'data' => [
                'action' => $request->request->get('action'),
                'html' => $a
            ]
        ]);

    }


    public function sendFeedback(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator, FieldsValidator $fieldsValidator)
    {
        $locale = $request->getLocale();

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);
        
        $form = $this->createForm(FeedbackType::class);
        
        $form->handleRequest($request);
        
        # check if form is submitted
        $err = [];

        if($form->isSubmitted() && $form->isValid() ){

            //IF FIELDS IS NULL PUT IN ARRAY AND SEND BACK TO USER

            //$form['name']->getData() ? $name = $form['name']->getData() : $err[] = 'name';
            $form['email']->getData() ? $email = $form['email']->getData() : $err[] = 'email';
            $form['booking']->getData() ? $booking_nr = $form['booking']->getData() : $err[] = 'booking_nr';
            $form['rate']->getData() ? $rate = $form['rate']->getData() : $err[] = 'rate';
            $form['observations']->getData() ?  $observations = $form['observations']->getData() : $err[] = 'observations';
            
            if($err)
                return new JsonResponse([
                    'status' => 0,
                    'message' => 'fields empty',
                    'data' => $err,
                    'mail' => null
                ]);

            $booking = $em->getRepository(Booking::class)->findOneBy(['id' => $booking_nr, 'status'=> 'confirmed']);
            
            if(!$booking)
                $err[] = 'booking_not_valid';
            
            else
                if($booking->getClient()->getEmail() != $email)
                    $err[] = 'booking_email_invalid';

            //$fieldsValidator->noFakeName($name) ? $err[] = 'invalid_name' : false;
            $fieldsValidator->noFakeEmails($email)? $err[] = 'invalid_email' : false;
            
            $feedback = $em->getRepository(Feedback::class)->findOneBy(['booking' => $booking]);
             
            if($feedback)
                $err[] = 'already_left_feedback';

            if($err)
              return new JsonResponse([
                    'status' => 2,
                    'message' => 'invalid fields',
                    'data' => $err,
                    'mail' => null,
                ]);

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
                    [
                        'id' => $feedback->getBooking()->getId(),
                        'name' => $booking->getClient()->getUsername(),
                        'email' => $booking->getClient()->getEmail(),
                        'rate' => $feedback->getRate(),
                        'logo' => $company->getLinkMyDomain().'/upload/gallery/'.$company->getLogo(),
                        'observations' => $feedback->getObservations()
                    ]),
                    'text/html'
                );
                
            $send = $mailer->send($message);

            return new JsonResponse([
                'status' => 1,
                'message' => 'all valid',
                'data' =>  'success',
                'mail' => $send,
                'locale' => $locale
            ]);
        } 

       return new JsonResponse([
            'status' => 0,
            'message' => 'fail_submit',
            'data' =>  'fail',
            'mail' => null,
            'locale' => null
        ]);  
    
    }

}

?>