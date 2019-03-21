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
    public  $garantia =0;
    public  $array =array();
    public  $array2 =array();
    public  $sinboleta =array();
    public  $sb2 =array();
    public  $saldoanterior =array();
    public  $boletaGarantia =array();
    public  $depositosTotal =array();
    public  $comision =array();
    public  $boletos =array();


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
        $this->docexcel->getActiveSheet()->getStyle('A2:M2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:M2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'AGENCIA: '.$this->objParam->getParametro('nombre') );
        $this->docexcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:M3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Desde:'.$this->objParam->getParametro('fecha_ini').'  '.'Hasta: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->mergeCells('A4:M4');

        $this->docexcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A4:M4')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->setCellValue('A5','CREDITO');
        $this->docexcel->getActiveSheet()->mergeCells('A5:D5');
        $this->docexcel->getActiveSheet()->setCellValue('E5','COMISION');
        $this->docexcel->getActiveSheet()->mergeCells('E5:G5');
        $this->docexcel->getActiveSheet()->setCellValue('H5','DEBITO');
        $this->docexcel->getActiveSheet()->mergeCells('H5:L5');
        $this->docexcel->getActiveSheet()->setCellValue('M5','SALDO');
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);



        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

        $this->docexcel->getActiveSheet()->setCellValue('A6','Nro.');
        $this->docexcel->getActiveSheet()->setCellValue('E6','Fecha Tran.');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Nro. Deposito.');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Total');
        //$this->docexcel->getActiveSheet()->mergeCells('D6:E6');



        $this->docexcel->getActiveSheet()->setCellValue('B6','Fecha Tran.');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Deposito');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Total');

        $this->docexcel->getActiveSheet()->setCellValue('H6','Fecha Tran.');
        $this->docexcel->getActiveSheet()->setCellValue('I6','Cod. Reserva Boleto');
        $this->docexcel->getActiveSheet()->setCellValue('J6','Neto');
        $this->docexcel->getActiveSheet()->setCellValue('K6','Tasa');
        $this->docexcel->getActiveSheet()->setCellValue('L6','Total');

        $this->docexcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:M6')->applyFromArray($styleTitulos1);

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
        $saldo_anterior = 7;
        $prueba = 7;

        $datos = $this->datos_titulo;


       //var_dump($datos);exit;

        foreach ($datos as $value){

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            if ( $value['tipo_credito'] == 'credito') {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila,  date_format(date_create($value["fecha"]), 'd/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['autorizacion__nro_deposito']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['importe']);
                $this->garantia = $value['garantia'];
                //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['tipo_credito']);
                array_push($this->depositosTotal,$value['importe']);

            }elseif ($value['tipo'] == 'ajustes') {
              /*--------------------------------------------comision-------------------------------------------------------------*/
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, 'comision');
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['comision']);
                array_push($this->comision,$value['comision']);

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, date_format(date_create($value["fecha"]), 'd/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, date_format(date_create($value["fecha"]), 'd/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['autorizacion__nro_deposito']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, 0);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, 0);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['importe']);
                array_push($this->boletos,$value['importe']);
           }elseif ($value['tipo'] == 'debito') {
             /*--------------------------------------------comision-------------------------------------------------------------*/
               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, 'comision');
               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['comision']);
               array_push($this->comision,$value['comision']);

               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, date_format(date_create($value["fecha"]), 'd/m/Y'));
               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, date_format(date_create($value["fecha"]), 'd/m/Y'));
               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['autorizacion__nro_deposito']);
               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['neto']);
               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, round($value['importe']-$value['neto']));
               $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['importe']);
               array_push($this->boletos,$value['importe']);
          }
            /*-------------------------------------------------------------------------------------------------------------------------*/
            if ($this->objParam->getParametro('tipo')=='corporativa' && $this->objParam->getParametro('tipo_pago')=='prepago' ) {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM(M$saldo_anterior+D$fila-L$fila)");
            } else {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((M$saldo_anterior-L$fila)+(D$fila+G$fila))");

            }

            $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("E$fila:F$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("H$fila:I$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("D$fila:D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("J$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("G$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("M$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:M$fila")->applyFromArray($bordes);






            $saldo_anterior++;
            $numero++;
            $fila++;

            $this->fila =  $fila;
        }


        $anteriorCierrePeriodo = $this->anteriorCierrePeriodo;
        if ($anteriorCierrePeriodo != NULL) {
          foreach ($anteriorCierrePeriodo as $value3){
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO ANTERIOR');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $anterior, $value3['saldo_sin_boleto_ant']);
          $this->sal_anterior = $value['saldo_sin_boleto_ant'];
          $this->docexcel->getActiveSheet()->getStyle("M$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          $this->docexcel->getActiveSheet()->mergeCells("A$anterior:D$anterior");
          $this->docexcel->getActiveSheet()->getStyle("A$anterior:M$anterior" )->applyFromArray($styleTitulos3);
        }
      }

        else {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'LA AGENCIA NO CUENTA CON UN SALDO CIERRE PERIODO ANTERIOR');
          $this->docexcel->getActiveSheet()->mergeCells("A$anterior:H$anterior");
          $this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
          $this->docexcel->getActiveSheet()->getStyle("A$anterior:M$anterior" )->applyFromArray($styleTitulos3);
        }
        array_push($this->saldoanterior,$value3['saldo_sin_boleto_ant']);


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
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFD966'
                )
            )
        );


        $styleTitulosNumeros22 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '5B9BD5'
                )
            )
        );

        $styleTitulosNumeros23 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'BDD7EE'
                )
            )
        );

        $styleTitulosNumeros3 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'
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
                'name'  => 'Calibri'
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
                'size'  => 12,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '00B0F0'
                )
            )
        );

        $styleTitulosNumeros7 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'ED7D31'
                )
            )
        );
        $styleTitulosNumeros8 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '70AD47'
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
        $bordes2 = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),

                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),
            ),

        );
        $bordes3 = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                )

            ),

        );
        $bordes4 = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                ),

            ),

        );
        $titulos = $fila + 1;
        $titulosub = $titulos + 1;
        $fechas = $titulosub + 1;
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$titulos,'RESUMEN ESTADO CUENTA CORRIENTE' );
        $this->docexcel->getActiveSheet()->getStyle("E$titulos:I$titulos")->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells("E$titulos:I$titulos");
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$titulosub,'AGENCIA: '.$this->objParam->getParametro('nombre')  );
        $this->docexcel->getActiveSheet()->getStyle("E$titulosub:I$titulosub")->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells("E$titulosub:I$titulosub");
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$fechas,'Desde:'.$this->objParam->getParametro('fecha_ini').'  '.'Hasta: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->mergeCells("E$fechas:I$fechas");
        $this->docexcel->getActiveSheet()->getStyle("E$fechas:I$fechas")->applyFromArray($styleTitulos);

        $boleta = $fechas + 2;
        $depositos = $boleta + 1;

        $comisiones = $depositos + 1;
        $totalCreditos = $comisiones+1;
        $saldo_antes  =  $totalCreditos + 1;
        $boleto = $saldo_antes +1 ;
        $totalDebitos = $boleto+1;
        $salBoleta = $totalDebitos + 1;
        $salSinBoleta = $salBoleta + 1;
        $sinComision = $salSinBoleta + 1;



        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $boleta, 'Boleta de Garantia');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $boleta, $this->garantia);
        $this->docexcel->getActiveSheet()->getStyle("E$boleta:I$boleta")->applyFromArray($styleTitulosNumeros22);
        $this->docexcel->getActiveSheet()->getStyle("E$boleta:I$boleta")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$boleta:I$boleta")->applyFromArray($bordes2);
        $this->docexcel->getActiveSheet()->getStyle("E$boleta:I$boleta")->applyFromArray($bordes3);
        $this->docexcel->getActiveSheet()->getStyle("I$boleta:I$boleta")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $depositos, 'Depositos');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $depositos ,  array_sum($this->depositosTotal));
        $this->docexcel->getActiveSheet()->getStyle("I$depositos:I$depositos")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("E$depositos:I$depositos")->applyFromArray($styleTitulosNumeros2);
        $this->docexcel->getActiveSheet()->getStyle("E$depositos:I$depositos")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$depositos:I$depositos")->applyFromArray($bordes2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $comisiones, 'Comision');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $comisiones , array_sum($this->comision));
        $this->docexcel->getActiveSheet()->getStyle("E$comisiones:I$comisiones")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$comisiones:I$comisiones")->applyFromArray($bordes4);
        $this->docexcel->getActiveSheet()->getStyle("E$comisiones:I$comisiones")->applyFromArray($styleTitulosNumeros2);
        $this->docexcel->getActiveSheet()->getStyle("I$comisiones:I$comisiones")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $totalCreditos ,  'Total Creditos');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $totalCreditos ,  "=SUM((I$depositos+I$comisiones))");
        $this->docexcel->getActiveSheet()->getStyle("I$totalCreditos:I$totalCreditos")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("E$totalCreditos:I$totalCreditos")->applyFromArray($styleTitulosNumeros4);
        $this->docexcel->getActiveSheet()->getStyle("E$totalCreditos:I$totalCreditos")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$totalCreditos:I$totalCreditos")->applyFromArray($bordes3);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $saldo_antes , 'Saldo Anterior');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $saldo_antes , array_sum($this->saldoanterior));
        $this->docexcel->getActiveSheet()->getStyle("I$saldo_antes:I$saldo_antes")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("E$saldo_antes:I$saldo_antes")->applyFromArray($styleTitulosNumeros3);
        $this->docexcel->getActiveSheet()->getStyle("E$saldo_antes:I$saldo_antes")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$saldo_antes:I$saldo_antes")->applyFromArray($bordes3);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $boleto ,  'Boleto');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $boleto , (array_sum($this->boletos))*(-1));
        $this->docexcel->getActiveSheet()->getStyle("I$boleto:I$boleto")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("E$boleto:I$boleto")->applyFromArray($styleTitulosNumeros3);
        $this->docexcel->getActiveSheet()->getStyle("E$boleto:I$boleto")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$boleto:I$boleto")->applyFromArray($bordes2);
        $this->docexcel->getActiveSheet()->getStyle("E$boleto:I$boleto")->applyFromArray($bordes3);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $totalDebitos ,  'Total Debitos');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $totalDebitos ,"=SUM((I$boleto+I$saldo_antes))");
        $this->docexcel->getActiveSheet()->getStyle("I$totalDebitos:I$totalDebitos")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("E$totalDebitos:I$totalDebitos")->applyFromArray($styleTitulosNumeros4);
        $this->docexcel->getActiveSheet()->getStyle("E$totalDebitos:I$totalDebitos")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$totalDebitos:I$totalDebitos")->applyFromArray($bordes3);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $salBoleta , 'Saldo Con Boleta');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $salBoleta, "=SUM((I$boleta+I$totalCreditos+I$totalDebitos))");
        $this->docexcel->getActiveSheet()->getStyle("E$salBoleta:I$salBoleta")->applyFromArray($styleTitulosNumeros7);
        $this->docexcel->getActiveSheet()->getStyle("E$salBoleta:I$salBoleta")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$salBoleta:I$salBoleta")->applyFromArray($bordes2);
        $this->docexcel->getActiveSheet()->getStyle("E$salBoleta:I$salBoleta")->applyFromArray($bordes3);
        $this->docexcel->getActiveSheet()->getStyle("I$salBoleta:I$salBoleta")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $salSinBoleta , 'Saldo Sin Boleta');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $salSinBoleta , "=SUM((I$totalCreditos+I$totalDebitos))");
        $this->docexcel->getActiveSheet()->getStyle("E$salSinBoleta:I$salSinBoleta")->applyFromArray($styleTitulosNumeros6);
        $this->docexcel->getActiveSheet()->getStyle("E$salSinBoleta:I$salSinBoleta")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$salSinBoleta:I$salSinBoleta")->applyFromArray($bordes3);
        $this->docexcel->getActiveSheet()->getStyle("I$salSinBoleta:I$salSinBoleta")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


        if ($this->objParam->getParametro('tipo')=='corporativa' && $this->objParam->getParametro('tipo_pago')=='prepago' ) {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $sinComision , 'Saldo Sin Comision');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $sinComision , "=SUM(I$saldo_antes+I$depositos+I$boleto)");
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($styleTitulosNumeros8);
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($bordes3);
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($bordes2);
          $this->docexcel->getActiveSheet()->getStyle("I$sinComision:I$sinComision")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        }else{
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $sinComision , 'Saldo Sin Comision');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $sinComision , "=SUM((I$depositos+I$saldo_antes)+(I$comisiones+I$boleto))"); ;
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($styleTitulosNumeros8);
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($bordes3);
          $this->docexcel->getActiveSheet()->getStyle("E$sinComision:I$sinComision")->applyFromArray($bordes2);
          $this->docexcel->getActiveSheet()->getStyle("I$sinComision:I$sinComision")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        }


    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
