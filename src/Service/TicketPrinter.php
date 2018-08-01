<?php
namespace App\Service;

use Symfony\Component\Templating\EngineInterface;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Money\Money;

class TicketPrinter
{
	private $templating;
	private $moneyFormatter;
	
	public function __construct(EngineInterface $templating, MoneyFormatter $moneyFormatter)
	{
		$this->templating = $templating;
		$this->moneyFormatter = $moneyFormatter;
	}
	
	public function print($booking, $operatorAgent, $type = 1, $dest = 'I')
	{	
		try {
			$s = array();
			
			$agent = $operatorAgent[0];
	
			$event = $booking->getEvent();
			$pickup = $booking->getPickup();
			$tickets = $booking->getTickets();
						
			$product = $event->getProduct();
			$operator = $product->getOperator();
			
			$pickupPriceByPax = Money::EUR(0);
			
			$pickupComissionResult = Money::EUR(0);
			
			if (!empty($pickup)) {			
				$result = $pickup->getAmount()->allocateTo(count($tickets));
				
				$amountPickup = $pickup->getAmount();
			
				$pickupPriceByPax = reset($result);
				
				$pickupComissionResult = $pickupPriceByPax->multiply($booking->getPickupComission());

				$pickup = $pickup->getCircuit().', '.$pickup->getPlace().'@'.$pickup->getTime()->format('H:i');
			}

			foreach ($tickets as $ticket) {
				
				//setting values to 0 in each ticket
				
				$extraComissionResult = Money::EUR(0);
				$ticketComissionResult = Money::EUR(0);
				
				$amount = $ticket->getPrice();

				$amountTicket = $ticket->getPrice();
				
				$ticketComissionResult = $amountTicket->multiply($booking->getProductComission());

				$amount = $amount->add($pickupPriceByPax);
				
				$extras = array();
				foreach ($ticket->getExtras() as $extra) {
					$extras[] = $extra->getName();
					$amount = $amount->add($extra->getPrice());
					$amountExtras = $extra->getPrice();
					$extraComissionResult = $amountExtras->multiply($booking->getExtrasComission());
				}

				//sum total commision only if charge_total = false 
				if ($agent->getChargeTotal() == false) {
					
					$ticketComissionResult = $ticketComissionResult->add($pickupComissionResult);
					$ticketComissionResult = $ticketComissionResult->add($extraComissionResult);
				}
				else
					$ticketComissionResult = Money::EUR(0);

				//operator no logo set no img logo
				$operator->getImage() && $operator->getImage() != null ? $logo = 'uploads/users/'.$operator->getImage()->getFileName() : $logo = 'img/noimg.png';

				$s[] = array(
						'operator_name' => $operator->getDesignation(),						
						'operator_logo' => $logo,
						'product_id' => $product->getId(),
						'client_name' => $booking->getClientName(),
						'ticket_sku' => $ticket->getSku(),
						'ticket_n' => $ticket->getId(),
						'client_tel' => $booking->getClientPhone(),
						'ticket_type' => $ticket->getPriceDesignation(),
						'ticket_price' => $this->moneyFormatter->format($amount),
						'ticket_event_place' => $event->getProduct()->getLocality(),
						'ticket_event_date' => $event->getStartAtDatetime()->format('d/m/Y H:i'),
						'ticket_pickup' => $pickup,
						'ticket_extras' => $extras,
						'ticket_status' => $ticket->getStatus(),
						'charge_total' => $agent->getChargeTotal(),
						'total_comission' => $this->moneyFormatter->format($ticketComissionResult)
				);
			


			}
						
			// 1 IS FOR PRINTERS ONE TICKET TEMPLATE
			// ELSE IS FOR PRINTERS A4 MAX 9 TICKETS PER PAGE TEMPLATE
			
			$type == 1 ? $content = $this->templating->render('agent/pos/one_ticket.pdf.php', array('ticket' => $s)) : $content = $this->templating->render('agent/pos/a4_ticket.pdf.php', array('ticket' => $s));
			$html2pdf = new Html2Pdf('P', 'A4', 'pt', true, 'UTF-8', 0);
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content);
			$pdfContent = $html2pdf->output('one_ticket.pdf', $dest);
		} catch (Html2PdfException $e) {
			$html2pdf->clean();
			$formatter = new ExceptionFormatter($e);
			throw new \Exception($formatter->getHtmlMessage());
		}
		
		return $pdfContent;
	}	
}
