<?php
class RReporteArchivoAcm
{
    private $docexcel;
    private $objWriter;
    private $drawing;
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

        $this->drawing = new PHPExcel_Worksheet_Drawing();


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
        $this->docexcel->getActiveSheet()->setTitle('REPORTE ARCHIVO ACM');
        $this->docexcel->setActiveSheetIndex(0);



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


        $styleBoa = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D8D8D8'
                )
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial',
                'color' => array(
                    'rgb'=>'021E49')

            ),

        );
        $styleBoa2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D8D8D8'
                )
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial',
                'color' => array(
                    'rgb'=>'021E49')

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
        $gdImage = imagecreatefrompng('../../../lib/imagenes/logos/logo.png');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(65);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        $this->docexcel->getActiveSheet()->mergeCells('A1:A3');
        /*$imagePath=dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg';
        $archivo= file($imagePath);
        //titulos

        $this->drawing->setPath($archivo);
        $this->drawing->setCoordinates('A2');*/
//        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,40,20);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,1,'Boliviana de Aviacion (BoA)' );
        $this->docexcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleBoa);
//        $this->docexcel->getActiveSheet()->mergeCells('B1');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,2,'Ingresos' );
        $this->docexcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleBoa);
//        $this->docexcel->getActiveSheet()->mergeCells('B2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,3,'BSP-BO' );
        $this->docexcel->getActiveSheet()->getStyle('B3')->applyFromArray($styleBoa);
//        $this->docexcel->getActiveSheet()->mergeCells('B3');
        $this->docexcel->getActiveSheet()->getStyle('C1:F1')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('C1:F1');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE ACMs GENERADOS POR COMISIONES AGT-BSP / BV DOM EN BOB' );
        $this->docexcel->getActiveSheet()->getStyle('C2:F2')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('C2:F2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'Periodo del : '.$this->objParam->getParametro('fecha_ini').' al: '.$this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('C3:F3')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('C3:F3');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,1,'Fecha: '. date("d-m-Y"));
        $this->docexcel->getActiveSheet()->getStyle('G1:I1')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('G1:I1');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,2,'Hora: '. date("H:i:s"));
        $this->docexcel->getActiveSheet()->getStyle('G2:I2')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('G2:I2');
        $this->docexcel->getActiveSheet()->getStyle('G3:I3')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('G3:I3');

        $this->docexcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A4:I4');


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,5,'Nro' );
        $this->docexcel->getActiveSheet()->getStyle('A5')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,5,'Agt' );
        $this->docexcel->getActiveSheet()->getStyle('B5')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'Nombre de la Agencia de Viaje' );
        $this->docexcel->getActiveSheet()->getStyle('C5')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,5,'Nro ACM' );
        $this->docexcel->getActiveSheet()->getStyle('D5:E5')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('D5:E5');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,5,'Neto-BOB' );
        $this->docexcel->getActiveSheet()->getStyle('F5')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,5,'Comision-BOB' );
        $this->docexcel->getActiveSheet()->getStyle('G5')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,5,'%OVER' );
        $this->docexcel->getActiveSheet()->getStyle('H5')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,5,'Can-Bol' );
        $this->docexcel->getActiveSheet()->getStyle('I5')->applyFromArray($styleBoa);
//        $this->docexcel->getActiveSheet()->mergeCells('I5:J5');

        $this->docexcel->getActiveSheet()->getStyle('A5:I5')->applyFromArray($styleBoa2);



        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(55);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);


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
        $styleImage = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        /*$styleContenido = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );*/
        $styleBoa = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D8D8D8'
                )
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial',
                'color' => array(
                    'rgb'=>'021E49')

            ),

        );
        $styleContenido = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'C9EEFF'
                )
            ),
            'font'  => array(
                'bold'  => false,
                'size'  => 12,
                'name'  => 'Arial',
                /*'color' => array(
                    'rgb' => '6B5ADD'
                )*/

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
        $styleAgencia= array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'C9EEFF'
                )
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',

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

        $styleSum = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'C9EEFF'
                )
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FF0000'
                )
            )
        );
        $fila = 6;
        $numero = 1;

        $aux = 6;
        $contador = 0;
        $total = 8;
        $datos = $this->objParam->getParametro('datos');
        $estacion=array();
        $totales=array();

        foreach($datos as $value){
            $valor=$value['cod_ciudad'];
            $neto[]= $value['neto_total_mb'];
            $comision[] = $value['importe_total_mb'];
            $boletos []= $value ['cant_bol_mb'];
            $agencia []= $value ['agencia'];
             if(!in_array($valor, $estacion)){
                 $estacion[]=$valor;
             }

        }


        foreach($estacion as $value1 ){

    foreach ($datos as $value) {
//        rsort($agencia);
        if ($value['cod_ciudad'] == $value1) {
//               var_dump($value1);exit;

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($styleBoa);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['office_id']);
            $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['agencia']);
            $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($styleAgencia);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['numero_acm']);
            $this->docexcel->getActiveSheet()->getStyle("D$fila:E$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['neto_total_mb']);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['importe_total_mb']);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['porcentaje'] . '%');
            $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['cant_bol_mb']);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido);



            $this->docexcel->getActiveSheet()->mergeCells("D$fila:E$fila");
            $this->docexcel->getActiveSheet()->getStyle("D$fila:E$fila")->applyFromArray($styleContenido);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido);

//            var_dump($fila);


            $numero++;
            $fila++;
            $total++;
        }//end if
    }//end for 1


            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, 'Total  Estacion '.$value1.':');
            $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->applyFromArray($styleBoa);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($fila), "=sum(F$aux:F$fila)");
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleBoa);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($fila), "=sum(G$aux:G$fila)");
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleBoa);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($fila), "=sum(I$aux:I$fila)");
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleBoa);
            $fila++;
            $totales[]=$fila-1;
            $total++;
            $aux = $fila;


        }//end for 2

                $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleTitulos);
        $fila++;

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($fila), 'Total General:');
        $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->applyFromArray($styleSum);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($fila), array_sum($neto));
        $this->docexcel->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->applyFromArray($styleSum);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($fila), array_sum($comision));
        $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->applyFromArray($styleSum);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($fila), array_sum($boletos));
        $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->applyFromArray($styleSum);

    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>

