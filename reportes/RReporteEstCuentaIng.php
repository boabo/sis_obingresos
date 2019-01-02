<?php
class RReporteEstCuentaIng
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $monto_cre_ante=array();
    private $montoDebito=array();
    private $ajusteDebito=array();
    private $montoCredito=array();
    private $ajusteCredito=array();
    private $garantia=array();
    private $montoSB=array();
    private $montoSaldo=array();
    private $objParam;
    public  $url_archivo;
    public  $fill = 0;
    public  $filles = 0;
    public  $monto_anterior= 0;
    public  $creditos=array();
    public  $debitos = 0;
    public  $pika = 0;


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
  function datosHeader ($totales,$periodoAnterior) {
        $this->datos_titulo = $totales;
        $this->periodoAnterior = $periodoAnterior;

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
        $objDrawing->setHeight(100);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        //$this->docexcel->getActiveSheet()->mergeCells('A1:C1');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'REPORTE ESTADO DE CUENTAS POR PERIODO' );
        $this->docexcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:J2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'AGENCIA: '.$this->objParam->getParametro('nombre') );
        $this->docexcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:J3');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Desde:'.$this->objParam->getParametro('fecha_ini').'  '.'Hasta: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->mergeCells('A4:J4');
        $this->docexcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleTitulos);
        //$this->docexcel->getActiveSheet()->mergeCells('A4:H4');
        $this->docexcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A5:J5');

        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->docexcel->getActiveSheet()->setCellValue('A6','PERIODOS');
        $this->docexcel->getActiveSheet()->setCellValue('B6','TOTAL VENTAS');
        $this->docexcel->getActiveSheet()->setCellValue('C6','AJUSTE DEBITO');
        $this->docexcel->getActiveSheet()->setCellValue('D6','FECHA DE DEPÓSITO');
        $this->docexcel->getActiveSheet()->setCellValue('E6','NRO DE DEPÓSITO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','NRO DE DEPÓSITO BOA');
        $this->docexcel->getActiveSheet()->setCellValue('G6','IMPORTE DEPOSITADO');
        $this->docexcel->getActiveSheet()->setCellValue('H6','AJUSTE CREDITO');
        $this->docexcel->getActiveSheet()->setCellValue('I6','SALDO SIN BOLETA DE GARANTIA');
        $this->docexcel->getActiveSheet()->setCellValue('J6','OBSERVACIONES');
        $this->docexcel->getActiveSheet()->getStyle('A6:J6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:J6')->getAlignment()->setWrapText(true);


    }

    function generarDatos(){
        $this->imprimeCabecera();
        $bordes = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),


            ),

        );
        $styleTitulos = array(
            'alignment' => array(

            ),
        );

        $styleTitulos3 = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'F8CBAD'
                )
            ),

            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'calibri'
            ),
              );

              $fila = 8;
              $fill = 8;
              $numero = 1;
              $inicio = 8;
              $inicioDe= 8;
              $inicio2 = 8;
              $anterior = 7;
              $aux3 = 8;
              $ini2 = 8;
              $contador=1;
              $datos = $this->datos_titulo;
              $periodo = array();



              $periodoAnterior = $this->periodoAnterior;


                      $styleTitulos4 = array(
                          'fill' => array(
                              'type' => PHPExcel_Style_Fill::FILL_SOLID,
                              'color' => array(
                                  'rgb' => '00B050'
                              )
                          ),

                          'font'  => array(
                              'bold'  => true,
                              'size'  => 11,
                              'name'  => 'Calibri'
                          ),
                            );


                      $bordes = array(
                          'borders' => array(
                              'top' => array(
                                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                              ),


                          ),

                      );
                      $styleBoa4 = array(
                          'fill' => array(
                              'type' => PHPExcel_Style_Fill::FILL_SOLID,
                              'color' => array(
                                  'rgb' => '5B9BD5'
                              )

                          ),
                          'font'  => array(
                              'bold'  => true,
                              'size'  => 14,
                              'name'  => 'Times New Roman',
                              'color' => array(
                                  'rgb' => 'FFFFFF'
                              )


                          ),
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

                      $totalest = array(
                          'font'  => array(
                              'bold'  => true,
                              'size'  => 11,
                              'name'  => 'calibri',
                          ),
                          'fill' => array(
                              'type' => PHPExcel_Style_Fill::FILL_SOLID,
                              'color' => array(
                                  'rgb' => '9BC2E6'
                              )
                          ),
                      );

                      $diferencia = array(
                          'font'  => array(
                              'bold'  => true,
                              'size'  => 11,
                              'name'  => 'calibri',
                          ),
                          'fill' => array(
                              'type' => PHPExcel_Style_Fill::FILL_SOLID,
                              'color' => array(
                                  'rgb' => 'FFD966'
                              )
                          ),
                      );

                      $garantia = array(
                          'font'  => array(
                              'bold'  => true,
                              'size'  => 11,
                              'name'  => 'calibri',
                          ),
                          'fill' => array(
                              'type' => PHPExcel_Style_Fill::FILL_SOLID,
                              'color' => array(
                                  'rgb' => '00B0F0'
                              )
                          ),
                      );
                      $conBoleta = array(
                          'font'  => array(
                              'bold'  => true,
                              'size'  => 11,
                              'name'  => 'calibri',
                          ),
                          'fill' => array(
                              'type' => PHPExcel_Style_Fill::FILL_SOLID,
                              'color' => array(
                                  'rgb' => 'FFC000'
                              )
                          ),
                      );
                      $SinBoleta = array(
                          'font'  => array(
                              'bold'  => true,
                              'size'  => 12,
                              'name'  => 'calibri',
                          ),
                          'fill' => array(
                              'type' => PHPExcel_Style_Fill::FILL_SOLID,
                              'color' => array(
                                  'rgb' => 'ED7D31'
                              )
                          ),
                      );





                    if ($periodoAnterior != NULL) {
                    foreach ($periodoAnterior as $value3){
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO ANTERIOR');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $anterior, $value3['saldo_sin_boleto_ant']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $anterior, $value3['monto_debitos']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $anterior, $value3['ajuste_debito']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $anterior, $value3['monto_creditos']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $anterior, $value3['ajuste_credito']);
                    array_push($this->montoCredito, $value3['monto_creditos']);
                    array_push($this->montoDebito, $value3['monto_debitos']);
                    array_push($this->ajusteCredito, $value3['ajuste_credito']);
                    array_push($this->ajusteDebito, $value3['ajuste_debito']);
                    $this->docexcel->getActiveSheet()->getStyle("B$anterior:I$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                    $this->monto_anterior = $value3['saldo_sin_boleto_ant'];
                    //this->docexcel->getActiveSheet()->mergeCells("A$anterior:B$anterior");
                    //$this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
                    $this->docexcel->getActiveSheet()->getStyle("A$anterior:J$anterior" )->applyFromArray($styleTitulos3);
                  } } else {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'LA AGENCIA NO CUENTA CON UN SALDO ANTERIOR');
                      $this->docexcel->getActiveSheet()->mergeCells("A$anterior:J$anterior");                      
                      $this->docexcel->getActiveSheet()->getStyle("A$anterior:J$anterior" )->applyFromArray($styleTitulos3);
                    }



              foreach($datos as $value){
                  $valor=$value['fecha_ini'];
                   if(!in_array($valor, $periodo)){
                       $periodo[]=$valor;
                   }
                   //var_dump($datos);exit;
                 }

            foreach($periodo as $value1 ){
              foreach ($datos as $value){
               if ($value['fecha_ini'] == $value1) {

                 if ($value['tipo_credito']=='creditos') {
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_credito']);
                   array_push($this->montoCredito, $value['monto_credito']);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['ajuste_credito']);
                   array_push($this->ajusteCredito, $value['ajuste_credito']);
                   array_push($this->garantia, $value['garantia']);
                   $creditos = $value['monto_credito'];
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, date_format(date_create($value["fecha"]), 'd/m/Y'));
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila," ".$value['autorizacion__nro_deposito']);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila," ".$value['nro_deposito_boa']);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila," ".$value['tipo_agencia']);


                   $this->docexcel->getActiveSheet()->getStyle("G$fila:I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                  $fila++;
                 }

                 elseif ($value['tipo_debito']=='debitos') {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $inicio, $value['ajuste_debito']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $inicio, $value['monto_debito']);
                    $this->montoDebitos = $value['monto_debito'];
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $inicio, $value['periodo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $inicio, $value['tipo_agencia']);
                    $this->docexcel->getActiveSheet()->getStyle("B$inicio:C$inicio")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                    array_push($this->montoDebito, $value['monto_debito']);
                    array_push($this->ajusteDebito, $value['ajuste_debito']);


                 }

          }




      }


      $fila++;
      $inicio=$fila;
      $ultimo=$fila-1;



    //  $inicioDe=$inicio;
    //  $anterior++;

      //


    }

      for ($i=8; $i < $fila; $i++) {
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $i, "=SUM((I$anterior+G$i+H$i)-(C$i+B$i))");
        $this->docexcel->getActiveSheet()->getStyle("I$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        $anterior++;
      }



           $totales=$fila;

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $totales, 'TOTAL VENTAS');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales, 'TOTAL DEPOSITOS');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $totales,  array_sum($this->montoCredito) );
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $totales,  array_sum($this->montoDebito) );
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $totales,  array_sum($this->ajusteCredito) );
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totales,  array_sum($this->ajusteDebito) );
           $this->docexcel->getActiveSheet()->getStyle("A$totales:J$totales" )->applyFromArray($styleTitulos4);
           $this->docexcel->getActiveSheet()->getStyle("B$totales:C$totales")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("G$totales:H$totales")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


           $totales2= $fila+2;
           $totalde = $totales2+1;
           $totalaj = $totales2 + 2;
           $totalGaran = $totales2+4;
           $saldoSin = $totales2+5;
           $saldoCon = $totales2+6;

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totales2, 'TOTAL CREDITOS');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales2,  array_sum($this->montoCredito) );
           $this->docexcel->getActiveSheet()->getStyle("D$totales2")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("C$totales2:D$totales2" )->applyFromArray($totalest);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totales2+1, 'TOTAL DEBITOS');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales2+1,  array_sum($this->montoDebito) );
           $this->docexcel->getActiveSheet()->getStyle("D$totalde")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("C$totalde:D$totalde" )->applyFromArray($totalest);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales2, 'TOTAL AJUSTE CREDITO');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $totales2,  array_sum($this->ajusteCredito) );
           $this->docexcel->getActiveSheet()->getStyle("G$totales2")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("F$totales2:G$totales2" )->applyFromArray($totalest);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales2+1, 'TOTAL AJUSTE DEBITO');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $totales2+1,  array_sum($this->ajusteDebito) );
           $this->docexcel->getActiveSheet()->getStyle("G$totalde")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("F$totalde:G$totalde" )->applyFromArray($totalest);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales2+2, 'DIFERENCIA AJUSTES');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $totales2+2, (array_sum($this->ajusteCredito) - array_sum($this->ajusteDebito)));
           $this->docexcel->getActiveSheet()->getStyle("G$totalaj")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("F$totalaj:G$totalaj" )->applyFromArray($diferencia);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totales2+2, 'DIFERENCIA TOTALES');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales2+2, (array_sum($this->montoCredito) - array_sum($this->montoDebito)));
           $this->docexcel->getActiveSheet()->getStyle("D$totalaj")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("C$totalaj:D$totalaj" )->applyFromArray($diferencia);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales2+4, 'BOLETA DE GARANTIA');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales2+4, array_sum($this->garantia));
           $this->docexcel->getActiveSheet()->getStyle("F$totalGaran")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("D$totalGaran:F$totalGaran" )->applyFromArray($garantia);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales2+5, 'SALDO SIN BOLETA');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales2+5, (array_sum($this->montoCredito) - array_sum($this->montoDebito))+(array_sum($this->ajusteCredito) - array_sum($this->ajusteDebito)));
           $this->docexcel->getActiveSheet()->getStyle("F$saldoSin")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("D$saldoSin:F$saldoSin" )->applyFromArray($SinBoleta);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales2+6, 'SALDO CON BOLETA');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales2+6, ((array_sum($this->montoCredito) - array_sum($this->montoDebito))+(array_sum($this->ajusteCredito) - array_sum($this->ajusteDebito)))+array_sum($this->garantia));
           $this->docexcel->getActiveSheet()->getStyle("F$saldoCon")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           $this->docexcel->getActiveSheet()->getStyle("D$saldoCon:F$saldoCon" )->applyFromArray($conBoleta);





          }

          function generarReporte(){
              $this->generarDatos();
              $this->docexcel->setActiveSheetIndex(0);
              $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
              $this->objWriter->save($this->url_archivo);
          }

      }
      ?>
