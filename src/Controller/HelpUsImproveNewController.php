<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\Company;
use App\Entity\Warning;
use App\Entity\Locales;
use App\Entity\Feedback;
use App\Entity\Booking;
use App\Form\ReportIssueType;
use App\Form\FeedbackType;
use App\Service\RequestInfo;
use App\Service\FieldsValidator;
use App\Service\Menu;


class HelpUsImproveNewController extends AbstractController
{
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }
    
    public function index(Request $request, RequestInfo $reqInfo, Menu $menu, TranslatorInterface $translator)
    {   
        $em = $this->getDoctrine()->getManager();
        //check the user brownser Locale
        $local = $reqInfo->getBrownserLocale($request);
        
        if(!$this->session->get('_locale')){
            $this->session->set('_locale', $local);
            return $this->redirectToRoute('index_new_help_us_improve');
        }
        
        $locales = $em->getRepository(Locales::class)->findAll();
        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);
        $comments = $em->getRepository(Feedback::class)->findBy(['visible' => true,'active' => true]);
        
        $reportIssueForm = $this->createForm(ReportIssueType::class);
        $feedbackForm = $this->createForm(FeedbackType::class);

        return $this->render('help_us_improve/base.html',
            array(
                'locales' => $locales,
                'warning' => $warning,
                'company' => $company,
                'comments' => $comments,
                'host' => $reqInfo->getHost($request),
                'page' => 'index_new_help_us_improve',
                'reportIssueForm' => $reportIssueForm->createView(),
                'feedbackForm' => $feedbackForm->createView(),
                'menu' => $menu->site('index_new_help_us_improve', $translator)
                )
        );
    }



    public function sendReportIssue(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator, FieldsValidator $fieldsValidator)
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

            //NO FAKE DATA LETS USE THE SERVICE FIELDS VALIDATOR
            if($attach)
                $fieldsValidator->onlyImageFiles($attach->guessExtension()) ?  $err[] = 'invalid_file': false;
            
            $fieldsValidator->noFakeName($name) ? $err[] = 'invalid_name' : false;
            $fieldsValidator->noFakeEmails($email) ? $err[] = 'invalid_email' : false;

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
                            'logo' => $company->getLinkMyDomain().'/upload/gallery/'.$company->getLogo(),
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


    public function userTranslation($lang, $page)
    {    
        $this->session->set('_locale', $lang);
        return $this->redirectToRoute($page);
    }

}


?>