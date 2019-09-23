<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class CronController extends AbstractController
{

    public function listCron(Filesystem $filesystem) {

        //$filesystem = new Filesystem();

        $r = [];

        if($filesystem->exists('/cron_logs')){

            $finder = new Finder();
            $finder->files()->in('../cron_logs/');
            foreach ($finder as $file)
                $r [] = [ 'title' => ucfirst(str_replace(".txt", "", $file->getFilename())), 'text' => $file->getContents()];
        }
        
        return $this->render('admin/list-cron.html', [
            'logs' => $r,
        ]);
    }
}

?>