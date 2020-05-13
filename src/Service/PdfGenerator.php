<?php
namespace App\Service;

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

use Twig\Environment;

use App\Entity\Payment;
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
	

	public function receipt(Company $company, Payment $payment, $dest = 'F')
	{	
		$logo = $this->helper->imageToB64('images/invoice_logo_grey.jpg');
		
		try {	
			$content = $this->twig->render('pdf/payment.html', ['payment' => $payment, 'company' => $company, 'logo' => $logo]);
		
			$html2pdf = new Html2Pdf();
			//$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content);

			$pdf = $html2pdf->output($_SERVER['DOCUMENT_ROOT'].'/payments/p_'.$payment->getId().'.pdf', $dest);
			//$pdfContent = $html2pdf->output('p_'.$payment->getId().'.pdf', 'S');
			} catch (Html2PdfException $e) {
				$html2pdf->clean();
				$formatter = new ExceptionFormatter($e);
				echo new \Exception($formatter->getHtmlMessage());
			}

		return $pdf;
	}


	public function attach(Company $company, Payment $payment, $dest = 'S')
	{

		$logo = $this->helper->imageToB64('images/invoice_logo_grey.jpg');
		
		try {	
			$content = $this->twig->render('pdf/payment.html', ['payment' => $payment, 'company' => $company, 'logo' => $logo]);
		
			$html2pdf = new Html2Pdf();
			//$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content);

			$pdf = $html2pdf->output('p_'.$payment->getId().'.pdf', $dest);
			} catch (Html2PdfException $e) {
				$html2pdf->clean();
				$formatter = new ExceptionFormatter($e);
				echo new \Exception($formatter->getHtmlMessage());
			}

		return $pdf;
	
	}


}

