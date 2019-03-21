<?php
class REstadoCuentaGeneral
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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,1,'REPORTE GENERAL DE SALDOS' );
        $this->docexcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('C1:H1');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'(Expresado Bolivianos)' );
        $this->docexcel->getActiveSheet()->getStyle('A2:L2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:K2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'Desde:'.$this->objParam->getParametro('fecha_ini').'  '.'Hasta: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A4:L4')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:K3');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'Fecha Reporte: '. date("d-m-Y H:i:s"));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,4,'Tipo Agencia: '. $this->objParam->getParametro('tipo_agencia'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,4,'Forma de Pago: '. $this->objParam->getParametro('forma_pago'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,4,'Generado por: '. $_SESSION['_LOGIN']);
        $this->docexcel->getActiveSheet()->getStyle('A5:L5')->applyFromArray($styleTitulos);
        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);

        $this->docexcel->getActiveSheet()->setCellValue('A6','NRO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','NOMBRE');
        $this->docexcel->getActiveSheet()->setCellValue('C6','OFFICE-ID');
        $this->docexcel->getActiveSheet()->setCellValue('D6','TIPO AGENCIA');
        $this->docexcel->getActiveSheet()->setCellValue('E6','FORMA DE PAGO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','CIUDAD');
        $this->docexcel->getActiveSheet()->setCellValue('G6','CREDITOS');
        $this->docexcel->getActiveSheet()->setCellValue('H6','GARANTIA');
        $this->docexcel->getActiveSheet()->setCellValue('I6','DEBITOS');
        $this->docexcel->getActiveSheet()->setCellValue('J6','AJUSTES');
        $this->docexcel->getActiveSheet()->setCellValue('K6','SALDO SIN BOLETA DE GARANTIA');
        $this->docexcel->getActiveSheet()->getStyle('K6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->setCellValue('L6','SALDO CON BOLETA DE GARANTIA');
        $this->docexcel->getActiveSheet()->getStyle('L6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:L6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:L6')->getAlignment()->setWrapText(true);


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
        //var_dump($datos);exit;
        foreach($estacion as $value1 ){
        foreach ($datos as $value) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nombre']);
            $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['codigo_int']);
            $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['tipo_agencia']);
            $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['formas_pago']);
            $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['codigo_ciudad']);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_creditos']);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['garantia']);
            $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['monto_debitos']);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_ajustes']);
            $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['saldo_sin_boleto']);
            $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['saldo_con_boleto']);
            $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("G$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            // $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($styleContenido);
            $numero++;
    				$fila++;
    				$total++;
    			}
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($total), 'Total');
          $this->docexcel->getActiveSheet()->mergeCells("D$total:F$total");
          $this->docexcel->getActiveSheet()->mergeCells("A$total:C$total");
          $this->docexcel->getActiveSheet()->getStyle("D$total:F$total")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("D$total:L$total")->applyFromArray($styleBoa4);
          $this->docexcel->getActiveSheet()->getStyle("A$total:C$total")->applyFromArray($styleBoa4);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($fila), "=sum(G$aux:G$fila)");
          $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido3);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($fila), "=sum(H$aux:H$fila)");
          $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido3);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($fila), "=sum(I$aux:I$fila)");
          $this->docexcel->getActiveSheet()->getStyle("I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido3);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($fila), "=sum(J$aux:J$fila)");
          $this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido3);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, ($fila), "=sum(K$aux:K$fila)");
          $this->docexcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($styleContenido3);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, ($fila), "=sum(L$aux:L$fila)");
          $this->docexcel->getActiveSheet()->getStyle("L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($styleContenido3);



          $fila++;
          $totales[]=$fila-1;
          $total++;
          $aux = $fila;

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
