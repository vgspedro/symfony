<?php
namespace App\Service;

class Helper
{
	public function imageToB64($path)
	{
 		$type = pathinfo($path, PATHINFO_EXTENSION);	
 		$data = file_get_contents($path);
		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}

}