<?php
class REstadoCuentaCorriente
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $aux=0;
    private $aux2=0;
    private $objParam;
    public  $url_archivo;
    public  $fila = 0;
    public  $filaAux = 0;
    public  $fnum =array();
    public  $fnumA =0;
    public  $array =array();
    public  $array2 =array();


    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
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
    function datosHeader ($totales,$resumen,$anteriorCierrePeriodo) {
        $this->datos_titulo = $totales;
        $this->resumen = $resumen;
        $this->anteriorCierrePeriodo = $anteriorCierrePeriodo;
    }
    function imprimeCabecera() {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('Cuenta Corriente');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '70AD47'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'E2EFDA'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $gdImage = imagecreatefromjpeg('../../../sis_obingresos/reportes/logoBoa.jpg');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(80);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        //$this->docexcel->getActiveSheet()->mergeCells('A1:C1');

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'ESTADO DE CUENTA DETALLADO' );
        $this->docexcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:J2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'AGENCIA: '.$this->datos_titulo[0]["nombre"] );
        $this->docexcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:J3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,4,'Desde:'.$this->objParam->getParametro('fecha_ini').'  '.'Hasta: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->mergeCells('D4:F4');

        $this->docexcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->setCellValue('A5','CREDITO');
        $this->docexcel->getActiveSheet()->mergeCells('A5:D5');
        $this->docexcel->getActiveSheet()->setCellValue('E5','DEBITO');
        $this->docexcel->getActiveSheet()->mergeCells('E5:I5');
        $this->docexcel->getActiveSheet()->setCellValue('J5','SALDO');
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);

        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->setCellValue('A6','Nro.');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Fecha Tran.');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Deposito');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Total');
        $this->docexcel->getActiveSheet()->setCellValue('E6','Fecha Tran.');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Cod. Reserva Boleto');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Neto');
        $this->docexcel->getActiveSheet()->setCellValue('H6','Tasa');
        $this->docexcel->getActiveSheet()->setCellValue('I6','Total');
        $this->docexcel->getActiveSheet()->setCellValue('J6','');
        $this->docexcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:J6')->applyFromArray($styleTitulos1);

    }

    function generarDatos(){
        $this->imprimeCabecera();
        $bordes = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),

        );
        $styleTitulos = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTitulos3 = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'F8CBAD'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            ),
              );
        $numero = 1;
        $fila = 8;
        $anterior = 7;
        $datos = $this->datos_titulo;


       //var_dump($datos);exit;
        foreach ($datos as $value){

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            if ( $value['tipo'] == 'credito') {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila,  date_format(date_create($value["fecha"]), 'd/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['autorizacion__nro_deposito']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['importe']);

            }else{
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, 'comision');
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['comision']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, date_format(date_create($value["fecha"]), 'd/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['autorizacion__nro_deposito']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['neto']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, round($value['importe']-$value['neto']));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe']);
            }
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['saldo']);
            $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("E$fila:F$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("D$fila:D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("G$fila:J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:J$fila")->applyFromArray($bordes);
            $numero++;
            $fila++;

            $this->fila =  $fila;
        }

        $anteriorCierrePeriodo = $this->anteriorCierrePeriodo;
        if ($anteriorCierrePeriodo != NULL) {
          foreach ($anteriorCierrePeriodo as $value3){
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO CIERRE PERIODO ANTERIOR');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $anterior, $value3['saldo_anterior']);
          $this->docexcel->getActiveSheet()->getStyle("I$anterior:J$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          $this->docexcel->getActiveSheet()->mergeCells("A$anterior:D$anterior");
          $this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
          $this->docexcel->getActiveSheet()->getStyle("A$anterior:J$anterior" )->applyFromArray($styleTitulos3);
        }
      }
        else {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'LA AGENCIA NO CUENTA CON UN SALDO CIERRE PERIODO ANTERIOR');
          $this->docexcel->getActiveSheet()->mergeCells("A$anterior:H$anterior");
          $this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
          $this->docexcel->getActiveSheet()->getStyle("A$anterior:J$anterior" )->applyFromArray($styleTitulos3);
        }


        $fill = $this->fila+3;
        $resumen = $this->resumen;
        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => '59A1EA'
                )
            )
        );
        $styleTitulosNumeros = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            )
        );

        $styleTitulosNumeros2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFD966'
                )
            )
        );

        $styleTitulosNumeros3 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'F4B084'
                )
            )
        );

        $styleTitulosNumeros4 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'A9D08E'
                )
            )
        );
        $styleTitulosNumeros5 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '8EA9DB'
                )
            )
        );
        $styleTitulosNumeros6 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '00B0F0'
                )
            )
        );

        $bordes = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),

        );
        $titulos = $fill - 2;
        $titulosub = $fill - 1;
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$titulos,'RESUMEN ESTADO CUENTA CORRIENTE' );
        $this->docexcel->getActiveSheet()->getStyle("B$titulos:J$titulos")->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells("B$titulos:J$titulos");
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$titulosub,'AGENCIA: '.$this->datos_titulo[0]["nombre"] );
        $this->docexcel->getActiveSheet()->getStyle("B$titulosub:J$titulosub")->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells("B$titulosub:J$titulosub");

        $filaAux = $fill +1;
        foreach ($resumen as $value){

            if($value['tipo'] == 'boleta_garantia' || $value['tipo'] == 'saldo_anterior'||
                $value['tipo'] == 'deposito'|| $value['tipo'] == 'comision'|| $value['tipo'] == 'otro_credito') {
                if($value['tipo'] == 'boleta_garantia'){
                    $valor = 'Boleta Garantia';
                }elseif ($value['tipo'] == 'saldo_anterior'){
                    $valor = 'Saldo Anterior';
                }elseif ($value['tipo'] == 'deposito'){
                    $valor = 'Depositos';
                }elseif ($value['tipo'] == 'comision'){
                    $valor = 'Comision';
                }elseif ( $value['tipo'] == 'otro_credito'){
                    $valor = 'Otros Creditos';
                }

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $filaAux, $valor);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $filaAux, $value['monto']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $filaAux, $value['moneda']);
                array_push($this->fnum,$value['monto']);
                $this->docexcel->getActiveSheet()->mergeCells("B$filaAux:D$filaAux");
                $this->docexcel->getActiveSheet()->getStyle("B$filaAux:I$filaAux")->applyFromArray($styleTitulosNumeros2);
                //$this->docexcel->getActiveSheet()->getStyle("F$filaAux:I$filaAux")->applyFromArray($styleTitulosNumeros2);
                $this->docexcel->getActiveSheet()->getStyle("B$filaAux:I$filaAux")->applyFromArray($bordes);
                $this->docexcel->getActiveSheet()->getStyle("F$filaAux:F$filaAux")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                $filaAux++;
                $this->fnumA = $filaAux;

            }
        }
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fill + 6 ,  'Total Creditos');
        $this->docexcel->getActiveSheet()->getStyle("B$this->fnumA:I$this->fnumA")->applyFromArray($styleTitulosNumeros4);
        $this->docexcel->getActiveSheet()->mergeCells("B$this->fnumA:D$this->fnumA");
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fill + 6 ,  array_sum($this->fnum));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fill + 6 ,  'BOB');
        $this->docexcel->getActiveSheet()->getStyle("F$this->fnumA:I$this->fnumA")->applyFromArray($styleTitulosNumeros);
        $this->docexcel->getActiveSheet()->getStyle("B$this->fnumA:I$this->fnumA")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("F$this->fnumA:F$this->fnumA")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


        $filaAuxL = $this->fnumA +1;
        foreach ($resumen as $value){

            if($value['tipo'] == 'boleto' || $value['tipo'] == 'periodo_adeudado'||
                $value['tipo'] == 'otro_debito') {

                if($value['tipo'] == 'boleto'){
                    $valor = 'Boleto';
                }elseif ($value['tipo'] == 'periodo_adeudado'){
                    $valor = 'Periodo Adeudado';
                }elseif ($value['tipo'] == 'otro_debito'){
                    $valor = 'Otro Debito';
                }
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $filaAuxL, $valor);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $filaAuxL, $value['monto']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $filaAuxL, $value['moneda']);
                array_push($this->array,$value['monto']);
                $this->docexcel->getActiveSheet()->mergeCells("B$filaAuxL:D$filaAuxL");
                $this->docexcel->getActiveSheet()->getStyle("B$filaAuxL:I$filaAuxL")->applyFromArray($styleTitulosNumeros3);
                $this->docexcel->getActiveSheet()->getStyle("B$filaAuxL:I$filaAuxL")->applyFromArray($bordes);
                $this->docexcel->getActiveSheet()->getStyle("F$filaAuxL:F$filaAuxL")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                $filaAuxL++;
                $this->aux = $filaAuxL;
            }
        }
        $estilo1=($fill+12);
        $estilo2=($fill+11);
        $estilo3=($fill+10);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fill + 10 , 'Total Debitos');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fill + 10 , array_sum($this->array));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fill + 10 , 'BOB');
        $this->docexcel->getActiveSheet()->getStyle("B$estilo3:I$estilo3")->applyFromArray($styleTitulosNumeros4);

        $resu=$fill+12;
        foreach ($resumen as $value){

                if($value['tipo'] == 'boleta_garantia'){
                    $valor = 'Boleta Garantia';


                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fill + 12 , 'Saldo sin Boleta');
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fill + 12 , (array_sum($this->fnum)-array_sum($this->array))-$value['monto']);          
                $this->docexcel->getActiveSheet()->getStyle("B$estilo1:I$estilo1")->applyFromArray($styleTitulosNumeros6);
                $this->docexcel->getActiveSheet()->getStyle("B$estilo1:I$estilo1")->applyFromArray($bordes);
                $this->docexcel->getActiveSheet()->getStyle("F$resu:F$resu")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                }

            }



        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fill + 11 , 'Saldo a la Fecha');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fill + 11 , array_sum($this->fnum)-array_sum($this->array) );
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fill + 11 , 'BOB');
        $this->docexcel->getActiveSheet()->getStyle("B$estilo2:I$estilo2")->applyFromArray($styleTitulosNumeros5);


        $this->docexcel->getActiveSheet()->mergeCells("B$this->aux:D$this->aux");
        $this->docexcel->getActiveSheet()->getStyle("B$this->aux:D$this->aux")->applyFromArray($styleTitulosNumeros);
        $this->docexcel->getActiveSheet()->getStyle("F$this->aux:I$this->aux")->applyFromArray($styleTitulosNumeros);
        $this->docexcel->getActiveSheet()->getStyle("B$this->aux:I$this->aux")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("F$this->aux:F$this->aux")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        $ayuda = $this->aux + 1;

        $this->docexcel->getActiveSheet()->mergeCells("B$ayuda:D$ayuda");
        $this->docexcel->getActiveSheet()->getStyle("B$ayuda:D$ayuda")->applyFromArray($styleTitulosNumeros);
        $this->docexcel->getActiveSheet()->getStyle("F$ayuda:I$ayuda")->applyFromArray($styleTitulosNumeros);
        $this->docexcel->getActiveSheet()->getStyle("B$ayuda:I$ayuda")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("F$ayuda:F$ayuda")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
