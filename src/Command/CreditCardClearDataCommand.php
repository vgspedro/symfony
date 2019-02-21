<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Booking;
use App\Entity\Client;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CreditCardClearDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
    
        $date = new \DateTime('now');
        $this->setName('app:clear-card-info')
        ->setDescription('Execute to clear credit card data from user after 15 days of the event taken place.')
        ->setHelp("Clear the Client fields");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $hour = 3600;
        $day = 86400;
        $totalDays = 1;

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Execute query',
            '============',
            '',
        ]);

        $now = new \DateTime('now');

        $interval = 86400 * $totalDays;

        $deadline = \DateTime::createFromFormat('U', ($now->format('U') - $interval));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();
        $bookings = $em->getRepository(Booking::class)->getClientCreditCardData($deadline->format('Y-m-d'));

        //CHECK IF THE EVENT HAS PASS 15 DAYS THAN TODAY
        //ON BOOKINGS WITH WARRANTY PAYMENT WE MUST DELETE THE CREDIT CARD NR

        if ($bookings){
            foreach ($bookings as $booking){
                $booking->getClient()->setCardNr('');

                $em->persist($booking);

                $em->flush();
            }
        }
        // outputs a message followed by a "\n"
        $dates =''; 
        foreach ($bookings as $booking)
          $dates .= '-->'.$booking->getClient()->getId();

        $output->writeln('Total credit cards found from '.$deadline->format('d/m/Y H:i').'='.$dates);
    }

}