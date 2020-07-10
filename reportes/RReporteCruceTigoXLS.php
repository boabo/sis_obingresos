<?php

class RReporteCruceTigoXLS{
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
        $this->docexcel->getProperties()->setCreator("BoA")
            ->setLastModifiedBy("BoA")
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
        $depositos = $this->objParam->getParametro('depositos');//print_r($depositos);exit;

        $fecha_desde = $this->objParam->getParametro('fecha_desde');
        $fecha_hasta = $this->objParam->getParametro('fecha_hasta');

        $numberFormat = '#,##0.00';

        $index = 0;
        /*PAGOS QUE ESTAN EN ATC Y RET*/

        $this->addHoja('PAGOS TIGO<->BOLETOS',$index);

        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);



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

        $this->docexcel->getActiveSheet()->getStyle('A1:N4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:N2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:M2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','REPORTE DE VENTAS BANCA ELECTRONICA');

        $this->docexcel->getActiveSheet()->getStyle('A3:M4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:M3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:M4');
        $this->docexcel->getActiveSheet()->setCellValue('A4','Medio Pago: Tigo Money');

        $this->docexcel->getActiveSheet()->setCellValue('N1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('N2', date('d/m/Y'));

        $this->docexcel->getActiveSheet()->getStyle('A5:N6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:G5');
        $this->docexcel->getActiveSheet()->setCellValue('A5','PAGOS TIGO');


        $this->docexcel->getActiveSheet()->setCellValue('A6','FECHA');
        $this->docexcel->getActiveSheet()->setCellValue('B6','PNR');
        $this->docexcel->getActiveSheet()->setCellValue('C6','CODIGO');
        $this->docexcel->getActiveSheet()->setCellValue('D6','NRO. ORDEN');
        $this->docexcel->getActiveSheet()->setCellValue('E6','LINEA');
        $this->docexcel->getActiveSheet()->setCellValue('F6','COD. VERIFICACIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('G6','MONTO EFECTIVO');


        $this->docexcel->getActiveSheet()->mergeCells('H5:M5');
        $this->docexcel->getActiveSheet()->setCellValue('H5','PAGOS (TICKETS)');

        $this->docexcel->getActiveSheet()->setCellValue('H6','FECHA');
        $this->docexcel->getActiveSheet()->setCellValue('I6','NRO. TICKET');
        $this->docexcel->getActiveSheet()->setCellValue('J6','OFICINA RESERVA');
        $this->docexcel->getActiveSheet()->setCellValue('K6','OFICINA PAGO');
        $this->docexcel->getActiveSheet()->setCellValue('L6','CODIGO (PNR)');
        $this->docexcel->getActiveSheet()->setCellValue('M6','MONTO TICKET');
        $this->docexcel->getActiveSheet()->setCellValue('N6','DIFERENCIA');



        $fila = 7;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');

        $monto_pagado = 0;
        $fila_total = $fila;
        $flag_left = true;
        $index_color = 0;
        $depo_date = array();
        foreach ($datos as $key => $rec){
            if(null != $rec->TransactionDate){
                $d_key = DateTime::createFromFormat('Y-m-d', $rec->TransactionDate)->format('dmY');
                $depo_date[$d_key] += $rec->TransactionAmount;
            }
            $styleGroup = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => $color_cell[$index_color]
                    )
                )
            );


            $monto_pagado = $monto_pagado + $rec->PaymentAmount;

            if( $rec->AuthorizationCodeFP != $datos[$key+1]->AuthorizationCodeFP) {

                if (!$flag_left){
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila_total.':N'.($fila + 1))->applyFromArray($styleGroup);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila_total, '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila_total, '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila_total, '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila_total, '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila_total, '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila_total, $monto_pagado);



                    if( null == $rec->TransactionAmount || '' == $rec->TransactionAmount){
                        $rec->TransactionAmount = 0;
                    }

                    if(number_format($rec->TransactionAmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0){
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila_total.':N'.$fila_total)->getFill()->getStartColor()->setRGB($color_cell[2]);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila_total, '=G'.$fila_total.'-M'.$fila_total);
                    }else{
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila_total, '=G'.$fila_total.'-M'.$fila_total);
                    }

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila + 1, DateTime::createFromFormat('Y-m-d', $rec->IssueDate)->format('d/m/Y'));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila + 1, $rec->TicketNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila + 1, $rec->OIDReservation);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->OIDIssue);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->AuthorizationCodeFP);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->PaymentAmount);

                    $fila += 2;
                }else{
                    $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->applyFromArray($styleGroup);

                    if(null == $rec->Pnr && null == $rec->Status){
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, '');
                    }else{
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, DateTime::createFromFormat('Y-m-d', $rec->TransactionDate)->format('d/m/Y'));
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->Pnr);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->TransactionCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->OrderNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->LineNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->TransactionAmount);
                    }

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, DateTime::createFromFormat('Y-m-d', $rec->IssueDate)->format('d/m/Y'));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec->TicketNumber);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec->OIDReservation);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $rec->OIDIssue);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $rec->AuthorizationCodeFP);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $rec->PaymentAmount);


                    if( null == $rec->TransactionAmount || '' == $rec->TransactionAmount){
                        $rec->TransactionAmount = 0;
                    }

                    if(number_format($rec->TransactionAmount, 2, ',', '')-number_format($monto_pagado, 2, ',', '') != 0){
                        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFill()->getStartColor()->setRGB($color_cell[2]);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, '=G'.$fila.'-M'.$fila);
                    }else{
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, '=G'.$fila.'-M'.$fila);
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
                    if (null != $rec->TransactionDate) {
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, DateTime::createFromFormat('Y-m-d', $rec->TransactionDate)->format('d/m/Y'));
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->Pnr);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->TransactionCode);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->OrderNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->LineNumber);
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->TransactionAmount);
                    }else{
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, '');
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, '');
                    }

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
                }
                
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila + 1, DateTime::createFromFormat('Y-m-d', $rec->IssueDate)->format('d/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila + 1, $rec->TicketNumber);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila + 1, $rec->OIDReservation);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila + 1, $rec->OIDIssue);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila + 1, $rec->AuthorizationCodeFP);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila + 1, $rec->PaymentAmount);

                $fila++;
            }
        }

        $index ++;

        $this->addHoja('CONCILACIÓN',$index);

        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

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

        $this->docexcel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:G2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:F2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','REPORTE DE VENTAS (CONCILIACIÓN) VENTAS<->DEPOSITOS');

        $this->docexcel->getActiveSheet()->getStyle('A3:F4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:F3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:F4');
        $this->docexcel->getActiveSheet()->setCellValue('A4','Medio Pago: Tigo Money');

        $this->docexcel->getActiveSheet()->setCellValue('G1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('G2', date('d/m/Y'));

        $this->docexcel->getActiveSheet()->mergeCells('A5:B6');
        $this->docexcel->getActiveSheet()->mergeCells('C5:D6');
        $this->docexcel->getActiveSheet()->mergeCells('E5:F6');
        $this->docexcel->getActiveSheet()->mergeCells('G5:G6');
        $this->docexcel->getActiveSheet()->getStyle('A5:G6')->applyFromArray($styleTitulos1);

        $this->docexcel->getActiveSheet()->mergeCells('A6:B6');
        $this->docexcel->getActiveSheet()->setCellValue('A5','FECHA');
        $this->docexcel->getActiveSheet()->mergeCells('C6:D6');
        $this->docexcel->getActiveSheet()->setCellValue('C5','INGRESO VENTAS');
        $this->docexcel->getActiveSheet()->mergeCells('E6:F6');
        $this->docexcel->getActiveSheet()->setCellValue('E5','INGRESO DEPOSITOS');
        $this->docexcel->getActiveSheet()->setCellValue('G5','DIFERENCIA');
        $fila = 7;
        foreach ($depo_date as $key => $depos){
            $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':B'.$fila)->applyFromArray($styleTitulos1);
            $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':B'.$fila);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, DateTime::createFromFormat('dmY', $key)->format('d/m/Y'));
            $this->docexcel->getActiveSheet()->mergeCells('C'.$fila.':D'.$fila);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $depo_date[$key]);
            $this->docexcel->getActiveSheet()->mergeCells('E'.$fila.':F'.$fila);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila,$depositos[$key] );
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila,$depo_date[$key]-$depositos[$key] );
            $fila++;
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