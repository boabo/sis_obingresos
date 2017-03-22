<?php
class RConciliacionBancaInter
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    private $fechas =array();

    private $datos_total=array();

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
    function imprimeCabecera() {


        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle('Resumen');

        foreach($this->objParam->getParametro('datos_total') as $valor) {
            if (!in_array($valor['fecha'],$this->fechas)) {
                array_push($this->fechas, $valor['fecha']);
            }

            $this->datos[$valor['tipo']][$valor['fecha']] = $valor['monto'];

        }

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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'CONCILIACION BANCARIA '. $this->objParam->getParametro('banco') . '(' . $this->objParam->getParametro('moneda').')'  );

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Del: '.  $this->objParam->getParametro('fecha_ini').'   Al: '.  $this->objParam->getParametro('fecha_fin') );

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);



        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValue('A5','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('B5','Total Ingresos');
        $this->docexcel->getActiveSheet()->setCellValue('C5','Total Skybiz');
        $this->docexcel->getActiveSheet()->setCellValue('D5','Dep. Registrados');

        $this->docexcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:D2');

        $this->docexcel->getActiveSheet()->getStyle('A4:D4')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A4:D4');

        $this->docexcel->getActiveSheet()->getStyle('A5:D5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:D5')->applyFromArray($styleTitulos2);


    }
    function generarDatos()
    {
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
        
        $styleTitulos4 = array(

            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFF66'
                )
            ));
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

        $this->docexcel->setActiveSheetIndex(0);

        for ($i=0; $i<count($this->fechas);$i++) {
            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':A' . $fila)->applyFromArray($styleTitulos2);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$fila,$this->fechas[$i]);
            $val = isset($this->datos['ingresos'][$this->fechas[$i]])?$this->datos['ingresos'][$this->fechas[$i]]:'0';
            $val_arch = isset($this->datos['archivos'][$this->fechas[$i]])?$this->datos['archivos'][$this->fechas[$i]]:'0';
            $val_depo = isset($this->datos['depositos'][$this->fechas[$i]])?$this->datos['depositos'][$this->fechas[$i]]:'0';
            if ( $val != $val_arch || $val != $val_depo || $val_depo != $val_arch) {
                $this->docexcel->getActiveSheet()->getStyle('B' . $fila . ':'.'D' . $fila)->applyFromArray($styleTitulos4);
            }

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $val);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $val_arch);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $val_depo);

            $fila++;

        }


        $sheet = 0;
        $fecha = '';


        foreach($this->objParam->getParametro('datos_detalle') as $valor) {
            if ($fecha != $valor['fecha']) {
                $sheet++;
                $this->docexcel->createSheet();
                $this->docexcel->setActiveSheetIndex($sheet);
                $this->docexcel->getActiveSheet()->setTitle($valor['fecha']);
                $fecha = $valor['fecha'];
                //cabecera
                $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
                $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
                $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
                $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
                $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'CONCILIACION BANCARIA '. $this->objParam->getParametro('banco') . '(' . $this->objParam->getParametro('moneda').')'  );

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'Fecha: '.  $valor['fecha']);
                $this->docexcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleTitulos1);
                $this->docexcel->getActiveSheet()->mergeCells('A2:D2');

                $this->docexcel->getActiveSheet()->getStyle('A3:D3')->applyFromArray($styleTitulos3);
                $this->docexcel->getActiveSheet()->mergeCells('A3:D3');

                $this->docexcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setWrapText(true);
                $this->docexcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($styleTitulos2);
                $this->docexcel->getActiveSheet()->setCellValue('A4','Fecha');
                $this->docexcel->getActiveSheet()->setCellValue('B4','PNR');
                $this->docexcel->getActiveSheet()->setCellValue('C4','Monto_Ing');
                $this->docexcel->getActiveSheet()->setCellValue('D4','Monto_Sky');
                $this->docexcel->getActiveSheet()->setCellValue('E4','Fecha Pago');
                $this->docexcel->getActiveSheet()->setCellValue('F4','Observaciones');
                $fila = 5;
            }

            if ( $valor['monto_ingresos'] != $valor['monto_archivos']) {
                $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':'.'F' . $fila)->applyFromArray($styleTitulos4);
            }

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $valor['fecha_hora']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $valor['pnr']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $valor['monto_ingresos']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $valor['monto_archivos']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $valor['fecha_pago']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $valor['observaciones']);

            $fila++;

        }
        $this->docexcel->setActiveSheetIndex(0);


    }
    function generarReporte(){

        //$this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);


    }

}
?>