<?php
namespace App\Service;

class Paginator
{
	public function paginate($query, $page = 1, $recordsPerPage = 10)
	{
		$query = $query->setFirstResult($recordsPerPage * ($page - 1))
				->setMaxResults($recordsPerPage);		
		
		 return new \Doctrine\ORM\Tools\Pagination\Paginator($query, true);
	}
}
