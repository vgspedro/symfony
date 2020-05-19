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
use Twig\Environment;

use App\Service\EmailSender;

class Booking24HoursReminderCommand extends Command
{
    private $em;
    private $emailer;
    //private $twig;
    //private $translator;
    private $kernel;

    public function __construct(EntityManagerInterface $em, EmailSender $emailer, /*Environment $twig, TranslatorInterface $translator, */KernelInterface $kernel)
    {
        $this->em = $em;
        $this->emailer = $emailer;
        //$this->twig = $twig;
        //$this->translator = $translator;
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

         //Debug outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Execute Client Booking 24h Reminder',
            '====================================',
            '',
        ]);
        
        $now = new \DateTime('now', new \DateTimeZone('Europe/Lisbon'));
        $tomorrow = new \DateTime('tomorrow', new \DateTimeZone('Europe/Lisbon'));

        $bookings = $this->em->getRepository(Booking::class)->getBookings24HoursReminder($tomorrow->format('Y-m-d'));
        $company = $this->em->getRepository(Company::class)->find(1);
        //CHECK IF THE EVENT IS FOR TOMORROW IF SO SEND EMAIL TO CLIENT

        $id = '';

        $filesystem = new Filesystem();
        
        foreach ($bookings as $booking) {
           //$this->sendEmail($booking, $company);
            $this->emailer->sendBookingReminder($company, $booking);
            $id.= $booking->getId().', ';
        }

        $txt = $now->format('Y-m-d H:i:s').' - Next day booking reminder '.$tomorrow->format('d/m/Y').', '.count($bookings).'xBookings['.$id.']';
        
        $filesystem->appendToFile($this->kernel->getProjectDir().'/cron_logs/reminder.txt', $txt.PHP_EOL);
        $filesystem->touch($this->kernel->getProjectDir().'/cron_logs/reminder.txt', time());
        
        // Debug outputs a message followed by a "\n"
        //$output->writeln($txt);

        return 0;

     
    }
}
