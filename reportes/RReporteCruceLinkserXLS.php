<?php

class RReporteCruceLinkserXLS
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
        //print_r($datos);exit;
        $tipo = $this->objParam->getParametro('tipo');
        $fecha_desde = $this->objParam->getParametro('fecha_desde');
        $fecha_hasta = $this->objParam->getParametro('fecha_hasta');
        $fecha = date('d/m/Y');
        $numberFormat = '#,##0.00';

        $index = 0;
        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');



        /*PAGOS DE LINKSER*/
        $this->addHoja('ADMINISTRADORA (LINKSER)',$index);

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
        $this->docexcel->getActiveSheet()->setCellValue('A1','REPORTE CRUCE DE TARJETAS - PAGOS ADMINISTRADORA SIN RELACIÓN CON TICKETS(BOA)');

        $this->docexcel->getActiveSheet()->getStyle('A3:R4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:Q3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:Q4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $this->docexcel->getActiveSheet()->setCellValue('R1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('R2', $fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:R6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:J5');
        $this->docexcel->getActiveSheet()->setCellValue('A5','PAGOS LINKSER');


        $this->docexcel->getActiveSheet()->setCellValue('A6','Establecimiento');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Terminal');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Lote');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Ticket');
        $this->docexcel->getActiveSheet()->setCellValue('E6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Hora');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('H6','Nro. Authorización');
        $this->docexcel->getActiveSheet()->setCellValue('I6','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('J6','Monto Pagado');

        $this->docexcel->getActiveSheet()->mergeCells('K5:R5');
        $this->docexcel->getActiveSheet()->setCellValue('K5','PAGOS RET (TICKETS)');

        $this->docexcel->getActiveSheet()->setCellValue('K6','Agencia');
        $this->docexcel->getActiveSheet()->setCellValue('L6','Descripción');
        $this->docexcel->getActiveSheet()->setCellValue('M6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('N6','Boleto/Factura/RO');
        $this->docexcel->getActiveSheet()->setCellValue('O6','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('P6','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('Q6','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('R6','Importe');


        $fila = 7;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');

        $monto_pagado = 0;
        $fila_total = $fila;
        $flag_left = true;
        $index_color = 0;

        $point_sale = '';
        $index_total = 0;
        $currency = '';
        $mount_admin = 0; $total_pagado = 0; $total_vendido = 0;
        $establishment_code = '';
        $payment_ammount = 0;

        foreach ($datos as $key => $rec){
            if($rec->ResultType == 'pago_administradora'){
                $styleGroup = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => $color_cell[$index_color]
                        )
                    )
                );


                if($rec->Currency != $currency) {
                    if($payment_ammount > 0){

                        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
                        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);

                        /*$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$index_total.':H'.($fila-1).')');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, '=SUM(I'.$index_total.':I'.($fila-1).')');*/
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
                }

                $payment_ammount += $rec->PaymentAmmount;

                if( $rec->AuthorizationCode != $datos[$key+1]->AuthorizationCode) {

                    $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($styleGroup);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, ltrim($rec->TerminalNumber, '0').' / '.$rec->NameEstable.' ('.$rec->TypeEstable.')');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y') : '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->PaymentHour);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->AuthorizationCode);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->CreditCardNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, trim($rec->CreditCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->PaymentAmmount);


                    $index_color++;
                    if($index_color == 2){
                        $index_color = 0;
                    }
                    $fila++;
                }else{

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, ltrim($rec->TerminalNumber, '0').' / '.$rec->NameEstable.' ('.$rec->TypeEstable.')');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y') : '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->PaymentHour);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->AuthorizationCode);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->CreditCardNumber);
                    $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(8, $fila, trim($rec->CreditCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->PaymentAmmount);

                }
                $currency = $rec->Currency;
                $establishment_code = $rec->TerminalNumber;
            }
        }

        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);
        /*$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$index_total.':H'.($fila-1).')');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, '=SUM(I'.$index_total.':I'.($fila-1).')');*/
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '=SUM(J'.$index_total.':J'.($fila-1).')');
        //FIN PAGO LINKSER

        $index++;
        /*PAGOS QUE NO ESTAN EN LINKSER*/
        $this->addHoja('TICKETS (BOA)',$index);
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
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
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
        $this->docexcel->getActiveSheet()->setCellValue('A1','REPORTE CRUCE DE TARJETAS - TICKETS(BOA) SIN RELACIÓN CON PAGOS DE LA ADMINISTRADORA');

        $this->docexcel->getActiveSheet()->getStyle('A3:R4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:Q3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:Q4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $this->docexcel->getActiveSheet()->setCellValue('R1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('R2', $fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:R6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:J5');
        $this->docexcel->getActiveSheet()->setCellValue('A5','PAGOS LINKSER');


        $this->docexcel->getActiveSheet()->setCellValue('A6','Establecimiento');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Terminal');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Lote');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Ticket');
        $this->docexcel->getActiveSheet()->setCellValue('E6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Hora');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('H6','Nro. Authorización');
        $this->docexcel->getActiveSheet()->setCellValue('I6','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('J6','Monto Pagado');

        $this->docexcel->getActiveSheet()->mergeCells('K5:R5');
        $this->docexcel->getActiveSheet()->setCellValue('K5','PAGOS RET (TICKETS)');

        $this->docexcel->getActiveSheet()->setCellValue('K6','Agencia');
        $this->docexcel->getActiveSheet()->setCellValue('L6','Descripción');
        $this->docexcel->getActiveSheet()->setCellValue('M6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('N6','Boleto/Factura/RO');
        $this->docexcel->getActiveSheet()->setCellValue('O6','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('P6','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('Q6','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('R6','Importe');

        $fila = 7;
        $record_tickets = [];
        $rec_tickets = [];
        $ticket_payment = 0;
        $admin_payment = 0;

        $code_auth = '';
        foreach ($datos as $key => $rec){
            if($rec->ResultType == 'pago_ret'){
                $record_tickets[] = $rec;
            }
        }

        $this->array_sort_by($record_tickets,'NamePlace');

        $place_payment = 0;
        $currency_payment = 0;

        $row_init = $fila;
        $row_end = -1;

        $sales_place = $record_tickets[0]->NamePlace;
        $currency = $record_tickets[0]->CurrencyTicket;

        $this->docexcel->getActiveSheet()->mergeCells('K'.$fila.':R'.$fila);
        $this->docexcel->getActiveSheet()->setCellValue('K' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $currency);
        $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
        $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFont()->setBold(true);

        $fila++;
        $index_total = $fila;
        $index_color = 0;
        //print_r($record_tickets);exit;
        foreach($record_tickets as $key => $ticket){

            $styleGroup = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => $color_cell[$index_color]
                    )
                )
            );


            if($ticket->NamePlace != $sales_place ) {
                $this->docexcel->getActiveSheet()->mergeCells('K'.$fila.':Q'.$fila);
                $this->docexcel->getActiveSheet()->setCellValue('K' . $fila, 'TOTAL MONEDA: ' . $currency);
                $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':Q'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFont()->setBold(true);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, '=SUM(R'.$index_total.':R'.($fila-1).')');

                $fila++;
                $this->docexcel->getActiveSheet()->mergeCells('K'.$fila.':R'.$fila);
                $this->docexcel->getActiveSheet()->setCellValue('K' . $fila, 'Estación: ' . $ticket->NamePlace.' Moneda: ' . $ticket->Currency);
                $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFont()->setBold(true);
                $fila++;
                $currency_payment = 0;
                $index_total = $fila;
            }

            if($ticket->CurrencyTicket != $currency) {

                if($currency_payment > 0){

                    $this->docexcel->getActiveSheet()->mergeCells('K'.$fila.':Q'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('K' . $fila, 'TOTAL MONEDA: ' . $currency);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':Q'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFont()->setBold(true);

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, '=SUM(R'.$index_total.':R'.($fila-1).')');

                    $fila++;

                    $this->docexcel->getActiveSheet()->mergeCells('K'.$fila.':R'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('K' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $ticket->CurrencyTicket);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFont()->setBold(true);
                    $fila++;
                    $index_total = $fila;
                }/*else{
                    $this->docexcel->getActiveSheet()->mergeCells('K'.$fila.':R'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('K' . $fila, 'Estación: ' . $sales_place.' Moneda: ' . $ticket->PaymentCurrency);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFont()->setBold(true);
                }*/
            }

            $this->docexcel->getActiveSheet()->getStyle('K' . $fila . ':R' . $fila)->applyFromArray($styleGroup);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $ticket->Iatacode);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $ticket->NameOffice);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $ticket->IssueDate != null || $ticket->IssueDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $ticket->IssueDate)->format('d/m/Y') : '');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $ticket->TicketNumber.' ['.$ticket->DocummentType.']');
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $ticket->AccountCardNumber);
            $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(14, $fila, trim($ticket->AccountCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $ticket->AuthorizationCodeFP);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, $ticket->CurrencyTicket);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, $ticket->PaymentAmount);

            if( $ticket->AuthorizationCodeFP != $record_tickets[$key+1]->AuthorizationCodeFP) {
                $index_color += 1;
                if ($index_color == 2) {
                    $index_color = 0;
                }
            }

            $fila++;
            $currency_payment += (float)$ticket->PaymentAmount;
            $currency = $ticket->CurrencyTicket;
            $sales_place = $ticket->NamePlace;

        }

        $this->docexcel->getActiveSheet()->mergeCells('K'.$fila.':Q'.$fila);
        $this->docexcel->getActiveSheet()->setCellValue('K' . $fila, 'TOTAL MONEDA: ' . $currency);
        $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':Q'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('K'.$fila.':R'.$fila)->getFont()->setBold(true);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, '=SUM(R'.$index_total.':R'.($fila-1).')');
        //FIN PAGOS RET

        /*TARJETAS PAGADAS*/

        $index++;
        /*PAGOS QUE ESTAN EN LINKSER Y RET*/

        $this->addHoja('CONCILIACIÓN SIN OBSERVACIONES',$index);

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
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('T')->setWidth(45);

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

        $this->docexcel->getActiveSheet()->getStyle('A1:T4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:T2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:S2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','CRUCE DE TARJETAS LINKSER Y ARCHIVO RET (TICKETS)');

        $this->docexcel->getActiveSheet()->getStyle('A3:T4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:S3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:S4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $this->docexcel->getActiveSheet()->setCellValue('T1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('T2', $fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:T6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:J5');
        $this->docexcel->getActiveSheet()->setCellValue('A5','PAGOS LINKSER');


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

        $this->docexcel->getActiveSheet()->mergeCells('K5:S5');
        $this->docexcel->getActiveSheet()->setCellValue('K5','PAGOS RET (TICKETS)');

        $this->docexcel->getActiveSheet()->setCellValue('K6','Agencia');
        $this->docexcel->getActiveSheet()->setCellValue('L6','Descripción');
        $this->docexcel->getActiveSheet()->setCellValue('M6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('N6','Boleto/Factura/RO');
        $this->docexcel->getActiveSheet()->setCellValue('O6','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('P6','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('Q6','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('R6','Importe');
        $this->docexcel->getActiveSheet()->setCellValue('S6','Diferencia');
        $this->docexcel->getActiveSheet()->setCellValue('T6','Observaciones');


        $fila = 7;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');

        $monto_pagado = 0;
        $fila_total = $fila;
        $flag_left = true;
        $index_color = 0;

        $point_sale = '';

        $record_tickets = [];
        foreach ($datos as $key => $rec){
            if($rec->ResultType == 'pago_both'){
                $record_tickets[] = $rec;
            }
        }


        $styleGroupBOB_USD = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'b066bb'
                )
            )
        );
        //var_dump($record_tickets);exit;
        $tickets_observed = array();
        $group_tickets_observed = array();
        foreach ($record_tickets as $key => $rec){

            $group_tickets_observed [] = $rec;
            $moneda_adm = $rec->Currency;
            $moneda_boa = $rec->CurrencyTicket;

            if($rec->ResultType == 'pago_both'){
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
                    $this->docexcel->getActiveSheet()->mergeCells('M'.$fila.':T'.$fila);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':T'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':T'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':T'.$fila)->getFont()->setBold(true);

                    $fila++;
                }

                $monto_pagado = $monto_pagado + $rec->PaymentAmount;

                if( $rec->AuthorizationCode != $record_tickets[$key+1]->AuthorizationCode) {
                    /*if (trim($rec->TicketNumber) == '9302405445898'){
                        var_dump($moneda_adm, $moneda_boa, $flag_left, $rec->AuthorizationCode, $record_tickets[$key+1]->AuthorizationCode);exit;
                    }*/
                    if (!$flag_left){

                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila_total.':T'.($fila + 1))->applyFromArray($styleGroup);


                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila_total, $monto_pagado);

                        if( number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0 || $moneda_adm != $moneda_boa ){
                            if ($moneda_adm != $moneda_boa){
                                //$this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':T' . $fila)->getFill()->getStartColor()->setRGB($styleGroupBOB_USD);
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':T' . ($fila+1))->applyFromArray($styleGroupBOB_USD);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila_total, 'Administradora [ '.$moneda_adm.' ] contra Ticket o Factura [ '.$moneda_boa.' ]');
                            }else {
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':T' . $fila_total)->getFill()->getStartColor()->setRGB($color_cell[2]);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila_total, '=H' . $fila_total . '-R' . $fila_total);
                            }

                            //$fila = $fila_total - 2;
                            foreach ($group_tickets_observed as $ticket){
                                $tickets_observed [] = $ticket;
                            }
                            $group_tickets_observed = array();

                        }else{
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila_total, '=H'.$fila_total.'-R'.$fila_total);
                        }

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->Iatacode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->NameOffice);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->IssueDate != null || $rec->IssueDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila + 1, $rec->TicketNumber.' ['.$rec->DocummentType.']');
                        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila + 1, $rec->AccountCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(14, $fila + 1, trim($rec->AccountCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila + 1, $rec->AuthorizationCodeFP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila + 1, $rec->CurrencyTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila + 1, $rec->PaymentAmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila + 1, '');

                        if( number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0 || $moneda_adm != $moneda_boa ){
                            $fila = $fila_total - 2;
                        }

                        $fila += 2;
                    }else{

                        if ($moneda_adm != $moneda_boa){
                            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroupBOB_USD);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila, 'Administradora [ '.$moneda_adm.' ] contra Ticket o Factura [ '.$moneda_boa.' ]');
                        }else {
                            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroup);
                        }
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, ltrim($rec->TerminalNumber, '0').' / '.$rec->NameEstable.' ('.$rec->TypeEstable.')');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->computed);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->AuthorizationCode);
                        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->CreditCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(9, $fila, trim($rec->CreditCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $rec->Iatacode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $rec->NameOffice);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $rec->TicketNumber.' ['.$rec->DocummentType.']');
                        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $rec->AccountCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(14, $fila, trim($rec->AccountCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $rec->AuthorizationCodeFP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, $rec->CurrencyTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, $rec->PaymentAmount);

                        if(number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0 || $moneda_adm != $moneda_boa ){

                            if ($moneda_adm != $moneda_boa){
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroupBOB_USD);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila, 'Administradora [ '.$moneda_adm.' ] contra Ticket o Factura [ '.$moneda_boa.' ]');
                            }else {
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->getFill()->getStartColor()->setRGB($color_cell[2]);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila, '=H'.$fila.'-R'.$fila);
                            }

                            $fila = $fila-1;
                            foreach ($group_tickets_observed as $ticket){
                                $tickets_observed [] = $ticket;
                            }
                            $group_tickets_observed = array();

                        }else{
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila, '=H'.$fila.'-R'.$fila);
                        }
                        $fila++;
                    }

                    $index_color+=1;
                    if($index_color == 2){
                        $index_color = 0;
                    }
                    $flag_left = true;
                    $monto_pagado = 0;
                    $group_tickets_observed = array();

                }else{

                    if($flag_left) {

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, ltrim($rec->TerminalNumber, '0').' / '.$rec->NameEstable.' ('.$rec->TypeEstable.')');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->computed);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->AuthorizationCode);
                        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->CreditCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(9, $fila, trim($rec->CreditCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);


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


                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->Iatacode);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->NameOffice);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->IssueDate != null || $rec->IssueDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y') : '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila + 1, $rec->TicketNumber.' ['.$rec->DocummentType.']');
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila + 1, $rec->AccountCardNumber);
                    $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(14, $fila + 1, trim($rec->AccountCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila + 1, $rec->AuthorizationCodeFP);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila + 1, $rec->CurrencyTicket);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila + 1, $rec->PaymentAmount);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila + 1, '');

                    $fila++;
                }

                $point_sale = $rec->Iatacode;
            }
        }

        /*************************************************************** OBSERVADAS ***************************************************************/
        /*TARJETAS OBSERVADAS*/

        $index++;
        /*PAGOS QUE ESTAN EN LINKSER Y RET OBSERVADAS*/

        $this->addHoja('CONCILIACIÓN CON OBSERVACIONES',$index);

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
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('T')->setWidth(45);

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

        $this->docexcel->getActiveSheet()->getStyle('A1:T4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:T2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:T2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','CRUCE DE TARJETAS LINKSER Y ARCHIVO RET (TICKETS)');

        $this->docexcel->getActiveSheet()->getStyle('A3:T4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:S3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:S4');
        //$this->docexcel->getActiveSheet()->setCellValue('A4','Ingresos');

        $this->docexcel->getActiveSheet()->setCellValue('T1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('T2', $fecha);

        $this->docexcel->getActiveSheet()->getStyle('A5:T6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:J5');
        $this->docexcel->getActiveSheet()->setCellValue('A5','PAGOS LINKSER');


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

        $this->docexcel->getActiveSheet()->mergeCells('K5:T5');
        $this->docexcel->getActiveSheet()->setCellValue('K5','PAGOS RET (TICKETS)');

        $this->docexcel->getActiveSheet()->setCellValue('K6','Agencia');
        $this->docexcel->getActiveSheet()->setCellValue('L6','Descripción');
        $this->docexcel->getActiveSheet()->setCellValue('M6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('N6','Boleto/Factura/RO');
        $this->docexcel->getActiveSheet()->setCellValue('O6','Nro. Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('P6','Nro. Autorización');
        $this->docexcel->getActiveSheet()->setCellValue('Q6','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('R6','Importe');
        $this->docexcel->getActiveSheet()->setCellValue('S6','Diferencia');
        $this->docexcel->getActiveSheet()->setCellValue('T6','Observaciones');


        $fila = 7;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59', 'b066bb');

        $color_obs = array('ffc7ce', 'b066bb');

        $monto_pagado = 0;
        $fila_total = $fila;
        $flag_left = true;
        $index_color = 0;

        $point_sale = '';

        $styleGroupBOB_USD = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'b066bb'
                )
            )
        );

        foreach ($tickets_observed as $key => $rec){

            $moneda_adm = $rec->Currency;
            $moneda_boa = $rec->CurrencyTicket;

            if($rec->ResultType == 'pago_both'){
                $styleGroup = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => $color_obs[$index_color]
                        )
                    )
                );

                /*if($rec->Iatacode != $point_sale) {
                    $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'Estación: ' . $rec->NameOffice);
                    $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':F'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('G' . $fila, ' AGT/Punto Venta: ' . $rec->Iatacode );
                    $this->docexcel->getActiveSheet()->mergeCells('G'.$fila.':L'.$fila);
                    $this->docexcel->getActiveSheet()->setCellValue('M' . $fila, ' Moneda: ' . $rec->Currency);
                    $this->docexcel->getActiveSheet()->mergeCells('M'.$fila.':T'.$fila);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':T'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':T'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':T'.$fila)->getFont()->setBold(true);

                    $fila++;
                }*/

                $monto_pagado = $monto_pagado + $rec->PaymentAmount;

                if( $rec->AuthorizationCode != $tickets_observed[$key+1]->AuthorizationCode) {

                    if (!$flag_left){
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila_total.':T'.($fila + 1))->applyFromArray($styleGroup);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila_total, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila_total, $monto_pagado);

                        if( $moneda_adm != $moneda_boa || number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0){
                            if ($moneda_adm != $moneda_boa){
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':T' . ($fila+1))->applyFromArray($styleGroup);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila_total, 'Administradora [ '.$moneda_adm.' ] contra Ticket o Factura [ '.$moneda_boa.' ]');
                            }else {
                                //$this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':T' . ($fila+1))->getFill()->getStartColor()->setRGB($color_cell[2]);
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila_total . ':T' . ($fila+1))->applyFromArray($styleGroup);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila_total, '=H' . $fila_total . '-R' . $fila_total);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila_total, 'Diferencia en los Montos');
                            }
                        }else{
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila_total, '=H'.$fila_total.'-R'.$fila_total);
                        }

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->Iatacode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->NameOffice);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->IssueDate != null || $rec->IssueDate != '' ?  DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila + 1, $rec->TicketNumber.' ['.$rec->DocummentType.']->');
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(14, $fila + 1, trim($rec->AccountCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila + 1, $rec->AuthorizationCodeFP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila + 1, $rec->CurrencyTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila + 1, $rec->PaymentAmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila + 1, '');

                        $fila += 2;
                    }else{

                        if ($moneda_adm != $moneda_boa){
                            //$this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroupBOB_USD);
                            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroup);
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila, 'Administradora [ '.$moneda_adm.' ] contra Ticket o Factura [ '.$moneda_boa.' ]');
                        }else {
                            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroup);
                        }
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, ltrim($rec->TerminalNumber, '0').' / '.$rec->NameEstable.' ('.$rec->TypeEstable.')');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->computed);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->AuthorizationCode);
                        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->CreditCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(9, $fila, trim($rec->CreditCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $rec->Iatacode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $rec->NameOffice);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $rec->IssueDate != null || $rec->IssueDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $rec->TicketNumber.' ['.$rec->DocummentType.']');
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(14, $fila, trim($rec->AccountCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $rec->AuthorizationCodeFP);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, $rec->CurrencyTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, $rec->PaymentAmount);

                        if( $moneda_adm != $moneda_boa || number_format($rec->PaymentAmmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0){
                            if ($moneda_adm != $moneda_boa){
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroup);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila, 'Administradora [ '.$moneda_adm.' ] contra Ticket o Factura [ '.$moneda_boa.' ]');
                            }else {
                                //$this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->getFill()->getStartColor()->setRGB($color_cell[2]);
                                $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':T' . $fila)->applyFromArray($styleGroup);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila, '=H' . $fila . '-R' . $fila);
                                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila, 'Diferencia en los Montos');
                            }

                        }else{
                            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila, '=H'.$fila.'-R'.$fila);
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

                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, ltrim($rec->TerminalNumber,'0').' / '.$rec->NameEstable.' ('.$rec->TypeEstable.')');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->TerminalNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->LotNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->PaymentTicket);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y') : '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->computed);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->Currency);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec->PaymentAmmount);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->AuthorizationCode);
                        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->CreditCardNumber);
                        $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(9, $fila, trim($rec->CreditCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);


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

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->Iatacode);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->NameOffice);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->IssueDate != null || $rec->IssueDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y') : '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila + 1, $rec->TicketNumber.' ['.$rec->DocummentType.']');
                    $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(14, $fila + 1, trim($rec->AccountCardNumber), PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila + 1, $rec->AuthorizationCodeFP);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila + 1, $rec->CurrencyTicket);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila + 1, $rec->PaymentAmount);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila + 1, '');

                    $fila++;
                }

                $point_sale = $rec->Iatacode;
            }
        }
        /*************************************************************** OBSERVADAS ***************************************************************/
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