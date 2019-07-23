<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\Company;
use App\Entity\Feedback;
use App\Entity\Warning;
use App\Entity\Locales;
use App\Entity\Booking;
use App\Form\ReportIssueType;
use App\Form\FeedbackType;
/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;
use App\Service\RequestInfo;

class HelpImproveController extends AbstractController
{
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }
    
    public function helpImprove(Request $request, RequestInfo $reqInfo)
    {   
        $em = $this->getDoctrine()->getManager();
        //check the user brownser Locale
        $local = $reqInfo->getBrownserLocale($request);
        
        if(!$this->session->get('_locale')){
            $this->session->set('_locale', $local);
            return $this->redirectToRoute('index_help_improve');
        }
        
        $locales = $em->getRepository(Locales::class)->findAll();
        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);
        
        $reportIssueForm = $this->createForm(ReportIssueType::class);
        $feedbackForm = $this->createForm(FeedbackType::class);

        return $this->render('help_improve.html.twig', 
            array(
                'locales' => $locales,
                'warning' => $warning,
                'company' => $company,
                'host' => $reqInfo->getHost($request),
                'page' => 'index_help_improve',
                'reportIssueForm' => $reportIssueForm->createView(),
                'feedbackForm' => $feedbackForm->createView()
                )
            );
    }


    public function sendReportIssue(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $domain = $request->getHttpHost();
        $company = $em->getRepository(Company::class)->find(1);
        $form = $this->createForm(ReportIssueType::class);

        $form->handleRequest($request);
        # check if form is submitted and Recaptcha response is success
        $err = array();
        if($form->isSubmitted() && $form->isValid() ){
            //IF FIELDS IS NULL PUT IN ARRAY AND SEND BACK TO USER
            $form['name']->getData() ? $name = $form['name']->getData() : $err[] = 'name';
            $form['email']->getData() ? $email = $form['email']->getData() : $err[] = 'email';
            $observations = $form['observations']->getData();
            $attach = $form['image']->getData() ? $attach = $form['image']->getData() : null;
             
            if($err){
                $response = array(
                    'status' => 0,
                    'message' => 'fields empty',
                    'data' => $err,
                    'mail' => null,
                );
                return new JsonResponse($response);
            }
            //NO FAKE DATA
            if($attach)
            $attach->guessExtension() == 'jpg' || $attach->guessExtension() == '.jpeg' || $attach->guessExtension() == 'png' ? $err[] = 'invalid_file' : false;
            $this->noFakeEmails($email) == 1 ? $err[] = 'invalid_email' : false;

            if($err){
                $response = array(
                    'status' => 2,
                    'message' => 'invalid fields',
                    'data' => $err,
                    'mail' => 'null',
                );
                return new JsonResponse($response);
            }
            else
            {

                $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
                    ->setUsername($company->getEmail())
                    ->setPassword($company->getEmailPass());            
                
                $mailer = new \Swift_Mailer($transport);
                $subject = $translator->trans('report_issue');

                $message = (new \Swift_Message($subject))
                    ->setFrom([$company->getEmail() => $company->getName()])
                    ->setTo(['test@tarugabenagiltours.pt'=> $company->getName(), $email => $name])
                    ->addPart($subject, 'text/plain')
                    ->setBody(
                
                $this->renderView(
                        'emails/report-issue-'.$locale.'.html.twig',
                        array(
                            'name' => $name,
                            'email' => $email,
                            'observations' => $observations,
                            'logo' => 'https://'.$request->getHost().'/upload/gallery/'.$company->getLogo(),
                        )
                    ),
                    'text/html'
                );
                
                if($attach){
                    $attachment = \Swift_Attachment::fromPath($attach)
                    ->setFilename($attach->getClientOriginalName())
                    ->setContentType('application/'.$attach->guessExtension());
                    ;
                    $message->attach($attachment);
                }
                
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

    public function sendFeedback(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator)
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
                    'mail' => null,
                    'locale' => $locale
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

            $this->noFakeName($name) == 1 ? $err[] = 'invalid_name' : false;
            $this->noFakeEmails($email) == 1 ? $err[] = 'invalid_email' : false;
            
            $feedback = $em->getRepository(Feedback::class)->findOneBy(['booking' => $booking]);
             
            if($feedback)
                $err[] = 'already_left_feedback';

            if($err){
                $response = array(
                    'status' => 2,
                    'message' => 'invalid fields',
                    'data' => $err,
                    'mail' => 'null',
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




    public function userTranslation($lang, $page)
    {    
        $this->session->set('_locale', $lang);
        return $this->redirectToRoute($page);
    }

    private function sendEmail(\Swift_Mailer $mailer, Booking $booking, $domain){
        $em = $this->getDoctrine()->getManager();
        $category = $booking->getAvailable()->getCategory();
        $company = $em->getRepository(Company::class)->find(1);
        $client = $booking->getClient();
        $locale = $client->getLocale();
        $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $locale]);

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());       
        $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn();
        $mailer = new \Swift_Mailer($transport);
                    
        $subject ='Reserva / Order #'.$booking->getId().' ('.$this->translateStatus('PENDING', $locale->getName()).')';
                    
        $message = (new \Swift_Message($subject))
            ->setFrom([$company->getEmail() => $company->getName()])
            ->setTo([$client->getEmail() => $client->getUsername(), $company->getEmail() => $company->getName()])
            ->addPart($subject, 'text/plain')
            ->setBody(
                $this->renderView(
                    'emails/booking-'.$locale ->getName().'.html.twig',
                    array(
                        'id' => $booking->getId(),
                        'username' => $client->getUsername(),
                        'email' => $client->getEmail(),
                        'status' => $this->translateStatus('PENDING', $locale->getName()),
                        'tour' => $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn(),
                        'date' => $booking->getAvailable()->getDatetimeStart()->format('d/m/Y'),
                        'hour' =>  $booking->getAvailable()->getDatetimeStart()->format('H:i'),
                        'adult' => $booking->getAdult(),
                        'children' => $booking->getChildren(),
                        'baby' => $booking->getBaby(),
                        'wp' => $category->getWarrantyPayment(),
                        'logo' => 'https://'.$domain.'/upload/gallery/'.$company->getLogo(),
                        'terms' => !$terms ? '' : $terms->getName(),
                        'terms_txt' => !$terms ? '' : $terms->getTermsHtml(),
                        'company_name' => $company->getName()
                    )
                ),
                'text/html'
            );
            $send = $mailer->send($message);
    }
    
    private function noFakeEmails($email) {
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

    private function noFakeName($a){
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[^!@#\$%\^&\*\(\)\[\]:;]/", "", $a);
        return $invalid;
    }

}


?>