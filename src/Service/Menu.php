<?php

namespace App\Service;

class Menu
{

    public function administration() {

        //company links
        $s[] = ['path' => '#', 'name'=> 'Editar', 'icon' => 'far fa-edit fa-fw', 'target' => '', 'id' => 'company', 'color' => 'w3-blue', 'submenu' => [] ];
        //$s[] = ['path' => '#', 'name'=> 'Texto Reservas', 'icon' => 'far fa-file-alt', 'target' => '', 'id' => 'about-us', 'color' => 'w3-blue', 'submenu' => [] ];
        $s[] = ['path' => '#', 'name'=> 'RGPD', 'icon' => 'far fa-file-alt', 'target' => '', 'id' => 'rgpd', 'color' => 'w3-blue', 'submenu' => [] ];
        $s[] = ['path' => '#', 'name'=> 'Termos & Condições', 'icon' => 'fas fa-asterisk', 'target' => '', 'id' => 'terms', 'color' => 'w3-blue', 'submenu' => [] ];
        $s[] = ['path' => '#', 'name'=> 'Logs Rotinas', 'icon' => 'fas fa-cogs', 'target' => '', 'id' => 'list-cron', 'color' => 'w3-blue', 'submenu' => [] ];
        $s[] = ['path' => '#', 'name'=> 'Faqs', 'icon' => 'fas fa-question-circle', 'target' => '', 'id' => 'faqs', 'color' => 'w3-blue', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'A Empresa', 'icon' => 'fa fa-home w3-text-blue', 'target' => '', 'id' => 'company', 'submenu' => $s ];


        //categories links
        $sc[] = ['path' => '#', 'name'=> 'Editar/Listar', 'icon' => 'far fa-edit fa-fw', 'target' => '', 'id' => 'category-list', 'color' => 'w3-purple', 'submenu' => [] ];
        $sc[] = ['path' => '#', 'name'=> 'Nova', 'icon' => 'far fa-plus-square fa-fw', 'target' => '', 'id' => 'category-new', 'color' => 'w3-purple', 'submenu' => [] ];
        $sc[] = ['path' => '#', 'name'=> 'Feedback', 'icon' => 'fa-fw far fa-comment-dots', 'target' => '', 'id' => 'feedback-list', 'color' => 'w3-purple', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Categorias', 'icon' => 'fa fa-tags fa-fw w3-text-purple', 'target' => '', 'id' => 'category', 'submenu' => $sc ];

        //extra payments links
        $sp[] = ['path' => '#', 'name'=> 'Listar', 'icon' => 'far fa-list-alt fa-fw', 'target' => '', 'id' => 'list-extra-payment', 'color' => 'w3-amber', 'submenu' => [] ];
        $sp[] = ['path' => 'create_extra_payment', 'name'=> 'Novo', 'icon' => 'far fa-plus-square fa-fw', 'target' => '', 'id' => '', 'color' => 'w3-amber', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Pagamento Extra', 'icon' => 'far fa-money-bill-alt fa-fw w3-text-amber', 'target' => '', 'id' => 'payments', 'submenu' => $sp ];

        //galleries links
        $sg[] = ['path' => '#', 'name'=> 'Editar/Listar', 'icon' => 'far fa-edit fa-fw', 'target' => '', 'id' => 'gallery-list', 'color' => 'w3-green', 'submenu' => [] ];
        $sg[] = ['path' => '#', 'name'=> 'Nova', 'icon' => 'far fa-plus-square fa-fw', 'target' => '', 'id' => 'gallery-new', 'color' => 'w3-green', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Galeria', 'icon' => 'far fa-images fa-fw w3-text-green', 'target' => '', 'id' => 'gallery', 'submenu' => $sg ];
        
        //staff links
        $st[] = ['path' => '#', 'name'=> 'Editar/Listar', 'icon' => 'far fa-edit fa-fw', 'target' => '', 'id' => 'staff-list', 'color' => 'w3-indigo', 'submenu' => [] ];
        $st[] = ['path' => '#', 'name'=> 'Novo', 'icon' => 'far fa-plus-square fa-fw', 'target' => '', 'id' => 'staff-new', 'color' => 'w3-indigo', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Staff', 'icon' => 'fas fa-user-friends fa-fw w3-text-indigo', 'target' => '', 'id' => 'staff', 'submenu' => $st ];
        
        // users links
        $su[] = ['path' => '#', 'name'=> 'Editar/Listar', 'icon' => 'far fa-edit fa-fw', 'target' => '', 'id' => 'user-list', 'color' => 'w3-pink', 'submenu' => [] ];
        $su[] = ['path' => '#', 'name'=> 'Novo', 'icon' => 'far fa-plus-square fa-fw', 'target' => '', 'id' => 'user-new', 'color' => 'w3-pink', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Utilizadores', 'icon' => 'fas fa-user-shield fa-fw w3-text-pink', 'target' => '', 'id' => 'user', 'submenu' => $su ];
        
        //unique links
        $e[] = ['path' => '#', 'name'=> 'Painel Controlo', 'icon' => 'fas fa-tachometer-alt', 'target' => '', 'id' => 'dashboard', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Disponibilidades', 'icon' => 'far fa-calendar-check', 'target' => '', 'id' => 'available', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Reservas', 'icon' => 'far fa-calendar-alt fa-fw', 'target' => '', 'id' => 'booking', 'submenu' => [] ];
        $e[] = ['path' => '#', 'name'=> 'Texto Fácil', 'icon' => 'fas fa-signal', 'target' => '', 'id' => 'easy-text', 'submenu' => [] ];
		$e[] = ['path' => '#', 'name'=> 'Aviso', 'icon' => 'fa fa-exclamation-triangle', 'target' => '', 'id' => 'warning', 'submenu' => [] ];
        $e[] = ['path' => 'index_new', 'name'=> 'Ver Site', 'icon' => 'fas fa-external-link-alt fa-fw', 'target' => '_blank', 'id' =>'', 'submenu' => [] ];
		$e[] = ['path' => 'logout', 'name'=> 'Sair', 'icon' => 'fas fa-sign-out-alt fa-fw', 'target' => '_self', 'id' => '', 'submenu' => [] ];
        
        return $e;
    }

    public function site($route, $translator){

        $s=[];

        $m[] = ['path' => 'index_new', 'name'=> $translator->trans('info.home'), 'target' => '_self', 'class' => '', 'route' => $route, 'submenu' => [] ];
        $m[] = ['path' => 'index_new_info', 'name'=> $translator->trans('info.usefull_info'), 'target' => '_self', 'class' => '', 'submenu' => [] ];
        $m[] = ['path' => 'index_new_help_us_improve', 'name'=> $translator->trans('link.help_improve'), 'target' => '_self', 'class' =>'', 'submenu' => [] ];

        foreach ($m as $k){

            $class = $k['path'] == $route ? ' active' : '';

            $s[] = [
                'path' => $k['path'], 'name'=> $k['name'], 'target' => $k['target'], 'class' => $class, 'submenu' => $k['submenu']
            ];
        }
        
        return $s;
    }

}
