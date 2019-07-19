<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Booking;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class Booking24HoursReminderCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:booking24h-reminder')
        ->setDescription('Send email to client that have booking for tomorrow.')
        ->setHelp("Client booking 24h remender");
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
        //CHECKIF THE EVENT IS FOR TOMORROW IF SO SEND EMAIL TO CLIENT

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());            
                
        $mailer = new \Swift_Mailer($transport);
        $subject = 'Reminder';//$translator->trans('send_feedback');

        $message = (new \Swift_Message($subject))
                ->setFrom([$company->getEmail() => $company->getName()])
                ->setTo([$company->getEmail() => $company->getName()])
                ->addPart($subject, 'text/plain')
                ->setBody(

                $this->renderView(
                    'emails/booking-reminder-'.$booking->getClient()->getLocale()->getName().'.html.twig',
                    array(
                        'id' => $booking->getId(),
                        'name' => $booking->getClient()->getName(),
                        'email' => $booking->getClient()->getEmail(),
                        'rate' =>  '',
                            //'logo' => 'https://'.$request->getHost().'/upload/gallery/'.$company->getLogo(),
                            //'observations' => $feedback->getObservations()
                    )
                ),
                'text/html'
            );
                
        $send = $mailer->send($message);

        $booking_id =''; 

        if ($bookings){
            foreach ($bookings as $booking){
                //$booking->getClient()->setCardNr('');

                //$this->entityManager->persist($booking);
                
                //$this->entityManager->flush();
            $booking_id.= '-->'.$booking->getId();
            }
            
        }
        // outputs a message followed by a "\n"
        $output->writeln(count($bookings). 'Bookings reminder send to Clients, to warn them that tomorrow they have a booking: '.$tomorrow->format('d/m/Y').' ('.$booking_id.')');
    }

}