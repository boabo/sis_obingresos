<?php

class RReporteCruceAtcXLS
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    var $datos_detalle;
    var $datos_titulo;
    public  $url_archivo;
    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");


        $this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');

    }

    public function addHoja($name,$index){

        $this->docexcel->createSheet($index)->setTitle($name);
        $this->docexcel->setActiveSheetIndex($index);
        return $this->docexcel;
    }

    function array_sort_by(&$arrIni, $col, $order = SORT_ASC){
        $arrAux = array();
        foreach ($arrIni as $key=> $row)
        {
            $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
            $arrAux[$key] = strtolower($arrAux[$key]);
        }
        array_multisort($arrAux, $order, $arrIni);
    }

    function hiddenString($str, $start = 1, $end = 1){
        $len = strlen($str);
        return substr($str, 0, $start) . str_repeat('X', $len - ($start + $end)) . substr($str, $len - $end, $end);
    }

    function imprimeDatos(){


        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'ffffff'
                )

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682b4'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            ));

        $this->styleVacio = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FA8072'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );


        $datos = $this->objParam->getParametro('datos');//print_r($datos);exit;

        $tipo = $this->objParam->getParametro('tipo');
        $fecha_desde = $this->objParam->getParametro('fecha_desde');
        $fecha_hasta = $this->objParam->getParametro('fecha_hasta');

        $numberFormat = '#,##0.00';

        $index = 0;
        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');



        /*PAGOS DE ATC*/
        $this->addHoja('VENTAS CON SALDO(ATC)',$index);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,6);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);



        /*logo*/
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('BoA ERP');
        $objDrawing->setDescription('BoA ERP');
        $objDrawing->setPath('../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWidth(105);
        $objDrawing->setHeight(75);
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        /*logo*/

        $this->docexcel->getActiveSheet()->getStyle('A1:J4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:J2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:I2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','PAGOS DE TARJETAS CON SALDO (ATC)');

        $this->docexcel->getActiveSheet()->getStyle('A3:J4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:I3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:I4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $fecha = date('d/m/Y');
        $this->docexcel->getActiveSheet()->setCellValue('J1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('J2', $fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleTitulos1);

        $this->docexcel->getActiveSheet()->setCellValue('A5','Establecimiento');
        $this->docexcel->getActiveSheet()->setCellValue('B5','Nombre Est.');
        $this->docexcel->getActiveSheet()->setCellValue('C5','Nro. Terminal');
        $this->docexcel->getActiveSheet()->setCellValue('D5','Nro. Lote');
        $this->docexcel->getActiveSheet()->setCellValue('E5','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('F5','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('G5','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('H5','Importe Pagado');
        $this->docexcel->getActiveSheet()->setCellValue('I5','Importe Vendido');
        $this->docexcel->getActiveSheet()->setCellValue('J5','Saldo');


        $fila = 6;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');

        $monto_pagado = 0;
        $fila_total = $fila;
        $flag_left = true;
        $index_color = 0;

        $currency = '';
        $mount_admin = 0;

        $total_pagado = 0;
        $total_vendido = 0;
        $total_saldo = 0;

        $index_total = 0;
        foreach ($datos as $key => $rec){


            //if($rec->Formato == 'ATC'){

                $styleGroup = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => $color_cell[$index_color]
                        )
                    )
                );

                if($rec->Currency != $currency) {
                    if($total_pagado > 0 && $total_vendido > 0){

                        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
                        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$index_total.':H'.($fila-1).')');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, '=SUM(I'.$index_total.':I'.($fila-1).')');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '=SUM(J'.$index_total.':J'.($fila-1).')');
                        $fila++;

                        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, ' Moneda: ' . $rec->Currency);
                        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':J'.$fila);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);
                    }else{
                        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, ' Moneda: ' . $rec->Currency);
                        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':J'.$fila);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);
                    }
                    $fila++;

                    $index_total = $fila;
                    $total_vendido = 0;
                    $total_pagado = 0;
                    $total_saldo = 0;
                }

                $monto_pagado = $monto_pagado + $rec->PaymentAmount;

                if( $rec->AuthorizationCode != $datos[$key+1]->AuthorizationCode) {


                    if (!$flag_left) {
                        if (number_format($mount_admin, 2, ',', '') - number_format($monto_pagado, 2, ',', '') != 0) {

                            $total_vendido += $monto_pagado;

                            $this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':J' . $fila_total)->applyFromArray($styleGroup);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila_total, $monto_pagado);

                            if (number_format($rec->PaymentAmmount, 2, ',', '') - number_format($monto_pagado, 2, ',', '') != 0) {
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':J' . $fila_total)->getFill()->getStartColor()->setRGB($color_cell[$index_color]);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila_total, '=H' . $fila_total . '-I' . $fila_total);
                            } else {
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila_total, '=H' . $fila_total . '-I' . $fila_total);
                            }

                            $total_saldo = $total_pagado - $total_vendido;

                            $fila += 1;
                        }
                    }else{

                        $total_pagado += $rec->PaymentAmmount;

                        if(number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0) {
                            $total_vendido += $monto_pagado;
                            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($styleGroup);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $rec->EstablishmentCode);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, 'PENDIENTE');
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->TerminalNumber);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->LotNumber);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->AuthorizationCode);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, DateTime::createFromFormat('Y-m-d', $rec->PaymentDate)->format('d/m/Y'));
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->CreditCardNumber);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $monto_pagado);

                            if (number_format($rec->PaymentAmmount, 2, ',', '') - number_format($monto_pagado, 2, ',', '') != 0) {
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':J' . $fila)->getFill()->getStartColor()->setRGB($color_cell[$index_color]);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '=H' . $fila . '-I' . $fila);
                            } else {
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '=H' . $fila . '-I' . $fila);
                            }
                            $fila++;
                        }
                    }

                    $index_color++;
                    if($index_color == 2){
                        $index_color = 0;
                    }
                    $flag_left = true;
                    $monto_pagado = 0;

                }else{

                    if($flag_left) {

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $rec->EstablishmentCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, 'PENDIENTE');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->TerminalNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->LotNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->AuthorizationCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, DateTime::createFromFormat('Y-m-d', $rec->PaymentDate)->format('d/m/Y'));
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->CreditCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, 0);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, 0);

                        $mount_admin = $rec->PaymentAmmount;
                        $total_pagado += $rec->PaymentAmmount;

                        $index_color+=1;
                        if($index_color == 2){
                            $index_color = 0;
                        }

                        $flag_left = false;
                        $fila_total = $fila;
                    }
                }
                $currency = $rec->Currency;
            //}
        }

        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$index_total.':H'.($fila-1).')');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, '=SUM(I'.$index_total.':I'.($fila-1).')');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '=SUM(J'.$index_total.':J'.($fila-1).')');
        //FIN PAGO ATC

        $index++;
        /*PAGOS QUE NO ESTAN EN ATC*/
        $this->addHoja('P. DE PAGO(ATC)',$index);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,6);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);


        /*logo*/
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('BoA ERP');
        $objDrawing->setDescription('BoA ERP');
        $objDrawing->setPath('../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWidth(105);
        $objDrawing->setHeight(75);
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        /*logo*/

        $this->docexcel->getActiveSheet()->getStyle('A1:H4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:H2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:G2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','VENTAS CON TARJETAS PENDIENTES DE PAGO (ATC)');

        $this->docexcel->getActiveSheet()->getStyle('A3:H4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:G3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:G4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $this->docexcel->getActiveSheet()->setCellValue('H1','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('H2',$fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($styleTitulos1);

        $this->docexcel->getActiveSheet()->setCellValue('A5','Agencia');
        $this->docexcel->getActiveSheet()->setCellValue('B5','Descripción');
        $this->docexcel->getActiveSheet()->setCellValue('C5','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('D5','Boleto/Factura/RO');
        $this->docexcel->getActiveSheet()->setCellValue('E5','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('F5','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('G5','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('H5','Importe');

        $fila = 6;
        $record_tickets = [];
        $rec_tickets = [];
        $ticket_payment = 0;
        $admin_payment = 0;

        $code_auth = $datos[0]->AuthorizationCodeFP;//print_r($datos[0]->AuthorizationCodeFP);exit;
        foreach ($datos as $key => $rec){

            //if($rec->Formato == 'ATC'){
                if( $rec->AuthorizationCodeFP != $datos[$key+1]->AuthorizationCodeFP) {
                    /*if($rec->AuthorizationCodeFP == '011143' ){
                        var_dump('B',$admin_payment,  $ticket_payment, $rec->AuthorizationCodeFP, $code_auth);
                    }*/
                    if($ticket_payment!=0 && $rec->AuthorizationCodeFP == $code_auth){
                        $admin_payment = (float)$rec->PaymentAmmount;//number_format($rec->PaymentAmmount, 2, '.', '');//(float)$rec->PaymentAmmount;//
                        $ticket_payment += (float)$rec->PaymentAmount;//number_format($rec->PaymentAmount, 2, '.', '');//(float)$rec->PaymentAmount;//
                        $rec_tickets[] = $rec;

                        if(round($admin_payment,2) != round($ticket_payment,2)){
                            foreach ($rec_tickets as $ticket){
                                $record_tickets[] = $ticket;
                            }
                        }
                    }elseif(round((float)$rec->PaymentAmmount,2) != round((float)$rec->PaymentAmount,2)){
                        $record_tickets[] = $rec;

                    }
                    $admin_payment = 0;
                    $ticket_payment = 0;
                    $rec_tickets = [];
                }else{

                    $admin_payment = (float)$rec->PaymentAmmount;//number_format($rec->PaymentAmmount, 2, '.', '');//(float)$rec->PaymentAmmount;//
                    $ticket_payment += (float)$rec->PaymentAmount;//number_format($rec->PaymentAmount, 2, '.', '');//(float)$rec->PaymentAmount;//
                    $rec_tickets[] = $rec;

                }
                $code_auth = $rec->AuthorizationCodeFP;
            //}
        }

        $this->array_sort_by($record_tickets,'NamePlace');

        $place_payment = 0;
        $currency_payment = 0;

        $row_init = $fila;
        $row_end = -1;

        $sales_place = $record_tickets[0]->NamePlace;
        $currency = $record_tickets[0]->Currency;

        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':H'.$fila);
        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $currency);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFont()->setBold(true);
        $fila++;
        $index_total = $fila;
        $index_color = 0;
        foreach($record_tickets as $ticket){

            $styleGroup = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => $color_cell[$index_color]
                    )
                )
            );


            if($ticket->NamePlace != $sales_place ) {
                $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
                $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFont()->setBold(true);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$index_total.':H'.($fila-1).')');

                $fila++;
                $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':H'.$fila);
                $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $ticket->NamePlace.' Moneda: ' . $ticket->Currency);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFont()->setBold(true);
                $fila++;
                $currency_payment = 0;
                $index_total = $fila;
            }

            if($ticket->Currency != $currency) {

                if($currency_payment > 0){

                    $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFont()->setBold(true);

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$index_total.':H'.($fila-1).')');

                    $fila++;

                    $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':H'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $ticket->Currency);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFont()->setBold(true);
                    $fila++;
                    $index_total = $fila;
                }/*else{
                    $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':H'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $ticket->Currency);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFont()->setBold(true);
                }*/
            }

            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($styleGroup);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $ticket->Iatacode);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $ticket->NameOffice);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, DateTime::createFromFormat('Y-m-d', $ticket->IssueDate)->format('d/m/Y'));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $ticket->TicketNumber);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $ticket->AccountCardNumber);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $ticket->AuthorizationCodeFP);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $ticket->Currency);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $ticket->PaymentAmount);

            $index_color+=1;
            if($index_color == 2){
                $index_color = 0;
            }

            $fila++;
            $currency_payment += (float)$ticket->PaymentAmount;
            $currency = $ticket->Currency;
            $sales_place = $ticket->NamePlace;

        }

        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':H'.$fila)->getFont()->setBold(true);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$index_total.':H'.($fila-1).')');
        //FIN PAGOS RET

        /*TARJETAS PAGADAS*/
        $index++;
        $this->addHoja('T. PAGADAS(ATC)',$index);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,6);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);



        /*logo*/
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('BoA ERP');
        $objDrawing->setDescription('BoA ERP');
        $objDrawing->setPath('../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWidth(105);
        $objDrawing->setHeight(75);
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        /*logo*/

        $this->docexcel->getActiveSheet()->getStyle('A1:F4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:F2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:E2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','VENTAS CON TARJETAS PAGADAS (ATC)');

        $this->docexcel->getActiveSheet()->getStyle('A3:F4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:E3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:E4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $this->docexcel->getActiveSheet()->setCellValue('F1','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('F2', $fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleTitulos1);

        $this->docexcel->getActiveSheet()->setCellValue('A5','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('B5','Descripción');
        $this->docexcel->getActiveSheet()->setCellValue('C5','Boleto/Factura/RO');
        $this->docexcel->getActiveSheet()->setCellValue('D5','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('E5','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('F5','Importe');


        $fila = 6;
        $record_tickets = [];
        $rec_tickets = [];
        $ticket_payment = 0;
        $admin_payment = 0;

        $code_auth = $datos[0]->AuthorizationCodeFP;//print_r($datos[0]->AuthorizationCodeFP);exit;
        foreach ($datos as $key => $rec){
            //if($rec->Formato == 'ATC'){
                if( $rec->AuthorizationCodeFP != $datos[$key+1]->AuthorizationCodeFP) {
                    if($ticket_payment!=0 && $rec->AuthorizationCodeFP == $code_auth){
                        $admin_payment = (float)$rec->PaymentAmmount;
                        $ticket_payment += (float)$rec->PaymentAmount;
                        $rec_tickets[] = $rec;

                        if(round($admin_payment,2) == round($ticket_payment,2)){
                            foreach ($rec_tickets as $ticket){
                                $record_tickets[] = $ticket;
                            }
                        }
                    }elseif(round((float)$rec->PaymentAmmount,2) == round((float)$rec->PaymentAmount,2)){
                        $record_tickets[] = $rec;

                    }
                    $admin_payment = 0;
                    $ticket_payment = 0;
                    $rec_tickets = [];
                }else{

                    $admin_payment = (float)$rec->PaymentAmmount;
                    $ticket_payment += (float)$rec->PaymentAmount;
                    $rec_tickets[] = $rec;

                }
                $code_auth = $rec->AuthorizationCodeFP;
            //}
        }

        $this->array_sort_by($record_tickets,'NamePlace');
        $place_payment = 0;
        $currency_payment = 0;

        $row_init = $fila;
        $row_end = -1;

        $sales_place = $record_tickets[0]->NamePlace;
        $currency = $record_tickets[0]->Currency;

        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':F'.$fila);
        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $currency);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true);
        $fila++;
        $index_total = $fila;
        $index_color = 0;
        foreach($record_tickets as $ticket){

            $styleGroup = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => $color_cell[$index_color]
                    )
                )
            );


            if($ticket->NamePlace != $sales_place ) {
                $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':E'.$fila);
                $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':E'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '=SUM(F'.$index_total.':F'.($fila-1).')');

                $fila++;
                $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':F'.$fila);
                $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $ticket->NamePlace.' Moneda: ' . $ticket->Currency);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true);
                $fila++;
                $currency_payment = 0;
                $index_total = $fila;
            }

            if($ticket->Currency != $currency) {

                if($currency_payment > 0){

                    $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':E'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':E'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true);

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '=SUM(F'.$index_total.':F'.($fila-1).')');

                    $fila++;

                    $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':F'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $ticket->Currency);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true);
                    $fila++;
                    $index_total = $fila;
                }
            }

            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($styleGroup);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, DateTime::createFromFormat('Y-m-d', $ticket->IssueDate)->format('d/m/Y'));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $ticket->NameOffice);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $ticket->TicketNumber);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $ticket->AccountCardNumber);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $ticket->AuthorizationCodeFP);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $ticket->PaymentAmount);

            $index_color+=1;
            if($index_color == 2){
                $index_color = 0;
            }

            $fila++;
            $currency_payment += (float)$ticket->PaymentAmount;
            $currency = $ticket->Currency;
            $sales_place = $ticket->NamePlace;

        }

        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':E'.$fila);
        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':E'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '=SUM(F'.$index_total.':F'.($fila-1).')');

        //FIN TARJETAS PAGADAS

        $index++;
        /*PAGOS QUE ESTAN EN ATC Y RET*/

        $this->addHoja('PAGOS ATC<->RET',$index);

        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);

        /*logo*/
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('BoA ERP');
        $objDrawing->setDescription('BoA ERP');
        $objDrawing->setPath('../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWidth(105);
        $objDrawing->setHeight(75);
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        /*logo*/

        $this->docexcel->getActiveSheet()->getStyle('A1:R4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:R2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:Q2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','PAGO DE TARJETAS QUE ESTAN EN EL ARCHIVO ATC Y ARCHIVO RET (TICKETS)');

        $this->docexcel->getActiveSheet()->getStyle('A3:R4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:Q3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:Q4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $this->docexcel->getActiveSheet()->setCellValue('R1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('R2', $fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:R6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:J5');
        $this->docexcel->getActiveSheet()->setCellValue('A5','PAGOS ATC');


        $this->docexcel->getActiveSheet()->setCellValue('A6','Establecimiento');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Terminal');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Lote');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Ticket');
        $this->docexcel->getActiveSheet()->setCellValue('E6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Hora');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('H6','Monto Pagado');
        $this->docexcel->getActiveSheet()->setCellValue('I6','Nro. Authorización');
        $this->docexcel->getActiveSheet()->setCellValue('J6','Nro. Tarjeta');

        $this->docexcel->getActiveSheet()->mergeCells('K5:R5');
        $this->docexcel->getActiveSheet()->setCellValue('K5','PAGOS RET (TICKETS)');

        $this->docexcel->getActiveSheet()->setCellValue('K6','Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('L6','Intancia');
        $this->docexcel->getActiveSheet()->setCellValue('M6','Ticket Number');
        $this->docexcel->getActiveSheet()->setCellValue('N6','Fecha Emisión');
        $this->docexcel->getActiveSheet()->setCellValue('O6','Payment Amount');
        $this->docexcel->getActiveSheet()->setCellValue('P6','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('Q6','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('R6','Diferencia');


        $fila = 7;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');

        $monto_pagado = 0;
        $fila_total = $fila;
        $flag_left = true;
        $index_color = 0;

        $point_sale = '';

        foreach ($datos as $key => $rec){
            //if($rec->Formato == 'ATC' && $rec->ResultType == 'pago_both'){
                $styleGroup = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => $color_cell[$index_color]
                        )
                    )
                );

                if($rec->Iatacode != $point_sale) {
                    $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $rec->NameOffice);
                    $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':F'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('G' . $fila, ' AGT/Punto Venta: ' . $rec->Iatacode );
                    $this->docexcel->getActiveSheet()->mergeCells('G'.$fila.':L'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('M' . $fila, ' Moneda: ' . $rec->Currency);
                    $this->docexcel->getActiveSheet()->mergeCells('M'.$fila.':R'.$fila);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':R'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':R'.$fila)->getFont()->setBold(true);

                    $fila++;
                }

                $monto_pagado = $monto_pagado + $rec->PaymentAmount;

                if( $rec->AuthorizationCode != $datos[$key+1]->AuthorizationCode) {

                    if (!$flag_left){
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila_total.':R'.($fila + 1))->applyFromArray($styleGroup);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila_total, $monto_pagado);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila_total, '');

                        if(number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0){
                            $this->docexcel->getActiveSheet()->getStyle('A'.$fila_total.':R'.$fila_total)->getFill()->getStartColor()->setRGB($color_cell[2]);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila_total, '=H'.$fila_total.'-O'.$fila_total);
                        }else{
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila_total, '=H'.$fila_total.'-O'.$fila_total);
                        }

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->mp);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->IP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->TicketNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila + 1, DateTime::createFromFormat('Y-m-d', $rec->IssueDate)->format('d/m/Y'));
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila + 1, $rec->PaymentAmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila + 1, $rec->AuthorizationCodeFP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila + 1, $rec->AccountCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila + 1, '');

                        $fila += 2;
                    }else{
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':R'.$fila)->applyFromArray($styleGroup);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $rec->EstablishmentCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, DateTime::createFromFormat('Y-m-d', $rec->PaymentDate)->format('d/m/Y'));
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->computed);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->AuthorizationCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->CreditCardNumber);

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $rec->mp);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $rec->IP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $rec->TicketNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, DateTime::createFromFormat('Y-m-d', $rec->IssueDate)->format('d/m/Y'));
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $rec->PaymentAmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $rec->AuthorizationCodeFP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, $rec->AccountCardNumber);

                        if(number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0){
                            $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':R'.$fila)->getFill()->getStartColor()->setRGB($color_cell[2]);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, '=H'.$fila.'-O'.$fila);
                        }else{
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, '=H'.$fila.'-O'.$fila);
                        }
                        $fila++;
                    }

                    $index_color+=1;
                    if($index_color == 2){
                        $index_color = 0;
                    }
                    $flag_left = true;
                    $monto_pagado = 0;

                }else{


                    if($flag_left) {

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $rec->EstablishmentCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, DateTime::createFromFormat('Y-m-d', $rec->PaymentDate)->format('d/m/Y'));
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->computed);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->AuthorizationCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->CreditCardNumber);


                        $flag_left = false;
                        $fila_total = $fila;
                    }else{
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '');
                    }

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->mp);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->IP);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->TicketNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila + 1, DateTime::createFromFormat('Y-m-d', $rec->IssueDate)->format('d/m/Y'));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila + 1, $rec->PaymentAmount);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila + 1, $rec->AuthorizationCodeFP);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila + 1, $rec->AccountCardNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila + 1, '');

                    $fila++;
                }

                $point_sale = $rec->Iatacode;
            //}
        }
    }

    function obtenerFechaEnLetra($fecha){
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $dia= date("d", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        // var_dump()
        $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
        return $dia.' de '.$mes.' del '.$anno;
    }
    function generarReporte(){
        //$this->imprimeDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>