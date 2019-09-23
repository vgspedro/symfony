<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Booking;
use App\Entity\Company;
use App\Service\Stripe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

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
        ->setDescription('Execute to check if payment interval (5 minutes cron) has ended.')
        ->setHelp("Change the Stock back");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Execute Cron: Cancel-payments Set Stock Back To Availability',
            '======================================',
            '',
        ]);

        $now = new \DateTime();
       
        //INTERVAL IS SET TO 900 SECONDS (15 MINUTES THE TIME THE USER HAS TO PAY AFTER MAKING THE BOOKING)

        $interval = 900;

        $startDateTime = \DateTime::createFromFormat('U', ($now->format('U') - $interval));

        $bookings = $this->em->getRepository(Booking::class)->getBookingPaymentsEntityProcessing($startDateTime);

        //BOOKINGS IN CONDITION. SO CLIENTS MAKE THE BOOKING BUT NOT PAYED IN MINUTES
        //NOW WE HAVE TO CANCEL THE BOOKINGS AND SET THE STOCK BACK TO THE EVENT
        //WRITE A LOG IN EACH LOG TABLE
        //CHANGE THE STATUS OF EACH PAYMENT TO 'canceled'
        
        $id ='';
        
        $filesystem = new Filesystem();

        if ($bookings){

            $company = $this->em->getRepository(Company::class)->find(1);
            
            foreach($bookings as $booking){

                if($booking->getStripePaymentLogs() && $u = $booking->getStripePaymentLogs()->getLogObj()){

                    //delele only the payment_intent obj from Stripe, avoid user buy it after timeout.
                    $paymentIntentId = $u->object == 'payment_intent' ? $u->id : false;

                    $id .= $booking->getId().'-->';

                    if($paymentIntentId){

                        $cancel = $this->stripe->cancelPaymentIntent($company, $paymentIntentId);

                        $booking->setPaymentStatus(Booking::STATUS_CANCELED);
                        $booking->setStatus(Booking::STATUS_CANCELED);
                        $stock = $booking->getAvailable()->getStock();
                        $booking->getCountPax();
                        $booking->getAvailable()->setStock($stock+$booking->getCountPax());
                    }
                }
                $this->em->persist($booking);
                $this->em->flush();
            } 
        }
        
        $txt = $now->format('Y-m-d H:i:s').' - Booking processing payment status to canceled before '.$startDateTime->format('d/m/Y H:i').', '.count($bookings).'xBookings['.$id.']';
        $filesystem->appendToFile('../cron_logs/paytimeout.txt', $txt.PHP_EOL);
        $filesystem->touch('../cron_logs/paytimeout.txt', time());

        $output->writeln($txt); 
    
    }
}