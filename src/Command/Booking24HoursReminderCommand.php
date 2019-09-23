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
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class Booking24HoursReminderCommand extends Command
{
    private $em;
    private $mailer;
    private $templating;
    private $translator;
    private $kernel;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer, \Twig_Environment $templating, TranslatorInterface $translator, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->kernel = $kernel;

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
        //$output->writeln([
        //    'Execute Client Booking 24h Reminder',
        //    '====================================',
        //    '',
        //]);
        
        $now = new \DateTime();
        $tomorrow = new \DateTime('tomorrow', new \DateTimeZone('Europe/Lisbon'));
        $bookings = $this->em->getRepository(Booking::class)->getBookings24HoursReminder($tomorrow->format('Y-m-d'));
        $company = $this->em->getRepository(Company::class)->find(1);
        //CHECK IF THE EVENT IS FOR TOMORROW IF SO SEND EMAIL TO CLIENT

        $id = '';

        $filesystem = new Filesystem();
        
        foreach ($bookings as $booking) {
        
            $this->sendEmail($booking, $company);
            $id.= $booking->getId().', ';
            
        }

        $txt = $now->format('Y-m-d H:i:s').' - Next day booking reminder '.$tomorrow->format('d/m/Y').', '.count($bookings).'xBookings['.$id.']';
        
        $filesystem->appendToFile($this->kernel->getProjectDir().'/cron_logs/reminder.txt', $txt.PHP_EOL);
        $filesystem->touch($this->kernel->getProjectDir().'/cron_logs/reminder.txt', time());

        // outputs a message followed by a "\n"
        //$output->writeln(count($bookings).' Bookings reminder send to bookings ('.$id.'), to warn them that tomorrow they have a booking: '.$tomorrow->format('d/m/Y'));
    }

    public function sendEmail(Booking $booking, Company $company){

        $https['ssl']['verify_peer'] = FALSE;
        $https['ssl']['verify_peer_name'] = FALSE;

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass())
            ->setStreamOptions($https);            
                
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