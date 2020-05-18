<?php
namespace App\Service;

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

use Twig\Environment;

use App\Entity\Booking;
use App\Entity\TermsConditions;
use App\Entity\Company;

use App\Service\Helper;

class PdfGenerator
{
    private $twig;
    private $helper;
    
    public function __construct( Environment $twig,  Helper $helper)
    {
        $this->twig = $twig;
        $this->helper = $helper;
    }
	
	/**
	*
	*
	**/
	public function voucher(Company $company, Booking $booking, TermsConditions $terms, $dest)  //$dest = 'S')
	{
		$logo = $this->helper->imageToB64('upload/gallery/'.$company->getLogo());
		
		try {	
			$content = $this->twig->render('pdf/booking_voucher.html', [
				'booking' => $booking,
				'company' => $company,
				'terms' => $terms,
				'logo' => $logo
			]);
		
			$html2pdf = new Html2Pdf();
			$html2pdf->writeHTML($content);
			$pdf = $html2pdf->output($booking->getId().'.pdf', $dest);
			
			return [
				'status' => 1,
				'pdf' => $pdf
			];
		}
		catch (Html2PdfException $e) {
			$html2pdf->clean();
			$formatter = new ExceptionFormatter($e);
			//echo new \Exception($formatter->getHtmlMessage());
			return [
				'status' => 0,
				'pdf' => $formatter->getHtmlMessage(), 
			];
		}
	
	}



/*
	public function voucher(Company $company, Booking $booking, TermsConditions $terms, $dest) //$dest = 'F' 'S')
	{	
		$logo = $this->helper->imageToB64('upload/gallery/'.$company->getLogo());
		
		try {	
			$content = $this->twig->render('pdf/booking_voucher.html', ['booking' => $booking, 'company' => $company, 'terms' => $terms, 'logo' => $logo]);
		
			$html2pdf = new Html2Pdf();
			//$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content);
			//Save in folder
			//$pdf = $html2pdf->output($_SERVER['DOCUMENT_ROOT'].'/payments/p_'.$payment->getId().'.pdf', $dest);
			$pdfContent = $html2pdf->output($booking->getId().'.pdf', $dest);
			} catch (Html2PdfException $e) {
				$html2pdf->clean();
				$formatter = new ExceptionFormatter($e);
				echo new \Exception($formatter->getHtmlMessage());
			}

		return $pdf;
	}
*/

}



