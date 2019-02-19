<?php
class RReporteControl
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $depositos=array();
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
        $this->docexcel->getActiveSheet()->setTitle('Control Agencias');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'EDEDED'
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
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $gdImage = imagecreatefromjpeg('../../../sis_obingresos/reportes/Logo2.jpg');
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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,1,'REPORTE CONTROL AGENCIAS' );
        $this->docexcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A1:G1');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'AGENCIA: '.$this->objParam->getParametro('nombre'));
        $this->docexcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:G2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'Fecha: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:G3');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Generado por: '. $_SESSION['_LOGIN']);
        $this->docexcel->getActiveSheet()->mergeCells('A4:G4');
        $this->docexcel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleTitulos);
        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);

        $this->docexcel->getActiveSheet()->setCellValue('A6','PERIODO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','ID_PERIODO_VENTA');
        $this->docexcel->getActiveSheet()->setCellValue('C6','DEPOSITOS + SALDOS');
        $this->docexcel->getActiveSheet()->setCellValue('D6','DEPOSITOS');
        $this->docexcel->getActiveSheet()->setCellValue('E6','DEBITOS');
        $this->docexcel->getActiveSheet()->setCellValue('F6','SALDO CALCULADO');
        $this->docexcel->getActiveSheet()->setCellValue('G6','SALDO ARRASTRADO');
        $this->docexcel->getActiveSheet()->setCellValue('H6','DIFERENCIA');
        $this->docexcel->getActiveSheet()->getStyle('A6:H6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setWrapText(true);


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
        $Totales = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'


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

            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '00B050'
                )

            ),
        );
        $Totales2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri'


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

            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '00B050'
                )

            ),
        );
        $Calculado = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri'


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
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFC000'
                )

            ),
        );
        $diferencia = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri'


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
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FF5733'
                )

            ),
        );
        // $styleContenido = array(
        //       'fill' => array(
        //         'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //         'color' => array(
        //             'rgb' => 'DDEBF7'
        //         )
        //     )
        // );

        $fila = 7;
    		$numero = 1;
    		$aux = 7;
        $inicio = 7;
        $inicio2 = 7;
        $anterior = 7;


        $datos = $this->objParam->getParametro('datos');
        $periodo=array();

        foreach($datos as $value){
            $valor=$value['id_periodo_venta'];

             if(!in_array($valor, $periodo)){
                 $periodo[]=$valor;
             }
             //var_dump($datos);exit;
           }

foreach($periodo as $value1 ){
        //var_dump($datos);exit;
        foreach ($datos as $value){
          if ($value['id_periodo_venta'] == $value1) {

          if($value['tipo'] == 'credito'){

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['id_periodo_venta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['depositos_con_saldos']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['periodo']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, 0);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, 0);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, 0);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, 0);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, 0); 
            $this->docexcel->getActiveSheet()->getStyle("D$fila:D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("C$fila:H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $fila++;
            $numero++;
            $aux=$fila-1;

          }
          elseif($value['tipo'] == 'depositos'){
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $aux, $value['depositos']);

            $this->docexcel->getActiveSheet()->getStyle("D$aux:D$aux")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          }elseif($value['tipo'] == 'debitos'){

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $aux, $value['debitos']);
            $this->docexcel->getActiveSheet()->getStyle("E$aux:E$aux")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          }elseif($value['tipo'] == 'arrastre'){

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $aux, $value['saldo_arrastrado']);
            $this->docexcel->getActiveSheet()->getStyle("G$aux:G$aux")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          }


        } //end if

      } //end ForEach 1
      // $fila++;
      // $aux=$fila;

    //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $aux, $value['depositos']);
      // $fila++;
      // $aux=$fila-1;


    } //End Foreach2
    for ($i=7; $i < $fila; $i++) {
      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i, "=SUM((C$i-E$i))");
      $this->docexcel->getActiveSheet()->getStyle("F$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


    }
    for ($i=8; $i < $fila; $i++) {
      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i,"=SUM(( F$anterior-G$i))");
      $this->docexcel->getActiveSheet()->getStyle("H$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_00);
      $anterior++;
    }
    for ($i=7; $i < $fila; $i++) {
      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila,"=SUM(D$inicio:D$i)");
      $this->docexcel->getActiveSheet()->getStyle("D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_00);
      $this->docexcel->getActiveSheet()->getStyle("D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

    }
    for ($i=7; $i < $fila; $i++) {
      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila,"=SUM(E$inicio:E$i)");
      $this->docexcel->getActiveSheet()->getStyle("E$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_00);
      $this->docexcel->getActiveSheet()->getStyle("E$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

    }
    for ($i=7; $i < $fila; $i++) {
      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila,"=SUM(H$inicio:H$i)");
      $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_00);
      $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

    }
    $final=$fila-1;
    $dife=$fila+1;

    $copia=$this->docexcel->getActiveSheet()->getCell('G7')->getValue();
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $inicio,$copia);
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila,"=SUM(D$fila-E$fila)");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila+1,"DIFERENCIA");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila+1,"=SUM(F$fila-F$final)");
    $this->docexcel->getActiveSheet()->getStyle("E$dife")->applyFromArray($diferencia);
    $this->docexcel->getActiveSheet()->getStyle("F$dife")->applyFromArray($diferencia);
    $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($Totales);
    $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($Totales);
    $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($Totales);
    $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($Totales2);
    $this->docexcel->getActiveSheet()->getStyle("F$final")->applyFromArray($Calculado);
    $this->docexcel->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_00);
    $this->docexcel->getActiveSheet()->getStyle("F$dife")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_00);
    $this->docexcel->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);



  }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
