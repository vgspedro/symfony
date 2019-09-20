<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Booking;
use App\Service\Stripe;
use Doctrine\ORM\EntityManagerInterface;

class PaymentTimeoutCommand extends Command
{

    private $em;
    private $stripe;

    public function __construct(EntityManagerInterface $em, Stripe $stripe)
    {
        $this->em = $em;
        $this->stripe = $stripe;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:cancel-payment')
        ->setDescription('Execute to check if payment interval (20 minutes) has ended.')
        ->setHelp("Change the Stock back");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Execute Cron: Cancel-payment by Minute',
            '======================================',
            '',
        ]);

        $now = new \DateTime('now');
       
        //INTERVAL IS SET TO 900 SECONDS (15 MINUTES THE TIME THE USER HAS TO PAY AFTER MAKING THE BOOKING)

        $interval = 900;

        $startDateTime = \DateTime::createFromFormat('U', ($now->format('U') - $interval));

        $this->stripe->cancelPaymentIntent($oPayment, $paymentIntentId);

        $bookings = $this->em->getRepository(Booking::class)->getBookingPaymentsEntityProcessing($startDateTime);

        //BOOKINGS IN CONDITION. SO CLIENTS MAKE THE BOOKING BUT NOT PAYED IN MINUTES
        //NOW WE HAVE TO CANCEL THE BOOKINGS AND SET THE STOCK BACK TO THE EVENT
        //WRITE A LOG IN EACH LOG TABLE
        //CHANGE THE STATUS OF EACH PAYMENT TO 'canceled'
        /*
        if ($bookings)
        {
            foreach($bookings as $booking){
                $booking->setPaymentStatus(Booking::STATUS_CANCELED);
                $stock = $booking->getAvailable->getStock();
                $booking->getCountPax();
                $booking->getAvailable->setAvailability($stock+ $booking->getCountPax());
            }

            $em->persist($booking);
            $em->flush();
        }
        */
        // outputs a message followed by a "\n"
        $output->writeln('Total bookings in PROCESSING status until '.$startDateTime->format('d/m/Y H:i').'= '.count($bookings)); 
        // retrieve the argument value using getArgument()
        //$output->writeln('Username: '.$input->getArgument('username'));
    }

}