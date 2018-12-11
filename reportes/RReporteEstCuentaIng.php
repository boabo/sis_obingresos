<?php
class RReporteEstCuentaIng
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $monto=array();
    private $montoDebito=array();
    private $montoSB=array();
    private $montoSaldo=array();
    private $objParam;
    public  $url_archivo;
    public  $fill = 0;
    public  $filles = 0;
    public  $garante = 0;
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

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->setCellValue('A6','NRO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','PERIODOS');
        $this->docexcel->getActiveSheet()->setCellValue('C6','IMPORTE A PAGAR');
        $this->docexcel->getActiveSheet()->setCellValue('D6','FECHA DE PAGO S/G CALENDARIO BSP');
        $this->docexcel->getActiveSheet()->setCellValue('E6','IMPORTE DEPOSITADO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','FECHA DE DEPÓSITO');
        $this->docexcel->getActiveSheet()->setCellValue('G6','NRO DE DEPÓSITO');
        $this->docexcel->getActiveSheet()->setCellValue('H6','NRO DE DEPÓSITO BOA');
        $this->docexcel->getActiveSheet()->setCellValue('I6','OBSERVACIONES');
        $this->docexcel->getActiveSheet()->setCellValue('J6','SALDO SIN BOLETA DE GARANTIA');
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

              $fila = 8;
              $fill = 8;
              $numero = 1;
              $inicio = 8;
              $anterior = 7;
              $aux3 = 8;
              $ini2 = 8;
              $contador=1;
              $datos = $this->datos_titulo;
              $periodo = array();



              foreach($datos as $value){
                  $valor=$value['id_periodo_venta'];
                   if(!in_array($valor, $periodo)){
                       $periodo[]=$valor;
                   }
                   //var_dump($datos);exit;
                 }

                 foreach($periodo as $value1 ){

              foreach ($datos as $value){
               if ($value['id_periodo_venta'] == $value1) {
                  if($value['tipo'] == 'boletos'){
                    $this->docexcel->getActiveSheet()->getStyle("A$aux3:J$aux3")->applyFromArray($bordes);
                    $this->docexcel->getActiveSheet()->getStyle("A$aux3")->applyFromArray($styleTitulos);
                  if ($numero==1) {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $inicio, $numero);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $inicio, $value['periodo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $inicio, $value['monto_debito']);
                    array_push( $this->montoDebito,$value['monto_debito']);
                    $this->docexcel->getActiveSheet()->getStyle("C$inicio:C$inicio")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $inicio, $value['monto_sin_boleta']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $aux3,date_format(date_create($value["fecha_pago"]), 'd/m/Y'));
                    $this->docexcel->getActiveSheet()->getStyle("J$inicio:J$inicio")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                  }
                  else{
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $aux3, $numero);
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $aux3, $value['periodo']);
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $aux3, $value['monto_debito']);
                  $this->docexcel->getActiveSheet()->getStyle("C$aux3:C$aux3")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                  $this->docexcel->getActiveSheet()->getStyle("J$aux3:J$aux3")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $aux3, $value['monto_sin_boleta']);
                  array_push( $this->montoDebito,$value['monto_debito']);
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $aux3,date_format(date_create($value["fecha_pago"]), 'd/m/Y'));

                }
              } if($value['tipo'] != 'boletos'){
                          $this->garante = $value['garante'];
                        }

                if($value['tipo'] == 'deposito' ) {
                  if ($contador==1) {
                      //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $inicio, $contador);
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $ini2 , $value['monto_deposito']);
                      $this->docexcel->getActiveSheet()->getStyle("E$fill:E$fill")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                      array_push($this->monto, $value['monto_deposito']);
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $ini2 ,date_format(date_create($value["fecha"]), 'd/m/Y'));
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $ini2 , " ".$value['nro_deposito']);
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $ini2 , " ".$value['nro_deposito_boa']);
                      $this->docexcel->getActiveSheet()->getStyle("H$fill")->getAlignment()->setWrapText(true);
                      $this->docexcel->getActiveSheet()->getStyle("C$fill:C$fill")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                    }else {
                      //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fill, $contador);
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fill , $value['monto_deposito']);
                      $this->docexcel->getActiveSheet()->getStyle("E$fill:E$fill")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                      array_push($this->monto, $value['monto_deposito']);

                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fill ,date_format(date_create($value["fecha"]), 'd/m/Y'));
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fill ," ".$value['nro_deposito']);
                    //  $this->docexcel->getActiveSheet()->getStyle("G$fill:G$fill")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fill , " ".$value['nro_deposito_boa']);
                      $this->docexcel->getActiveSheet()->getStyle("H$fill")->getAlignment()->setWrapText(true);
                      $this->docexcel->getActiveSheet()->getStyle("C$fill:C$fill")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                    }
                      //$fill ++;

                  }
                  $contador++;
                  $fill ++;
                }

              }


              $numero++;
              $aux3=$fill;

            }

            $periodoAnterior = $this->periodoAnterior;


              $subtitulo = array(
                  'font'  => array(
                      'bold'  => true,
                      'size'  => 10,
                      'name'  => 'Arial',
                  )
              );
              $saldos = array(
                  'font'  => array(
                      'bold'  => true,
                      'size'  => 10,
                      'name'  => 'Arial',
                  ),
                  'fill' => array(
                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => array(
                          'rgb' => '27AE60'
                      )
                  ),
              );
              $deudas = array(
                  'font'  => array(
                      'bold'  => true,
                      'size'  => 10,
                      'name'  => 'Arial',
                  ),
                  'fill' => array(
                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => array(
                          'rgb' => 'CD6155'
                      )
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

                    $bordes = array(
                        'borders' => array(
                            'top' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            ),


                        ),

                    );
                    $styleBoa4 = array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        ),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => '5B9BD5'
                            )

                        ),
                        'font'  => array(
                            'bold'  => true,
                            'size'  => 16,
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

                    $saldo2 = array(
                        'font'  => array(
                            'bold'  => true,
                            'size'  => 10,
                            'name'  => 'Arial',
                        ),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => '8EA9DB'
                            )
                        ),
                    );
                    $resumen = array(
                        'font'  => array(
                            'bold'  => true,
                            'size'  => 10,
                            'name'  => 'Arial',
                        ),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => 'FFC000'
                            )
                        ),
                    );
                    $resumen2 = array(
                        'font'  => array(
                            'bold'  => true,
                            'size'  => 10,
                            'name'  => 'Arial',
                        ),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array(
                                'rgb' => 'F8CBAD'
                            )
                        ),
                    );



                  foreach ($periodoAnterior as $value3){
                  if ($value3['tipo'] == 'boletos') {
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO ANTERIOR');
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $anterior, $value3['monto_anterior']);
                  array_push($this->monto, $value3['monto_anterior']);
                  $this->docexcel->getActiveSheet()->getStyle("I$anterior:J$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                  $this->docexcel->getActiveSheet()->mergeCells("A$anterior:D$anterior");
                  $this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
                  $this->docexcel->getActiveSheet()->getStyle("A$anterior:J$anterior" )->applyFromArray($styleTitulos3);
                }  else {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'LA AGENCIA NO CUENTA CON UN SALDO A LA FECHA ANTERIOR');
                    $this->docexcel->getActiveSheet()->mergeCells("A$anterior:H$anterior");
                    $this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
                    $this->docexcel->getActiveSheet()->getStyle("A$anterior:J$anterior" )->applyFromArray($styleTitulos3);
                  }
              }


              $final=$aux3;

              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$final,'Total');
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$final,array_sum($this->montoDebito));
              $this->docexcel->getActiveSheet()->getStyle("A$final:J$final" )->applyFromArray($bordes);
              $this->docexcel->getActiveSheet()->getStyle("A$final:J$final" )->applyFromArray($styleBoa4);
              $this->docexcel->getActiveSheet()->getStyle("C$final:C$final")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,$final,'Total');
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$final,array_sum($this->monto));
              $this->docexcel->getActiveSheet()->getStyle("E$final:E$final")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

              if ($this->garante > 0) {
                  $aux = $final + 2;
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $final + 2, 'Boleta Garantia');
                  $this->docexcel->getActiveSheet()->getStyle("C$aux:C$aux")->applyFromArray($subtitulo);
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $final + 2, $this->garante);
                  $this->docexcel->getActiveSheet()->getStyle("C$aux:C$aux")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                  $this->docexcel->getActiveSheet()->getStyle("A$aux:C$aux" )->applyFromArray($resumen);

              }
              else{
                $aux6=$final + 2;
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $final + 2, 'LA AGENCIA NO CUENTA CON BOLETA DE GARANTIA');
                $this->docexcel->getActiveSheet()->getStyle("A$aux6:C$aux6" )->applyFromArray($resumen2);
              }

              $boole = $final+4;
              $this->docexcel->getActiveSheet()->getStyle("A$boole:D$boole" )->applyFromArray($subtitulo);
              $this->docexcel->getActiveSheet()->getStyle("C$boole:C$boole" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$final+3,'MONTO DE LA DEUDA');
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$final+3,array_sum($this->montoDebito));
              $this->docexcel->getActiveSheet()->getStyle("A$boole:C$boole" )->applyFromArray($resumen);



              $bool = $final+3;
              $this->docexcel->getActiveSheet()->getStyle("A$bool:B$bool" )->applyFromArray($subtitulo);
              $this->docexcel->getActiveSheet()->getStyle("C$bool:C$bool" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$final+4,'MONTO PAGADO');
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$final+4,array_sum($this->monto)+ $this->garante);
              $this->docexcel->getActiveSheet()->getStyle("A$bool:C$bool" )->applyFromArray($resumen);




              $saldo = array_sum($this->monto) + $this->garante - array_sum($this->montoDebito);
              if($saldo > 0 ){
                  $cap = $final + 5;
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$final+5,'IMPORTE  A FAVOTR  AGT');
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$final+5,$saldo);
                  $this->docexcel->getActiveSheet()->getStyle("C$cap:C$cap")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                  $saldo = $final+5;
                  $this->docexcel->getActiveSheet()->getStyle("A$saldo:C$saldo" )->applyFromArray($saldos);

              }else{
                  $cap = $final + 5;
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$final+5,'DEUDA  AGT');
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$final+5,$saldo);
                  $this->docexcel->getActiveSheet()->getStyle("C$cap:C$cap")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                  $deuda = $final+5;
                  $this->docexcel->getActiveSheet()->getStyle("A$deuda:C$deuda" )->applyFromArray($deudas);

              }
              $sb=$final+6;
              $cb=$final+7;
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$final+6,'SALDO SIN BOLETA DE GARANTIA');
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$final+7,'SALDO CON BOLETA DE GARANTIA');
              foreach ($periodoAnterior as $value4){
              if ($value4['tipo'] == 'SinBoleta') {

              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $final+6, $value4['monto_anterior']);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $final+7, $this->garante + $value4['monto_anterior']);
              //array_push($this->monto, $value3['monto_anterior']);
              $this->docexcel->getActiveSheet()->getStyle("I$anterior:J$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

              $this->docexcel->getActiveSheet()->mergeCells("A$anterior:D$anterior");
              $this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
              $this->docexcel->getActiveSheet()->getStyle("A$anterior:J$anterior" )->applyFromArray($styleTitulos3);

            }

          }

              $this->docexcel->getActiveSheet()->getStyle("C$sb:C$sb")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
              $this->docexcel->getActiveSheet()->getStyle("A$sb:C$sb" )->applyFromArray($saldo2);
              $this->docexcel->getActiveSheet()->getStyle("C$cb:C$cb")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
              $this->docexcel->getActiveSheet()->getStyle("A$cb:C$cb" )->applyFromArray($saldo2);







          }

          function generarReporte(){
              $this->generarDatos();
              $this->docexcel->setActiveSheetIndex(0);
              $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
              $this->objWriter->save($this->url_archivo);
          }

      }
      ?>
