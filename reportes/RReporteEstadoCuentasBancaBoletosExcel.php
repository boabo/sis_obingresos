<?php
class RReporteEstadoCuentasBancaBoletosExcel
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $monto=array();
    private $montoDebito=array();
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

    function imprimeCabecera() {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('Estado de Cuentas');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D6E8F2'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTitulos1 = array(
            'font'  => array(
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D6E8F2'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTituloAgencia = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 20,
                'name'  => 'Calibri'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleDatoAgencia = array(
            'font'  => array(
                'bold'  => false,
                'size'  => 20,
                'name'  => 'Calibri'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );


        $styleTitulos2 = array(
            'font'  => array(
              'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '9BCEEA'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
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

        $styleCentro = array(
          'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleDerecha = array(
          'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $gdImage = imagecreatefromjpeg('../../../sis_obingresos/reportes/logoBancaElectronica.jpg');
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
        //titulos
        /*Ponemos negrita a los subtitulos*/
        $desde_negrita = new PHPExcel_RichText();
        $texto_negrita_desde = $desde_negrita->createTextRun('Desde: ');
        $texto_negrita_desde->getFont()->setBold(true);
        $texto_negrita_desde->getFont()->setSize('12');
        $desde_negrita->createText($this->objParam->getParametro('fecha_ini'));

        $hasta_negrita = new PHPExcel_RichText();
        $texto_negrita_hasta = $hasta_negrita->createTextRun('Hasta: ');
        $texto_negrita_hasta->getFont()->setBold(true);
        $hasta_negrita->createText($this->objParam->getParametro('fecha_fin'));

        $generacion_negrita = new PHPExcel_RichText();
        $texto_negrita_generacion = $generacion_negrita->createTextRun('Fecha Generación Reporte: ');
        $texto_negrita_generacion->getFont()->setBold(true);
        $generacion_negrita->createText(date("d/m/Y H:i:s"));

        $tipo_negrita = new PHPExcel_RichText();
        $texto_negrita_tipo = $tipo_negrita->createTextRun('Tipo Agencia: ');
        $texto_negrita_tipo->getFont()->setBold(true);
        $tipo_negrita->createText($this->objParam->getParametro('tipo_agencia'));

        $generado_negrita = new PHPExcel_RichText();
        $texto_negrita_generado = $generado_negrita->createTextRun('Generado por: ');
        $texto_negrita_generado->getFont()->setBold(true);
        $generado_negrita->createText($_SESSION['_LOGIN']);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,1,'REPORTE ESTADO DE CUENTAS' );
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,2,$desde_negrita);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,2,$hasta_negrita);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,$generacion_negrita);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,$generado_negrita);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,5,'AGENCIA: ');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,5,$this->objParam->getParametro('nombre'));


        $this->docexcel->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A2:O2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A3:O3')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A4:O4')->applyFromArray($styleTitulos1);
        //$this->docexcel->getActiveSheet()->getStyle('A5:Q5')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A5:O5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('H5')->applyFromArray($styleTituloAgencia);
        $this->docexcel->getActiveSheet()->getStyle('I5:M5')->applyFromArray($styleDatoAgencia);

        $this->docexcel->getActiveSheet()->getStyle('K2:L2')->applyFromArray($styleCentro);
        $this->docexcel->getActiveSheet()->getStyle('G2:H2')->applyFromArray($styleDerecha);

        $this->docexcel->getActiveSheet()->mergeCells('A1:O1');
        // $this->docexcel->getActiveSheet()->mergeCells('H2:I2');
        // $this->docexcel->getActiveSheet()->mergeCells('K2:L2');
        $this->docexcel->getActiveSheet()->mergeCells('A3:O3');
        $this->docexcel->getActiveSheet()->mergeCells('A4:O4');
        $this->docexcel->getActiveSheet()->mergeCells('I5:M5');
        //$this->docexcel->getActiveSheet()->mergeCells('A5:O5');



        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(9);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(46);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(16);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);

        $this->docexcel->getActiveSheet()->setCellValue('A6','NRO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','TRANSACCION ID');
        $this->docexcel->getActiveSheet()->setCellValue('C6','PNR');
        $this->docexcel->getActiveSheet()->setCellValue('D6','TKT');
        $this->docexcel->getActiveSheet()->setCellValue('E6','NETO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','TASAS');
        $this->docexcel->getActiveSheet()->setCellValue('G6','MONTO TOTAL');
        $this->docexcel->getActiveSheet()->setCellValue('H6','COMISIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('I6','MONEDA');
        $this->docexcel->getActiveSheet()->setCellValue('J6','FECHA EMISIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('K6','FECHA TRANSACCIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('L6','FECHA PAGO BANCO');
        $this->docexcel->getActiveSheet()->setCellValue('M6','FORMA PAGO');
        $this->docexcel->getActiveSheet()->setCellValue('N6','ENTIDAD PAGO');
        $this->docexcel->getActiveSheet()->setCellValue('O6','ESTADO');

        $this->docexcel->getActiveSheet()->getStyle('B6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('D6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('F6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('H6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('I6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('J6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('K6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('L6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('M6')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->getStyle('A6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('B6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('C6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('D6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('E6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('F6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('G6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('H6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('I6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('J6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('K6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('L6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('M6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('N6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('O6')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('A6:O6')->applyFromArray($styleTitulos2);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);



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


        $styleContenido = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'EFF7FC'
              )
          ),
    		);

        $styleTotales = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '86F0B9'
              )
          ),
    		);

        $stylebordes_blancos = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFFFFF'
              )
          ),
    		);

        $styleNegrita = array(
            'font'  => array(
                'bold'  => true
            )
        );

        $styleDerecha = array(
          'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $fila = 7;
    		$numero = 1;
    		$aux = 7;
        $totales=array();
    		$total = 7;
    		$pago= 11;
    		$estacion=array();
        $datos = $this->objParam->getParametro('datos');

        foreach($datos as $value){
    				 if(!in_array($valor, $estacion)){
    						 $estacion[]=$valor;
    				 }

    		}

        foreach ($datos as $value) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($styleNegrita);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['transaccion_id']);
            $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['pnr']);
            $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value["tkt"]);
            $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value["neto"]);
            $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['tasas']);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_total']);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['comision']);
            $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['moneda']);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, date("d/m/Y", strtotime($value['fecha_emision'])));
            $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, date("d/m/Y H:i:s", strtotime($value['fecha_transaccion'])));
            $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, date("d/m/Y ", strtotime($value['fecha_pago_banco'])));
            $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['forma_pago']);
            $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['entidad_pago']);
            $this->docexcel->getActiveSheet()->getStyle("N$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estado']);
            $this->docexcel->getActiveSheet()->getStyle("O$fila")->applyFromArray($bordes);

            $this->docexcel->getActiveSheet()->getStyle("A$fila:O$fila")->applyFromArray($styleContenido);
            $this->docexcel->getActiveSheet()->getStyle("E$fila:H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER);

            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, date("d/m/Y", strtotime($value["fecha_pago_banco"])));


            $numero++;
    				$fila++;
    				$total++;
    			}
          /*Mostrando los Totales*/
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, 'Totales:');
          $this->docexcel->getActiveSheet()->mergeCells("B$fila:D$fila");
          $this->docexcel->getActiveSheet()->getStyle("B$fila:D$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("B$fila:H$fila")->applyFromArray($styleNegrita);
          $this->docexcel->getActiveSheet()->getStyle("B$fila:H$fila")->applyFromArray($styleTotales);
          $this->docexcel->getActiveSheet()->getStyle("B$fila:H$fila")->applyFromArray($styleDerecha);
          $this->docexcel->getActiveSheet()->getStyle("E$fila:H$fila")->applyFromArray($bordes);

          /*Calculando las sumas de los totales*/
            $inicio = 7;
            $final = $fila - 1;
            $borde_abajo_inicio=$fila+1;
            $borde_abajo = $fila+100;
            $borde_derecha_inicio = 1;
            $borde_derecha_abajo = $fila+100;
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, "=SUM((E$inicio:E$final))");
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, "=SUM((F$inicio:F$final))");
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, "=SUM((G$inicio:G$final))");
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, "=SUM((H$inicio:H$final))");
            $this->docexcel->getActiveSheet()->getStyle("E$fila:H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($stylebordes_blancos);
            $this->docexcel->getActiveSheet()->getStyle("I$fila:Z$fila")->applyFromArray($stylebordes_blancos);
            $this->docexcel->getActiveSheet()->getStyle("A$borde_abajo_inicio:Z$borde_abajo")->applyFromArray($stylebordes_blancos);
            $this->docexcel->getActiveSheet()->getStyle("P$borde_derecha_inicio:Z$borde_derecha_abajo")->applyFromArray($stylebordes_blancos);

          /*************************************/






    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
