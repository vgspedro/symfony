<?php
namespace App\Service;

use App\Entity\Company;
use App\Entity\TermsConditions;
use App\Entity\Booking;

use Symfony\Component\Translation\TranslatorInterface;

use App\Service\PdfGenerator;

use Twig\Environment;

class EmailSender
{	
    private $twig;  
    private $pdf_gen;
    private $translator;     

    public function __construct(Environment $twig, PdfGenerator $pdf_gen, TranslatorInterface $translator)
    {
        $this->twig = $twig;
        $this->pdf_gen = $pdf_gen;
        $this->translator = $translator;
    }

    /**
    * Send email of Booking to Client with a pdf
    *@param Company Object, Booking Object, Terms Conditions Object
    *@return array
    **/
    public function sendBooking(Company $company, Booking $booking, TermsConditions $terms){

        $pdf = $this->pdf_gen->voucher($company, $booking, $terms, 'S');

        $attachment = $pdf['status'] == 1
            ? 
            new \Swift_Attachment($pdf['pdf'], $this->translator->trans('booking').'#'.$booking->getId().'.pdf', 'application/pdf')
            : 
            false;

        $tour = $booking->getClient()->getLocale()->getName() == 'pt_PT' 
        ? 
            $booking->getAvailable()->getCategory()->getNamePt()
        :
            $booking->getAvailable()->getCategory()->getNameEn();

        try {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());       
        
            $mailer = new \Swift_Mailer($transport);
                    
            $subject = $tour.' '.$this->translator->trans('booking').' #'.$booking->getId().' ('.$this->translator->trans('pending').')';
            
            $receipt_url = '';

            $text = $this->translator->trans('hello').' '.$booking->getClient()->getUsername().'! \n'.
                $this->translator->trans('your_booking').' #'.$booking->getId().' - '. $tour.' '.
                $this->translator->trans('is').' '.$this->translator->trans('pending').', '.
                $this->translator->trans('soon_new_email_status').'\n'.
                $this->translator->trans('in_attach_info');

            //Get the Receipt url if exits
            if($booking->getStripePaymentLogs())
                if($booking->getStripePaymentLogs()->getLogObj())
                    $receipt_url = $booking->getStripePaymentLogs()->getLogObj()->receipt_url;

            $message = (new \Swift_Message($subject))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo([
                    $booking->getClient()->getEmail() => $booking->getClient()->getUsername(),
                    $company->getEmail() => $company->getName()])
                ->addPart($text, 'text/plain')
                ->setBody(
                    $this->twig->render(
                        'emails/booking.html',
                        [
                            'tour' => $tour,
                            'booking' => $booking,
                            'client' => $booking->getClient(),
                            'status' => 'pending',
                            'company' => $company,
                            'receipt_url' => $receipt_url
                        ]
                    ),
                'text/html');
            $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://tarugabenagiltours.pt');
            $message->setReadReceiptTo($company->getEmail());
            $message->setPriority(2);

            $attachment ? $message->attach($attachment) : false;

            $mailer->send($message);

            return ['status' => 1];
        }

        catch(Exception $e) {
            return ['status' => $e->getMessage()];  
        }
    }


    /**
    * Send email of Booking to Client with a pdf
    *@param Company Object, Booking Object, Terms Conditions Object
    *@return array
    **/
    public function sendBookingStatus(Company $company, Booking $booking, TermsConditions $terms){

        $pdf = $this->pdf_gen->voucher($company, $booking, $terms, 'S');

        $attachment = $pdf['status'] == 1
            ? 
            new \Swift_Attachment($pdf['pdf'], $this->translator->trans('booking').'#'.$booking->getId().'.pdf', 'application/pdf')
            : 
            false;

        $tour = $booking->getClient()->getLocale()->getName() == 'pt_PT' 
        ? 
            $booking->getAvailable()->getCategory()->getNamePt()
        :
            $booking->getAvailable()->getCategory()->getNameEn();

        try {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());       
        
            $mailer = new \Swift_Mailer($transport);
                    
            $subject = $tour.' '.$this->translator->trans('booking').' #'.$booking->getId().' ('.$this->translator->trans($booking->getStatus()).')';
            
            $text = $this->translator->trans('hello').' '.$booking->getClient()->getUsername().'! \n'.
                $this->translator->trans('your_booking').' #'.$booking->getId().' - '. $tour.' '.
                $this->translator->trans('is').' '.$this->translator->trans($booking->getStatus()).', '.$booking->getNotes();

            $message = (new \Swift_Message($subject))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo([
                    $booking->getClient()->getEmail() => $booking->getClient()->getUsername(),
                    $company->getEmail() => $company->getName()])
                ->addPart($text, 'text/plain')
                ->setBody(
                    $this->twig->render(
                        'emails/booking-status.html',
                        [
                            'tour' => $tour,
                            'booking' => $booking,
                            'client' => $booking->getClient(),
                            'status' => 'pending',
                            'company' => $company,
                        ]
                    ),
                'text/html');
            $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://tarugabenagiltours.pt');
            $message->setReadReceiptTo($company->getEmail());
            $message->setPriority(1);

            $attachment ? $message->attach($attachment) : false;

            $mailer->send($message);

            return ['status' => 1];
        }

        catch(Exception $e) {
            return ['status' => $e->getMessage()];  
        }
    }



    /**
    * Send email of Booking Refund to Client
    *@param Company Object, Booking Object
    *@return array
    **/
    public function sendBookingRefund(Company $company, Booking $booking /*,TermsConditions $terms*/){

/*
        $pdf = $this->pdf_gen->voucher($company, $booking, $terms, 'S');

        $attachment = $pdf['status'] == 1
            ? 
            new \Swift_Attachment($pdf['pdf'], $this->translator->trans('booking').'#'.$booking->getId().'.pdf', 'application/pdf')
            : 
            false;
*/
        $tour = $booking->getClient()->getLocale()->getName() == 'pt_PT' 
        ? 
            $booking->getAvailable()->getCategory()->getNamePt()
        :
            $booking->getAvailable()->getCategory()->getNameEn();

        try {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());
        
            $mailer = new \Swift_Mailer($transport);
                    
            $subject = $tour.' '.$this->translator->trans('booking').' #'.$booking->getId().' ('.$this->translator->trans('refund').')';
            
            $text = $this->translator->trans('hello').' '.$booking->getClient()->getUsername().'! \n'.
                $this->translator->trans('your_booking').' #'.$booking->getId().' - '. $tour.' '.
                $this->translator->trans('is').' '.$this->translator->trans('refund');
            
            $receipt_url ='';

            if($booking->getStripePaymentLogs())
                if($booking->getStripePaymentLogs()->getLogObj())
                    $receipt_url = $booking->getStripePaymentLogs()->getLogObj()->receipt_url;

            $message = (new \Swift_Message($subject))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo([
                    $booking->getClient()->getEmail() => $booking->getClient()->getUsername(),
                    $company->getEmail() => $company->getName()])
                ->addPart($text, 'text/plain')
                ->setBody(
                    $this->twig->render(
                        'emails/booking-refund.html',
                        [
                            'tour' => $tour,
                            'booking' => $booking,
                            'refund_txt' => $this->translator->trans('refund_txt', [], 'messages', $booking->getClient()->getLocale()->getName()),
                            'client' => $booking->getClient(),
                            'status' => 'pending',
                            'company' => $company,
                            'receipt_url' => $receipt_url
                        ]
                    ),
                'text/html');
            $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://tarugabenagiltours.pt');
            $message->setReadReceiptTo($company->getEmail());
            $message->setPriority(1);

            //$attachment ? $message->attach($attachment) : false;

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
                $message->getHeaders()->addTextHeader('List-Unsubscribe', 'https://tarugabenagiltours.pt');
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
