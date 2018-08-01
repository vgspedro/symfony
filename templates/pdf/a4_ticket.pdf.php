
<page format="210x297" orientation="P" style="font: arial">

<?php
    $top = 0;
    $left = 0;
    $i = 0;
    $j = 0;

foreach ($ticket  as $tickets) {    
    if(($i * $j) == 6){
        $top = 0;
        $left = 0;
        $i = 0;
        $j = 0;
        ?>
        </page>
        <page format="210x297" orientation="P" style="font: arial">
        <?php
    }

    if($i == 3)
    {
        $i = 0;
        $j++;
    }
    $top = $j * 99;
    $left = $i * 70;

    //NAME MAX SIZE
    if (strlen($tickets['client_name']) > 15)
        $tickets['client_name'] = substr($tickets['client_name'], 0, 12) . '...';

    //TEL MAX SIZE
    if (strlen($tickets['client_tel']) > 20)
       $tickets['client_tel'] = substr($tickets['client_tel'], 0, 17) . '...';

    //TICKET MAX SIZE
       if (strlen($tickets['ticket_event_place']) > 16)
        $tickets['ticket_event_place'] = substr($tickets['ticket_event_place'], 0, 13) . '...';

    //PICk UP MAX SIZE
       if (strlen($tickets['ticket_pickup']) > 76)
        $tickets['ticket_pickup'] = substr($tickets['ticket_pickup'], 0, 73) . '...';
        else
    //TICKET TYPE MAX SIZE
       if (strlen($tickets['ticket_type']) > 21)
        $tickets['ticket_type'] = substr($tickets['ticket_type'], 0, 18) . '...';

    //TICKET TYPE MAX SIZE
       if (strlen($tickets['operator_name']) > 28)
        $tickets['operator_name'] = substr($tickets['operator_name'], 0, 25) . '...';

    //TICKET PRICE TO FLOAT 2 PLACES
       $tickets['ticket_price'] ? $tickets['ticket_price'] = $tickets['ticket_price'] : $tickets['ticket_price'] = 0;
    
        $encoded = str_rot13(base64_encode($tickets['ticket_n']));
    //TICKET STATUS
    $tickets['ticket_status'] === 'used' || $tickets['ticket_status'] === 'canceled' ? $color = 'color:#FFF' : $color ='color:#000';

    /* COMISSION CHARGE TOTAL false || true */

    if ($tickets['charge_total'] == false){
        $tickets['total_comission'];
        $total = $tickets['ticket_price'] - $tickets['total_comission'];
        $price = '<span style="color:#F00">COBRAR: '.number_format((float)$total, 2, '.', '').'€</span>';
        $partly_paid ='<div style="font-size:11px;font-weight:bold;position:absolute; top:5mm; left:23mm; color:#F00">RECEBIDO: '. $tickets['total_comission'].' €</div>';
    }
    else {
        $partly_paid ='';
        $price = '<span>Preço: '. $tickets['ticket_price'].'€</span>';
    }

    file_exists($tickets['operator_logo']) ? $logo = $tickets['operator_logo'] : $logo = 'img/noimg.png';

    ?>
<div style="left:<?php echo $left?>mm;position:absolute;top:<?php echo $top?>mm">
    <div style="margin:2.5mm 2.5mm;width:60mm;height:89mm;border: solid 2px #2196F3; border-collapse: collapse" align="center">
        <img src="<?php echo $logo ?>" style="margin-top:3mm;margin-left:10mm; width:40mm;height:17mm">
        <h2 style="text-align: center;font-size: 15px;font-weight:bold;margin-bottom:-10mm">
            <?php echo $tickets['operator_name'] ?>
        </h2>
        <h2 style="text-align:center;font-weight:100;margin-bottom: -2mm">
            <?php echo $tickets['ticket_type'] ?>
        </h2>
        <div style="width:56mm; margin:2mm; border-top:1px solid #777">
            <div style="width:40mm">    
                <div style="margin-top:2mm">Nome: <?php echo $tickets['client_name'] ?></div>
                <div style="margin-top:3px">Tel: <span style="font-size:13px"><?php echo $tickets['client_tel'] ?></span></div>
                <div style="margin-top:3px"><?php echo $price ?></div>
                <div style="margin-top:3px">Local: <?php echo $tickets['ticket_event_place'] ?></div>
                <div style="margin-top:3px">Data: <span style="font-size:13px"><?php echo $tickets['ticket_event_date'] ?></span></div>
            </div>
            <div style="font-size:10px; width:60mm; margin-top:3mm">
                <div>
                    <span style="font-weight:bold">PickUp: </span><?php echo $tickets['ticket_pickup'] ?>
                </div>
                <div style="margin-top: 1mm">
                    <span style="font-weight:bold">Extras: </span><?php foreach($tickets['ticket_extras'] as $extras) echo $extras.',' ?>
                </div>
            </div>
        </div>
    </div>
    <div style="rotate: 90; position: absolute; width: 30mm; height: 4mm; left: 61mm; top: 4mm; font-style: italic; font-weight: normal; text-align: center; font-size: 3mm; background:black;color:#FFF; padding: 4px 0px 0px 0px">
        <?php echo $tickets['ticket_sku'] ?>
    </div>
        <div style="rotate: 90; position: absolute; width: 40mm; height: 4mm; left: 3mm; top: 4mm; font-style: italic; font-weight: normal; text-align: center; font-size: 2mm;color:#000; padding: 4px 0px 0px 0px">
        <?php echo $view['translator']->trans('vat_included') ?>
    </div>
    <qrcode value="<?php echo $encoded ?>" ec="H" style="<?php echo $color ?>; border:none;position:absolute;top:52mm;width:20mm;left:45mm">
    </qrcode>
    <?php echo $partly_paid ?>
    <div style="font-size: 8px; position:absolute; width:65mm;left:3mm;top:92.5mm">
        <a href="../<?php echo $view['router']->path('product_terms_conditions', array('productId' => $tickets['product_id'])) ?>">
            Termos e Condições 
            <i>Terms & Conditions</i>
        </a>
    </div>
</div>
<?php
        $i++;
   }
?>




</page>

