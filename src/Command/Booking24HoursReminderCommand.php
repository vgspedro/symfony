<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\Booking;
use App\Entity\Company;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class Booking24HoursReminderCommand extends Command
{
    private $entityManager;
    private $mailer;
    private $templating;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer, \Twig_Environment $templating, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:booking24h-reminder')
        ->setDescription('Send email to client that have bookings for tomorrow.')
        ->setHelp("Client booking 24h reminder");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Execute Client Booking 24h Reminder',
            '====================================',
            '',
        ]);

        $tomorrow = new \DateTime('tomorrow');
        $bookings = $this->entityManager->getRepository(Booking::class)->getBookings24HoursReminder($tomorrow->format('Y-m-d'));
        $company = $this->entityManager->getRepository(Company::class)->find(1);
        //CHECK IF THE EVENT IS FOR TOMORROW IF SO SEND EMAIL TO CLIENT

        $id = '';

        foreach ($bookings as $booking) {
        
            $this->sendEmail($booking, $company);
            $id.= $booking->getId().', ';
        }
        // outputs a message followed by a "\n"
        $output->writeln(count($bookings).' Bookings reminder send to bookings ('.$id.'), to warn them that tomorrow they have a booking: '.$tomorrow->format('d/m/Y'));
    }

    protected function sendEmail(Booking $booking, Company $company){

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());            
                
        $mailer = new \Swift_Mailer($transport);
        
        if ($booking->getClient()->getLocale()->getName() == 'pt_PT'){
            
            $subject =  'Olá, não se esqueça que ...';
            $tour = $booking->getAvailable()->getCategory()->getNamePt();
        }
        else{
         
            $subject =  'Hello, don´t forget that ...';
            $tour = $booking->getAvailable()->getCategory()->getNameEn();
        }

        $message = (new \Swift_Message($subject))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo([$booking->getClient()->getEmail() => $booking->getClient()->getUsername()])
                ->addPart($subject, 'text/plain')
                ->setBody(

                $this->templating->render(
                    'emails/booking-reminder-'.$booking->getClient()->getLocale()->getName().'.html.twig',
                    array(
                        'id' => $booking->getId(),
                        'name' => $booking->getClient()->getUsername(),
                        'time' => $booking->getTimeEvent()->format('H:i'),
                        'date' => $booking->getDateEvent()->format('d/m/Y'),
                        'tour' => $tour,
                        'logo' => $company->getLinkMyDomain().'/upload/gallery/'.$company->getLogo(),
                        'company_name' => $company->getName()
                    )
                ),
                'text/html'
            );
                
        $send = $mailer->send($message);
    }

}