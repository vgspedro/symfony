<?php
namespace App\Service;

use App\Entity\Company;
use App\Entity\Information;
use App\Entity\Flat;
use App\Entity\Payment;
use App\Entity\Manager;

use App\Service\PdfGenerator;

use Twig\Environment;

class EmailSender
{	
    private $twig;  
    private $pdf_gen;   

    public function __construct(Environment $twig, PdfGenerator $pdf_gen)
    {
        $this->twig = $twig;
        $this->pdf_gen = $pdf_gen;
    }

    /**
    * Send email of an Information to all the Flats owners emails
    *@param Company Object, Information Object, array Flat Object
    *@return array
    **/
    public function sendInformation(Company $company, Information $information, array $flats){

        $d = [];
        
        foreach ($flats as $n)
            $d[] = $n->getEmail();

        try {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport(
                $company->getEmailSmtp(), 
                $company->getEmailPort(),
                $company->getEmailCertificade()))
                ->setUsername($company->getEmail())
                ->setPassword($company->getEmailPass());
            
            $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message('Informação #'.$information->getId()))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo($d)
                ->setBcc([$company->getEmail(),'condurbvales7@gmail.com'])
                ->setBody(
                    $this->twig->render('emails/information.html',[
                        'information' => $information,
                        'company' => $company
                    ]
                    ),'text/html'
                )
                ->setContentType('text/html')
                ->addPart('Caros condóminos.\nDesde já os melhores cumprimentos!\n\n '.$information->getDescription().' \n\n'.$information->getCreatedAt()->format('d/m/Y/ H:i'), 'text/plain');
                $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://masivi.com');
                $message->setReadReceiptTo($company->getEmail());
                $message->setPriority(2);
            // Send the message
            $mailer->send($message);
         
        return ['status' => 1];
        }
        catch(Exception $e) {
                return ['status' => $e->getMessage()];  
        }
    }

    /**
    * Send email of Payment to a Flat Owner with a pdf
    *@param Company Object, Payment Object
    *@return array
    **/
    public function sendPayment(Company $company, Payment $payment){

        $attachment = new \Swift_Attachment($this->pdf_gen->attach($company, $payment, 'S'), 'Recibo #'.$payment->getId().'.pdf', 'application/pdf');
        
        try {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport(
                $company->getEmailSmtp(), 
                $company->getEmailPort(),
                $company->getEmailCertificade()))
                ->setUsername($company->getEmail())
                ->setPassword($company->getEmailPass());
            
            $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message('Pagamento #'.$payment->getId().' Fracção '.$payment->getFlat()->getTitle()))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo($payment->getFlat()->getEmail())
                ->setBcc([$company->getEmail(),'condurbvales7@gmail.com'])
                ->setBody(
                    $this->twig->render('emails/payment.html',[
                        'payment' => $payment,
                        'company' => $company
                    ]
                    ),'text/html'
                )
                ->setContentType('text/html')    
                ->addPart('Caro condómino '.$payment->getFlat()->getOwnerName(). 
                    '\nDesde já os melhores cumprimentos!
                    \n\nServe o presente para envio de recibo nº'.$payment->getId().
                    ', referente ao periodo de '.$payment->getStartDate()->format('d/m/Y').
                    ' a '.$payment->getEndDate()->format('d/m/Y').', no montante de '
                    .$payment->getAmount().'€, fracção '.$payment->getFlat()->getTitle().', recebido a '.$payment->getPayDate()->format('d/m/Y'), 'text/plain');
                $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://masivi.com');
                $message->setReadReceiptTo($company->getEmail());
                $message->setPriority(1);
                $message->attach($attachment);
            // Send the message
                
            $mailer->send($message);
         
        return ['status' => 1];
        }
        catch(Exception $e) {
                return ['status' => $e->getMessage()];  
        }
    }

    /**
    * Send email of an Information to all the Flats owners emails
    *@param Company Object, Information Object, array Flat Object
    *@return array
    **/
    public function sendPassRecovery(Company $company, Manager $manager, $new){

        try {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport(
                $company->getEmailSmtp(), 
                $company->getEmailPort(),
                $company->getEmailCertificade()))
                ->setUsername($company->getEmail())
                ->setPassword($company->getEmailPass());
            
            $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message('Recuperação Password'))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo($manager->getEmail())
                ->setBcc([$company->getEmail(),'condurbvales7@gmail.com'])
                ->setBody(
                    $this->twig->render('emails/password_recovery.html',[
                        'name' => ucwords($manager->getUsername()),
                        'password' => $new
                    ]
                    ),'text/html'
                )
                ->setContentType('text/html')
                ->addPart('Olá '.$manager->getUsername().', a sua nova password é '.$new, 'text/plain');
                $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://masivi.com');
                $message->setReadReceiptTo($company->getEmail());
                $message->setPriority(2);
            // Send the message
            $mailer->send($message);
         
        return ['status' => 1];
        }
        catch(Exception $e) {
                return ['status' => $e->getMessage()];  
        }
    }

}
