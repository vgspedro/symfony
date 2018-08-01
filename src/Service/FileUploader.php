<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
	private $targetDir;
	
	public function __construct($targetDir)
	{
		$this->targetDir = $targetDir;
	}
	
	public function upload(UploadedFile $file)
	{
		// generate a unique name for the file before saving it
		$fileName = md5(uniqid()).'.'.$file->guessExtension();
		
		$file->move($this->targetDir, $fileName);
		
		return $fileName;
	}
	
	public function removeUpload($filename)
	{
		unlink($this->targetDir.'/'.$filename);
	}
	
	public function getTargetDir()
	{
		return $this->targetDir;
	}	
}
