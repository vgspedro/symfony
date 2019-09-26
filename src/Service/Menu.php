<?php
namespace App\Service;

class Menu
{

    public function administration() {

        $e[] = ['path' => '#', 'name'=> 'Painel Controlo', 'icon' => 'fas fa-tachometer-alt', 'target' => '', 'id' => 'dashboard' ];

        $e[] = ['path' => '#', 'name'=> 'Disponibilidades', 'icon' => 'far fa-calendar-check', 'target' => '', 'id' => 'available' ];

        $e[] = ['path' => '#', 'name'=> 'Reservas', 'icon' => 'far fa-calendar-alt fa-fw', 'target' => '', 'id' => 'booking' ];
		
        $e[] = ['path' => '#', 'name'=> 'Texto Fácil', 'icon' => 'fas fa-signal', 'target' => '', 'id' => 'easy-text' ];

		$e[] = ['path' => '#', 'name'=> 'Aviso', 'icon' => 'fa fa-exclamation-triangle', 'target' => '', 'id' => 'warning' ];
        
        $e[] = ['path' => 'index', 'name'=> 'Ver Site', 'icon' => 'fas fa-external-link-alt fa-fw', 'target' => '_blank', 'id' =>''];
		
		$e[] = ['path' => 'logout', 'name'=> 'Sair', 'icon' => 'fas fa-sign-out-alt fa-fw', 'target' => '_self', 'id' => ''];
        
        return $e;
    }

    public function site() {
        $err = [];
        return $err;
    }

}
