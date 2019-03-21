<?php
class RMovimientos
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $monto=array();
    private $montoTotal=array();
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
    function datosHeader ($totales,$periodoAnteriorMov) {
        $this->datos_titulo = $totales;
        $this->periodoAnteriorMov = $periodoAnteriorMov;
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
        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'REPORTE DE MOVIMIENTOS POR PERIODO' );
        $this->docexcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:F2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'AGENCIA: '.$this->datos_titulo[0]["nombre"] );
        $this->docexcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:F3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,4,'Desde:'.$this->objParam->getParametro('fecha_ini').'  '.'Hasta: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->mergeCells('B4:D4');

        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->setCellValue('A6','Nro');
        $this->docexcel->getActiveSheet()->setCellValue('B6','PERIODO');
        $this->docexcel->getActiveSheet()->setCellValue('C6','CREDITO MONTO');
        $this->docexcel->getActiveSheet()->setCellValue('D6','DEBITO MONTO');
        $this->docexcel->getActiveSheet()->setCellValue('E6','SALDO SIN BOLETA DE GARANTIA');
        $this->docexcel->getActiveSheet()->setCellValue('F6','SALDO CON BOLETA DE GARANTIA');
        $this->docexcel->getActiveSheet()->getStyle('A6:F6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:F6')->getAlignment()->setWrapText(true);


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
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'F8CBAD'
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
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            ),
              );
            $numeros2 = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'F8CBAD'
                    )
                ),

                'font'  => array(
                    'bold'  => true,
                    'size'  => 12,
                    'name'  => 'Arial'
                ),


        );
        $styleTitulos2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
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

        $styleContenido3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Times New Roman'


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
        $anterior = 7;
        $fila = 8;
    		$numero = 1;
    		$aux = 7;
        $totales=array();
    		$total = 7;
    		$pago= 11;
    		$estacion=array();
        $datos = $this->datos_titulo;

        foreach($datos as $value){
    				 if(!in_array($valor, $estacion)){
    						 $estacion[]=$valor;
    				 }
           }
    foreach($estacion as $value1 ){
        foreach ($datos as $value){


            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:F$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($styleTitulos2);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['periodo']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['monto_total']);
            array_push($this->montoTotal, $value['monto_total']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['monto_total_debito']);
            array_push($this->montoDebito, $value['monto_total_debito']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['saldo']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['saldo2']);
            $this->docexcel->getActiveSheet()->getStyle("C$fila:F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:F$fila")->applyFromArray($bordes);


            $numero++;
            $fila++;
            $total++;

        }
        $periodoAnteriorMov = $this->periodoAnteriorMov;

        if ($periodoAnteriorMov != NULL) {
          foreach ($periodoAnteriorMov as $value3){
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO ANTERIOR');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $anterior, $value3['saldo_boleta']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $anterior, $value3['saldo_sin_boleta']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $anterior, $value3['debito_anterior']);
          array_push($this->montoDebito, $value3['debito_anterior']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $anterior, $value3['credito_anterior']);
          array_push($this->montoTotal, $value3['credito_anterior']);
          $this->docexcel->getActiveSheet()->getStyle("C$anterior:F$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          $this->docexcel->getActiveSheet()->mergeCells("A$anterior:B$anterior");
          //$this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
          $this->docexcel->getActiveSheet()->getStyle("A$anterior:F$anterior" )->applyFromArray($styleTitulos3);
        }
      }
        else {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'LA AGENCIA NO CUENTA CON UN SALDO A LA FECHA ANTERIOR');
          $this->docexcel->getActiveSheet()->mergeCells("A$anterior:F$anterior");
          //$this->docexcel->getActiveSheet()->mergeCells("I$anterior:J$anterior");
          $this->docexcel->getActiveSheet()->getStyle("A$anterior:F$anterior" )->applyFromArray($styleTitulos3);
        }

        $total=$fila;

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($total), 'Total');
        $this->docexcel->getActiveSheet()->mergeCells("A$total:B$total");
        $this->docexcel->getActiveSheet()->getStyle("A$total:B$total")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("A$total:F$total")->applyFromArray($styleBoa4);

         $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($total), array_sum($this->montoTotal));
         $this->docexcel->getActiveSheet()->getStyle("C$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
         $this->docexcel->getActiveSheet()->getStyle("C$total")->applyFromArray($styleContenido3);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($total), array_sum($this->montoDebito));
        $this->docexcel->getActiveSheet()->getStyle("D$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("D$total")->applyFromArray($styleContenido3);





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
