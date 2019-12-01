<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Translation\TranslatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\PessimisticLockException;
//LockMode::PESSIMISTIC_WRITE
use App\Entity\Company;
use App\Entity\Warning;
use App\Entity\Gallery;
use App\Entity\User;
use App\Entity\Faqs;
use App\Entity\Locales;
use App\Entity\Feedback;
use App\Entity\TermsConditions;
use App\Service\MoneyFormatter;
use App\Service\RequestInfo;
use App\Service\Menu;


use Money\Money;

class UsefullInfoNewController extends AbstractController
{

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }
    

    public function index(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo, Menu $menu, TranslatorInterface $translator)
    {   
        $id = !$request->query->get('id') ? 'home': $request->query->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);
        
        $local = $request->getLocale();
        $user_locale = $em->getRepository(Locales::class)->findOneBy(['name' => $request->getLocale()]); 
        !$this->session->get('_locale') ? $this->session->set('_locale', $local) : false;

        $locales = $em->getRepository(Locales::class)->findAll();

        $faqs = $em->getRepository(Faqs::class)->findOneBy(['locales' => $user_locale]);
        
        $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $user_locale]);

        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'ASC']);

        return $this->render('usefull_info/base.html', 
            [
                'colors'=> '',//$this->color(),
                'warning' => $warning,
                'locale' => null,
                'faqs' => $faqs,
                'galleries' => $gallery,
                'locales' => $locales,
                'id' => '#'.$id,
                'terms' => $terms,
                'company' => $company,
                'host' => $reqInfo->getHost($request),
                'page' => 'index_new_info',
                'menu' => $menu->site('index_new_info', $translator)
            ]
            );
    }

    public function userTranslation($lang, $page)
    {    
        $this->session->set('_locale', $lang);
        return $this->redirectToRoute($page);
    }

}
?>