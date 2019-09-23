<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\AboutUsType;
use Symfony\Component\Finder\Finder;

class CronController extends AbstractController
{

    public function listCron()
    {
        $finder = new Finder();

        $finder->files()->in('../cron_logs/');
        
        $r = [];

        foreach ($finder as $file)
            $r [] = [ 'title' => ucfirst(str_replace(".txt", "", $file->getFilename())), 'text' => $file->getContents()];
        
        return $this->render('admin/list-cron.html', [
            'logs' => $r,
        ]);
    }
}

?>