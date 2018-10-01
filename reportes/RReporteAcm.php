<?php
class RReporteAcm
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
        $this->docexcel->getActiveSheet()->setTitle('ACM DOMESTICO');
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



        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,1,'Boliviana de Aviacion (BoA)' );
        $this->docexcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('A1:B1');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'Pais/Estacion: '. $this->objParam->getParametro('codigo_largo') );
        $this->docexcel->getActiveSheet()->getStyle('A2:B2')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('A2:B2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,1,'ACM DOMESTICO BOB' );
        $this->docexcel->getActiveSheet()->getStyle('C1:F1')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('C1:F1');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'NUMERO: '. $this->objParam->getParametro('numero') );
        $this->docexcel->getActiveSheet()->getStyle('C2:F2')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('C2:F2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,1,'Fecha de Impresion: '. date("d-m-Y H:i:s"));
        $this->docexcel->getActiveSheet()->getStyle('G1:J1')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->getStyle('G2:J2')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('G1:J1');
        $this->docexcel->getActiveSheet()->mergeCells('G2:J2');

        $this->docexcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:J3');
        //$this->docexcel->getActiveSheet()->getStyle('A3:J3')->getAlignment()->setWrapText(true);

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Fecha: '. $this->objParam->getParametro('fecha'));
                $this->docexcel->getActiveSheet()->getStyle('A4:B4')->applyFromArray($styleBoa);
                $this->docexcel->getActiveSheet()->mergeCells('A4:B4');

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'Moneda: '. $this->objParam->getParametro('codigo') );
                $this->docexcel->getActiveSheet()->getStyle('C4:D4')->applyFromArray($styleBoa);
                $this->docexcel->getActiveSheet()->mergeCells('C4:D4');

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,4,'Ruta: '. $this->objParam->getParametro('ruta') );
                $this->docexcel->getActiveSheet()->getStyle('E4:F4')->applyFromArray($styleBoa);
                $this->docexcel->getActiveSheet()->mergeCells('E4:F4');

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,4,'Periodo del : '.$this->objParam->getParametro('fecha_ini').' al: '.$this->objParam->getParametro('fecha_fin'));
                $this->docexcel->getActiveSheet()->getStyle('G4:J4')->applyFromArray($styleBoa);
                $this->docexcel->getActiveSheet()->mergeCells('G4:J4');

                $this->docexcel->getActiveSheet()->getStyle('A7:J7')->applyFromArray($styleTitulos);
                $this->docexcel->getActiveSheet()->mergeCells('A7:J7');

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,6,'Agencia: '.$this->objParam->getParametro('nombre').'        Officce ID: '.$this->objParam->getParametro('office_id'));
                $this->docexcel->getActiveSheet()->getStyle('A6:J6')->applyFromArray($styleBoa);
                $this->docexcel->getActiveSheet()->mergeCells('A6:J6');



        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,8,'Billete' );
        $this->docexcel->getActiveSheet()->getStyle('B8')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,8,'Nro' );
        $this->docexcel->getActiveSheet()->getStyle('A8')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,8,'Neto' );
        $this->docexcel->getActiveSheet()->getStyle('C8')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,8,'SUM/Com-BSP' );
        $this->docexcel->getActiveSheet()->getStyle('D8:E8')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('D8:E8');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,8,'Over-Comision' );
        $this->docexcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,8,'%OVER' );
        $this->docexcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,8,'Mon' );
        $this->docexcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleBoa);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,8,'T/D' );
        $this->docexcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('I8:J8');

        $this->docexcel->getActiveSheet()->getStyle('B8:J8')->applyFromArray($styleBoa2);
        $this->docexcel->getActiveSheet()->getStyle('A8')->applyFromArray($styleBoa2);



        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'Hasta: '. $this->objParam->getParametro('fecha'));
        /*$this->docexcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:K3');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,4,'Fecha Reg: '. date("d-m-Y H:i:s"));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,4,'Agencia: '. $this->objParam->getParametro('nombre'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,4,'Forma de Pago: '. $this->objParam->getParametro('codigo'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,4,'Por: '. $_SESSION['_LOGIN']);
        $this->docexcel->getActiveSheet()->getStyle('A4:K4')->applyFromArray($styleTitulos);*/
        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

      /*  $this->docexcel->getActiveSheet()->setCellValue('A6','NRO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','AGENCIA');
        $this->docexcel->getActiveSheet()->setCellValue('C6','NUMERO ACM');
        $this->docexcel->getActiveSheet()->setCellValue('D6','FECHA');
        $this->docexcel->getActiveSheet()->setCellValue('E6','RUTA');
        $this->docexcel->getActiveSheet()->setCellValue('F6','IMPORTE');
        $this->docexcel->getActiveSheet()->setCellValue('G6','MONEDA');
        $this->docexcel->getActiveSheet()->getStyle('A6:G6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setWrapText(true);*/


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
        $styleContenido = array(
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
        $styleContenido2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'F1FBFF'
                )
            ),
            'font'  => array(
                'bold'  => false,
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
        $fila = 9;
        $numero = 1;
        $total = 10;
        $pago = 11;
        $datos = $this->objParam->getParametro('datos');


        //var_dump($datos);exit;
        foreach ($datos as $value) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($styleBoa2);


            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($total), 'Totales');
            $this->docexcel->getActiveSheet()->getStyle("B$total:J$total")->applyFromArray($styleBoa);



            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['billete']);
            $this->docexcel->getActiveSheet()->getStyle("B$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER);
            $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['neto']);
            $this->docexcel->getActiveSheet()->getStyle("C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $total, $value['neto_total_mb']);
            $this->docexcel->getActiveSheet()->getStyle("C$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("C$total")->applyFromArray($styleBoa);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['com_bsp']);
            $this->docexcel->getActiveSheet()->getStyle("D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $total, $value['total_bsp']);
            $this->docexcel->getActiveSheet()->getStyle("D$total:E$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("D$total:E$total")->applyFromArray($styleBoa);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['comision']);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $total, $value['importe']);
            $this->docexcel->getActiveSheet()->getStyle("F$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("F$total")->applyFromArray($styleBoa);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['porcentaje']);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['codigo']);
            $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['td']);
            $this->docexcel->getActiveSheet()->getStyle("I$fila:J$fila")->applyFromArray($styleContenido2);




            $this->docexcel->getActiveSheet()->mergeCells("D$fila:E$fila");
            $this->docexcel->getActiveSheet()->getStyle("D$fila:E$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->mergeCells("D$total:E$total");
            $this->docexcel->getActiveSheet()->getStyle("D$total:E$total")->applyFromArray($styleBoa);

              $this->docexcel->getActiveSheet()->mergeCells("I$fila:J$fila");
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->getStyle("C$fila:D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $numero++;
            $fila++;
            $total++;
            $pago++;
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
