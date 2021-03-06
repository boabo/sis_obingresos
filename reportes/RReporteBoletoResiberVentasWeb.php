<?php
class RReporteBoletoResiberVentasWeb
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    public  $url_archivo;
    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
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
    function imprimeCabecera($indice,$title) {
        $this->docexcel->createSheet();
        $this->docexcel->setActiveSheetIndex($indice);
        $this->docexcel->getActiveSheet()->setTitle($title);

        $styleTitulos1 = array(
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


        $styleTitulos2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '0066CC'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $styleTitulos3 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'REPORTE BOLETOS' );
        $this->docexcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:J2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Del: '.  $this->objParam->getParametro('fecha_ini').'   Al: '.  $this->objParam->getParametro('fecha_fin') );
        $this->docexcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A4:J4');
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        $this->docexcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleTitulos2);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValue('A5','Boleto Resiber');
        $this->docexcel->getActiveSheet()->setCellValue('B5','Boleto Ventas Web');
        $this->docexcel->getActiveSheet()->setCellValue('C5','Numero de Tarjeta');
        $this->docexcel->getActiveSheet()->setCellValue('D5','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('E5','Monto Resiber');
        $this->docexcel->getActiveSheet()->setCellValue('F5','Monto Ventas Web');
        $this->docexcel->getActiveSheet()->setCellValue('G5','Moneda');

    }
    function generarBoletosSinVentasWeb()
    {
        $styleTitulos3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '0066CC'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $fila = 6;
        $datos = $this->objParam->getParametro('resiber');
        $this->imprimeCabecera(0,'Ingresos - No SkyByz');
        $boletos = array();
        foreach ($datos as $value)
        {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['boleto_resiber']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['boleto_ventas_web']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['numero_tarjeta'].' ');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto_resiber']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_ventas_web']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['moneda']);

            $this->docexcel->getActiveSheet()
                ->getStyle("A$fila:A$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()
                ->getStyle("G$fila:G$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()
                ->getStyle("C$fila:C$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleTitulos3);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->getAlignment()->setWrapText(true);
            $fila++;
        }
    }

    function generarVentasWebSinBoletos()
    {
        $styleTitulos3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '0066CC'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $fila = 6;
        $datos = $this->objParam->getParametro('ventas_web');
        $this->imprimeCabecera(1,'SkyBiz-Ingresos (void)');
        $boletos = array();
        foreach ($datos as $value)
        {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['boleto_resiber']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['boleto_ventas_web']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['numero_tarjeta'].' ');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto_resiber']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_ventas_web']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['moneda']);

            $this->docexcel->getActiveSheet()
                ->getStyle("A$fila:A$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()
                ->getStyle("C$fila:C$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()
                ->getStyle("G$fila:G$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleTitulos3);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->getAlignment()->setWrapText(true);
            $fila++;
        }
    }

    function generarDiferenciaMonto()
    {
        $styleTitulos3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '0066CC'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $fila = 6;
        $datos = $this->objParam->getParametro('montos_diferentes');
        $this->imprimeCabecera(2,'Diferencias en Montos');
        $boletos = array();
        foreach ($datos as $value)
        {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['boleto_resiber']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['boleto_ventas_web']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['numero_tarjeta'].' ');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto_resiber']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_ventas_web']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['moneda']);

            $this->docexcel->getActiveSheet()
                ->getStyle("A$fila:A$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()
                ->getStyle("C$fila:C$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()
                ->getStyle("G$fila:G$fila")
                ->getNumberFormat()
                ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleTitulos3);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->getAlignment()->setWrapText(true);
            $fila++;
        }
    }

    function generarReporte(){

        //$this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>