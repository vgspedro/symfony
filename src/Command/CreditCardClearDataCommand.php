<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Booking;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CreditCardClearDataCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:clear-card-info')
        ->setDescription('Execute to clear credit card data from user after 15 days of the event taken place.')
        ->setHelp("Clear the Ccard field");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $hour = 3600;
        $day = 86400;
        $totalDays = 15;

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        //$output->writeln([
        //    'Execute Clear Query (c.card field)',
        //    '==================================',
        //    '',
        //]);

        $now = new \DateTime('now');

        $interval = 86400 * $totalDays;

        $deadline = \DateTime::createFromFormat('U', ($now->format('U') - $interval));

        $filesystem = new Filesystem();
        
        $bookings = $this->entityManager->getRepository(Booking::class)->getClientCreditCardData($deadline->format('Y-m-d'));

        //CHECK IF THE EVENT HAS PASS 15 DAYS THAN TODAY
        //ON BOOKINGS WITH WARRANTY PAYMENT WE MUST DELETE THE CREDIT CARD NR
        $id ='';

        if ($bookings){
            foreach ($bookings as $booking){
                
                $id.= $booking->getId().', ';

                $booking->getClient()->setCardNr('');
                
                $booking->getClient()->setCvv('Deleted on: '.$deadline->format('d/m/Y H:i:s'));
                
                $this->entityManager->persist($booking);
                
                $this->entityManager->flush();
            }
        }

        $txt = $now->format('Y-m-d H:i:s').' -> Delete clients data where the event is older than '.$deadline->format('d/m/Y H:i').' = '.count($bookings).' #ID['.$id.']';
        $filesystem->appendToFile('cron_logs/cleardata.txt', $txt.PHP_EOL);
        $filesystem->touch('cron_logs/cleardata.txt', time());


    }

}