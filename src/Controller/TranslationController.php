<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Locales;


class TranslationController extends AbstractController
{

    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function translate(Request $request) {

        $em = $this->getDoctrine()->getManager();
        
        $userLocale = $request->request->get('lang') ? $request->request->get('lang') : 'pt_PT';

        $userLocales = $em->getRepository(Locales::class)->findOneBy(['name' => $userLocale]);
        
        if(!$userLocales)
            $userLocales = $em->getRepository(Locales::class)->findOneBy(['name' => 'pt_PT']);

        $this->session->set('_locale', $userLocales);

        $userLocales->getName() == 'en_EN' ? 
            $response = array('lang' => $userLocales->getName(), 'data' => $this->translationEn()) : 
            $response = array('lang' => $userLocales->getName(), 'data' => $this->translationPt());
        return new JsonResponse($response);
    }


    private function translationPt(){
        return 
            array(
        'error' => 'Erro',
        'success' => 'Sucesso',
        'close' =>'Fechar',
        'sorry'=>'Lamentamos',
        'required'=>'Obrigatório *',
        'wifi_error' =>'Por favor verifique a ligação Wi-fi/Internet, e tente novamente!',
        'order' => 'Reserva Nº',
        'thank_you' => 'Confirmaremos a sua reserva dentro de 12 Horas. Obrigado',
        'session' => 'Sessão expirou!!',
        'check' => 'Verifique',
        'session_info' => 'Por favor, atualize a página!',

    'link' => array(
        'where_we_are'=>'Onde Estamos',
        'the_team'=>'A Equipa',
        'price'=>'Passeios',
        'kayak'=>'Aluguer Kayak',
        'schedule'=>'Horário',
        'contact'=>'Contatos',
        'booking'=>'Reservas'),

    'part_one' => array(
        'intro' =>'A Taruga Benagil Tours encontra-se no Algarve, mais concretamente na Praia de Benagil, venha visitar-nos!',
        'local' =>'Localização GPS:',
        'gps' => 'LAT: 37º5\'16"N LONG: 8º24\'35"W </b>',
        'ticket_shop' => 'A Nossa Ticket Shop',
        'find_us'=>'Encontre-nos em ...'
    ),

    'part_two'=> array(
        'important_info'=>'Dúvidas e Informações Importantes',
        'booking'=>'Reservas',
        'booking_txt'=>'O passeio inclui paragem na gruta de Benagil?<br><br> R: A paragem na gruta não é permitida, no entanto, na nossa visita de 1H15Min., se as condições do mar forem favoráveis, há uma paragem de 10/15 minutos na gruta para nadar.<br>Quem não quiser mergulhar, pode ficar no barco com o capitão.<br><br>Todas as reservas feitas por telefone ou por email têm que ser confirmadas 30 min. antes na nossa ticket shop Taruga Tours na Praia de Benagil. O desrespeito dessas condições anulam a reserva.',
        'light'=>'Luminosidade',
        'light_txt'=>'Existe mais intensidade de luz dentro das grutas entre as 10:00 e as 16:00, de qualquer das formas, o passeio às grutas é bonito em qualquer altura do dia.',
        'parking'=> 'Estacionamento',
        'parking_txt'=>'Existe parque gratuito nas proximidades da praia.',
        'what_to_take'=>'A levar para a visita',
        'what_to_take_txt'=>'Roupa confortavel e protetor solar para os dias mais quentes.',
        'ties'=>'Marés',
        'ties_txt'=>'A altura das marés não é muito importante para nós, pois os nossos barcos são pequenos e visitamos cerca de 20 grutas no passeio de 1 hora.',
        'attention' => 'Atenção!',
        'attention_txt' => 'Por se tratar de um passeio marítimo, não nos responsabilizamos por quaisquer danos causados a equipamentos como telemóveis, máquinas fotográficas, etc.',
        'no_bookings_txt' =>'Estimados clientes, não aceitamos reservas nos dias marcados a vermelho devido a termos esgotado todas as reservas ou devido às condições do mar.',
        'check' =>'Verifique'
    ),


    'part_tree'=> array(
        'duration'=>'Duração Passeio',
        'adult'=>'Adulto',
        'children'=>'Criança',
    ),

    'part_four'=> array(
        'kayak_txt'=>'Para os mais aventureiros temos a opção do aluguer do Kayak.',
        'kayak_txt_i_info'=>'Informação importante',
        'kayak_txt_conditions'=>'É um aluguer livre que obriga o preenchimento de um termo de responsabilidade onde a pessoa que aluga tem de possuir o seu <b>documento de identificação.</b><br><br>Não guardamos bens pessoais no local.<br><br>O equipamento é adquirido na entrada da praia ficando à <b>responsabilidade</b> de quem aluga a entrega do mesmo no final do aluguer.<br>(Pode solicitar ajuda de um colaborador no momento).<br><br>É aconselhável apenas a pessoas com <b>experiência em kayak</b> visto que será para usar em mar e interior das grutas.<br><br><b>Não permitido crianças</b> com menos de 5 anos.',
        'kayak_schedules'=>'Horário disponível para reserva online - 10:00.
    <br><br>Mais horários disponíveis para reserva direta na praia até às 17:00.',
        'kayak_single'=>'Kayak Single - 45 Min.',
        'kayak_double'=>'Kayak Duplo - 45 Min.',
        'paddle'=>'Prancha Paddle - 1Hora',
        'kayak_single_price'=>'15.00€',
        'kayak_double_price'=>'30.00€',
        'paddle_price'=>'20.00€'
    ),

    'part_five'=> array(
        'schedule_txt'=>'Estamos abertos o ano inteiro com o seguinte horário.',
        'schedule_summer'=>'9:30 às 18:30',
        'schedule_summer_txt'=>'(Maio a Setembro)',
        'schedule_winter'=>'10:30 às 16:30',
        'schedule_winter_txt'=>'(Outubro a Abril)'
    ),

    'part_six'=> array(
        'contact_txt'=>'Para mais informações sobre os nossos serviços ou reservas, não hesite em contatar-nos!'
    ),

    'part_seven'=> array(
        'address'=>'Morada *',
        'name'=>'Nome *',
        'adult'=>'Nº Adultos *',
        'children'=>'Nº Crianças *',
        'baby'=>'Nº Bébés *',
        'date'=>'Data *',
        'email_invalid'=>'Email Inválido',
        'hour'=>'Hora *',
        'tour'=>'Passeio *',
        'telephone'=>'Telefone *',
        'email'=>'Email *',
        'submit'=>'Enviar',
        'tour' =>'Passeio *',
        'rgpd' => 'RGPD (Regulamento Geral de Proteção de Dados)*',
        'rgpd_txt' =>'Eu concordo que a Taruga Benagil Tours entre em contato comigo, para me informar do estado da minha reserva, em conformidade com o RGPD. Eu concordo que posso cancelar a qualquer momento. Eu concordo que as informações fornecidas através deste site são armazenadas e processadas com o propósito de obter a confirmação da minha reserva. A Taruga Benagil Tours leva a proteção de dados muito a sério.<br>Email: <a class="w3-text-blue" href="mailto:protecaodedados@tarugatoursbenagilcaves.pt?subject=Protecao%20Dados">protecaodedados@tarugatoursbenagilcaves.pt</a>',
        'spam_txt' => 'Não recebeu o nosso email, por favor verifique a sua caixa de spam ou ligue para o número +351969617828',
        'booking' => 'Todas as reservas feitas por:',
        'book_email' => 'EMAIL - É válida após receber a confirmação da equipa Taruga Tours',
        'book_phone' => 'TELEFONE - Tem que ser confirmada 30 min. antes na nossa ticket shop Taruga Tours na Praia de Benagil.',
        'book_after' => 'O desrespeito dessas condições anulam a reserva. Ao clicar em submeter irá receber no seu email, a confirmação da reserva.',
        'zero' => 'Minimo é 1 Adulto, adicionou 0',
        'other_buy_it' => 'Outro cliente finalizou a compra 1º, tente outra data.',
        'get_available' => 'Obter Datas',
        'no_stock' => 'De momento, este passeio, não tem disponibilidade.',
        'sorry' => 'Lamentamos mas...',
        'cvv_invalid' => 'CVV (3 digitos)',
        'no_match_names' => 'Nome e Nome Cartão tem que ser iguais',
        'card_nr_invalid' => 'Nº Cartão Crédito Inválido',
        'telephone_invalid' => 'Telefone (Min: 9 digitos)',
        'name_invalid' => 'Name (Apenas Letras)'
    ),

    'info' => array(
        'choose_date_hour'=> 'Escolha Data & Hora',
        'choose_date_calendar'=> 'Escolha Data do Calendário *',
        'choose_hour'=> 'Escolha Hora *',
        'buy_now' => 'Comprar',
        'no_webstorage' => 'Lamentamos! Mas o seu navegador não suporta Web Storage, escolha outro navegador.',
        'credit_card_nr' => 'Nº Cartão Crédito *',
        'name_credit_card' => 'Nome Cartão Crédito *',
        'date_credit_card' => 'Data Expiração *',
        'tour_data' => 'Info. Passeio',
        'personal_data' => 'Info. Pessoal',
        'credit_card_data' => 'Info. Cartão Crédito',
        'confirm' => 'Confirmar',
        'fill_all' => 'Preencha todos os seguintes campos',
        'wp_set_no_cc_data' => 'Garantia pagamento obrigatório, mas os dados do Cartão estão em falta',
        'date_card_invalid' => 'Data Expiração Inválida',
        'doubts' => 'Dúvidas',
        'home' =>'Inicio',
        'usefull_info' =>'Informações Uteis',
        'gallery' => 'Galeria'


    ),


    'arbitrary' => array(
    'name' => 'Centro Arbitragem',
    'txt' =>'Entidades de Resolução Alternativa de Litígios de Consumo<br>
    Nos termos da Lei n.º 144/2015 de 8 de Setembro informamos que o Cliente poderá recorrer às seguintes Entidades de Resolução Alternativa de Litígios de Consumo:<br>
    Provedor do Cliente das Agências de Viagens e Turismo in www.provedorapavt.com;
    <br>
    Centro de Arbitragem de Consumo do Algarve
    <br>
    Tribunal Arbitral
    <br>
    Rede de Arbitragem de Consumo<br>
    Edificio Ninho de Empresas, Estrada da Penha 8005-131 Faro<br>
    Tel:289 823 135 Fax:289 812 213<br>
    Email: info@consumoalgarve.pt<br>
    www.consumoalgarve.pt
    ou a qualquer uma das entidades devidamente indicadas na lista disponibilizada pela Direcção Geral do Consumidor em http://www.consumidor.pt cuja consulta desde já aconselhamos.'
    ),

    'cookies' => array(
        'txt'=>'Este site utiliza cookies. Ao navegar no site estará a consentir a sua utilização.',
        'link'=>'Saiba mais sobre o uso de cookies.',
        'btn' =>'Aceitar'
    ),


        'page_price_prices'=>'Preços',
        'page_price_adult'=>'Adulto',
        'page_price_children'=>'Criança',
        'page_ticket_shop'=>'Ticket Shop',
        'page_price_duration'=>'Duração Passeio'
    );
}


private function translationEn(){
    return 
array(
    'error' => 'Error',
    'success' => 'Success',
    'close' =>'Close',
    'sorry'=>'Sorry',
    'required'=>'Required *',
    'wifi_error' =>'Please check Wi-fi/Internet connection, and try again!',
    'order' => 'Booking Nr.',
    'thank_you' => 'In 12 hours we will confirm your booking. Thank you.',
    'session' => 'Session timeout reached!!',
    'check' => 'Check',
    'session_info' => 'Please, refresh the page!',

'link' => array(
    'where_we_are'=>'Where we Are',
    'the_team'=>'The Team',
    'price'=>'Tours',
    'kayak'=>'Kayak Rental',
    'schedule'=>'Schedule',
    'contact'=>'Contacts',
    'booking'=>'Booking'),

'part_one' => array(
    'intro' =>'Taruga Benagil Tours is in the Algarve in Benagil´s Beach, come and visit us!',
    'local' =>'GPS Location:',
    'gps' => 'LAT: 37º5\'16"N LONG: 8º24\'35"W </b>',
    'find_us'=>'Find us ...',
    'ticket_shop' => 'Ticket Shop'
),

'part_two'=> array(
    'important_info'=>'Questions & Important Information',
    'booking'=>'Booking',
    'booking_txt'=>'Does the tour include a stop at the Benagil cave? <br><br> A: Landing at the cave is no longer allowed, however, on our 1H 15Min. visit, if the sea ​​conditions are favorable, there is a 10/15 Min. stop in the cave for a swim.<br> Anyone who does not want to dive, could stay on the boat with the skipper. <br><br>All bookings must be made by phone or email and confirmed 30 Min. Before the visit in our ticket shop Taruga Tours in Benagil Beach. Not following these rules will void the booking.',

    'light'=>'Luminosity',
    'light_txt'=>'There´s more light inside the caves between 10:00am to 16:00pm, but the cave visit is beautiful at any time of the day.',
    'parking'=> 'Parking',
    'parking_txt'=>'There´s free parking nearby the beach.',
    'what_to_take'=>' What to bring',
    'what_to_take_txt'=>'Comfortable clothes and sunscreen for the hotter days.',
    'ties'=>'Ties',
    'ties_txt'=>'The height of the ties isn´t important to us, since our boats are small and we visit about 20 caves in 1 Hour.',
    'attention' => 'Attention!',
    'attention_txt' => 'Since it´s a boat ride, we are not responsible for any damages caused to electronic equipments like mobile phones, cameras, etc.',
    'no_bookings_txt' =>'Dear costumers, we do not accept reservations on days marked in red because we are full of due to the conditions of the sea.',
    'check' =>'Check'
),


'part_tree'=> array(
    'duration'=>'Duration',
    'adult'=>'Adult',
    'children'=>'Children',
),

'part_four'=> array(
    'kayak_txt'=>'For the more adventurous we have the option of kayak hire.',
    'kayak_txt_i_info'=>'Important information',
    'kayak_txt_conditions'=>'It´s a rental not a tour, that requires the fulfillment of a term of responsibility where the person who rents has to possess his <b>identification document.</b>
<br><br>We do not keep personal property on the premises.
<br><br>The equipment is acquired at the entrance of the beach being the <b>responsibility</b> of those who rents the delivery of the same at the end of the rental.<br>
(You can request help from a collaborator at the moment).
<br><br>It´s advisable only to people with <b>experience in kayaking</b> as it will be to use in the sea and inside of the caves.
<br><br><b>Not allowed for kids</b> under 5 years.',
    'kayak_schedules'=>'Timetable available for online booking - 10:00. 
<br><br>More schedules available for direct booking at the beach till 17:00.',
    'kayak_single'=>'Kayak Single - 45 Min.',
    'kayak_double'=>'Kayak Double - 45 Min.',
    'paddle'=>'Paddle Board - 1Hour',
    'kayak_single_price'=>'15.00€',
    'kayak_double_price'=>'30.00€',
    'paddle_price'=>'20.00€'
),

'part_five'=> array(
    'schedule_txt'=>'We are open all year long with the following schedule:',
    'schedule_summer'=>'9:30 till 18:30',
    'schedule_summer_txt'=>'(May to September)',
    'schedule_winter'=>'10:30 till 16:30',
    'schedule_winter_txt'=>'(October to April)'
),

'part_six'=> array(
    'contact_txt'=>'For further information about our services please feel free to contact us!'
),

'part_seven'=> array(
    'address'=>'Address *',
    'name'=>'Name *',
    'adult'=>'Nr. Adults *',
    'children'=>'Nr. Childrens *',
    'baby'=>'Nr. Babies *',
    'date'=>'Date *',
    'hour'=>'Hour *',
    'email_invalid'=>'Invalid Email',
    'tour'=>'Tour *',
    'telephone'=>'Telephone *',
    'email'=>'Email *',
    'submit'=>'Send',
    'tour' =>'Tour *',
    'rgpd' => 'GDPR (General Data Protection Regulation)*',
    'rgpd_txt' =>'I agree that Taruga Benagil Tours can contact and inform me about the status of my booking, in accordance with the GDPR. I agree that I can cancel at any time. I agree that the information provided through this site is stored and processed for the purpose of Booking Confirmation. The Taruga Benagil Tours takes data protection very seriously.<br>Email: <a class="w3-text-blue" href="mailto:protecaodedados@tarugatoursbenagilcaves.pt?subject=Protecao%20Dados">protecaodedados@tarugatoursbenagilcaves.pt</a>',
    'spam_txt' => 'You didn´t receive our email, please check your spam box or call +351969617828',
    'booking' => 'All bookings by:',
    'book_email' => 'EMAIL - Only valid after the confirmation from the Taruga Tours Team',
    'book_phone' => 'PHONE - Must by confirmed 30 min. before at our Ticket shop Taruga Tours in Benagil´s beach.',
    'book_after' => 'After submitting the booking order, we will send you a confirmation email.',
    'zero' => 'Minimum is 1 Adult, you added 0',
    'other_buy_it' => 'Other person just bought it, try another date.',
    'get_available' => 'Obtain Dates',
    'no_stock' => 'At this moment, this tour is unavailable.',
    'sorry' => 'Sorry but...',
    'cvv_invalid' => 'CVV (3 digits)',
    'no_match_names' => 'Name and Owner Credit Card must be the same',
    'card_nr_invalid' => 'Credit Card Invalid',
    'telephone_invalid' => 'Telephone (Min: 9 digits)',
    'name_invalid' => 'Name (Only Letters)'
),

'info' => array(
    'choose_date_hour'=> 'Choose Date & Hour',
    'choose_date_calendar'=> 'Choose a Date from Calendar *',
    'choose_hour'=> 'Choose Hour *',
    'buy_now' => 'Buy Now',
    'no_webstorage' => 'Sorry! No Web Storage support on your browser, please open on another browser',
    'credit_card_nr' => 'Credit Card Nº *',
    'name_credit_card' => 'Owner Credit Card *',
    'date_credit_card' => 'Credit Card Expire Date *',
    'tour_data' => 'Tour Info',
    'personal_data' => 'Personal Info',
    'credit_card_data' => 'Credit Card Info',
    'confirm' => 'Confirm',
    'fill_all' => 'Fill all the following fields',
    'wp_set_no_cc_data' => 'Warranty Paynmebt requiredm, but Credit Card data is missing.',
    'date_card_invalid' => 'Credit Card Expire Date Invalid',
    'doubts' => 'Doubts',
    'home' =>'Home',
    'usefull_info' =>'Usefull Info',
    'gallery' => 'Gallery'

),

'arbitrary' => array(
'name' => 'Arbitrary Center',
'txt' =>'Alternative Dispute Resolution Entities<br>
Pursuant to Law no. 144/2015 of September 8, we hereby inform you that the Client may use the following Alternative Dispute Resolution Entities:<br>Provider of the Client of the Travel and Tourism Agencies in www.provedorapavt.com;<br>
Algarve Arbitration Center of Consumption<br>
Arbitral Court<br>
Consumer Arbitrage Network<br>
Edificio Ninho de Empresas, Estrada da Penha 8005-131 Faro<br>
Tel: 289 823 135 Fax: 289 812 213<br>
Email: info@consumoalgarve.pt<br>
www.consumoalgarve.pt or to any of the entities duly indicated in the list available by the Directorate General of Consumers at http://www.consumidor.pt whose consultation we advise.'
),

'cookies' => array(
'txt'=>'This website uses cookies to ensure you get the best experience on our website.',
'link'=>'Know more about cookies.',
'btn' =>'Got It!'
),

    'page_price_prices'=>'Prices',
    'page_price_adult'=>'Adult',
    'page_price_children'=>'Children',
    'page_ticket_shop'=>'Ticket Shop',
    'page_price_duration'=>'Tour Time',
);




}
}

?>