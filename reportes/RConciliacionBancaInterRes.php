<?php
class RConciliacionBancaInterRes
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    private $bancos =array();
    private $datos =array();
    private $datos_observaciones =array();

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

        foreach($this->objParam->getParametro('datos_conciliacion') as $valor) {
            if (!in_array($valor['banco'],$this->bancos)) {
                array_push($this->bancos, $valor['banco']);
            }

            $this->datos[$valor['banco']][$valor['tipo']][$valor['moneda']]['monto1'] = $valor['monto1'];
            $this->datos[$valor['banco']][$valor['tipo']][$valor['moneda']]['monto2'] = $valor['monto2'];

        }

        foreach($this->objParam->getParametro('datos_observaciones') as $valor) {

            if (!in_array($valor['banco'],$this->datos_observaciones)) {
                $this->datos_observaciones[$valor['banco']] = " - " . $valor['fecha'] . ' ' . $valor['observacion'];
            } else {
                $this->datos_observaciones[$valor['banco']] .= "\n - " . $valor['fecha'] . ' ' .$valor['observacion'];
            }


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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'RESUMEN CONCILIACION BANCARIA PERIODO '. $this->objParam->getParametro('periodo') . '-' . $this->objParam->getParametro('gestion'));

        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Del: '.  $this->objParam->getParametro('fecha_ini').'   Al: '.  $this->objParam->getParametro('fecha_fin') );

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(60);



        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValue('A5','Banco');
        $this->docexcel->getActiveSheet()->setCellValue('B5','Ingresos');
        $this->docexcel->getActiveSheet()->setCellValue('B6','BOB');
        $this->docexcel->getActiveSheet()->setCellValue('C6','USD');
        $this->docexcel->getActiveSheet()->setCellValue('D5','Depositos');
        $this->docexcel->getActiveSheet()->setCellValue('D6','BOB');
        $this->docexcel->getActiveSheet()->setCellValue('E6','USD');
        $this->docexcel->getActiveSheet()->setCellValue('F5','Diferencias');
        $this->docexcel->getActiveSheet()->setCellValue('F6','BOB');
        $this->docexcel->getActiveSheet()->setCellValue('G6','USD');
        $this->docexcel->getActiveSheet()->setCellValue('H5','Observaciones');

        $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:H2');

        //$this->docexcel->getActiveSheet()->getStyle('A4:D4')->applyFromArray($styleTitulos3);
        //$this->docexcel->getActiveSheet()->mergeCells('A4:D4');

        $this->docexcel->getActiveSheet()->getStyle('A5:H5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($styleTitulos2);
        $this->docexcel->getActiveSheet()->getStyle('A6:H6')->applyFromArray($styleTitulos2);
        $this->docexcel->getActiveSheet()->mergeCells('B5:C5');
        $this->docexcel->getActiveSheet()->mergeCells('D5:E5');
        $this->docexcel->getActiveSheet()->mergeCells('F5:G5');


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

        $styleNormal = array(
            'font'  => array(
                'bold'  => false,
                'size'  => 9,
                'name'  => 'Arial'

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ));


        $fila = 7;

        $this->docexcel->setActiveSheetIndex(0);
        //generar bancos
        for ($i=0; $i<count($this->bancos);$i++) {

            $this->docexcel->getActiveSheet()->getStyle('A' . $fila . ':A' . $fila)->applyFromArray($styleTitulos2);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$fila,$this->bancos[$i]);
            $this->docexcel->getActiveSheet()->getStyle('B' . $fila . ':H' . $fila)->applyFromArray($styleNormal);

            $boletos_bob = 0;
            $boletos_usd = 0;
            $depositos_bob = 0;
            $depositos_usd = 0;


            if (isset($this->datos[$this->bancos[$i]]['boletos']['BOB']['monto1'])) {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$fila,$this->datos[$this->bancos[$i]]['boletos']['BOB']['monto1']);
                $boletos_bob =  $this->datos[$this->bancos[$i]]['boletos']['BOB']['monto1'];
            }

            if (isset($this->datos[$this->bancos[$i]]['boletos']['USD']['monto1'])) {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$fila,$this->datos[$this->bancos[$i]]['boletos']['USD']['monto1']);
                $boletos_usd =  $this->datos[$this->bancos[$i]]['boletos']['USD']['monto1'];
            }

            if (isset($this->datos[$this->bancos[$i]]['depositos']['BOB']['monto1'])) {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,$fila,$this->datos[$this->bancos[$i]]['depositos']['BOB']['monto1']);
                $depositos_bob =  $this->datos[$this->bancos[$i]]['depositos']['BOB']['monto1'];
            }

            if (isset($this->datos[$this->bancos[$i]]['depositos']['USD']['monto1'])) {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,$fila,$this->datos[$this->bancos[$i]]['depositos']['USD']['monto1']);
                $depositos_usd =  $this->datos[$this->bancos[$i]]['depositos']['USD']['monto1'];
            }

            if ($boletos_bob != $depositos_bob) {
                $this->docexcel->getActiveSheet()->getStyle('F' . $fila . ':F' . $fila)->applyFromArray($styleTitulos4);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,$fila,$boletos_bob - $depositos_bob);

            }

            if ($boletos_usd != $depositos_usd) {
                $this->docexcel->getActiveSheet()->getStyle('G' . $fila . ':G' . $fila)->applyFromArray($styleTitulos4);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,$fila,$boletos_usd - $depositos_usd);

            }

            if (!in_array($this->bancos[$i],$this->datos_observaciones)) {
                $observaciones = $this->datos_observaciones[$this->bancos[$i]];
            } else {
                $observaciones = '';
            }

            if (isset($this->datos[$this->bancos[$i]]['ajustes']['BOB']['monto1'])) {
                if ($this->datos[$this->bancos[$i]]['ajustes']['BOB']['monto1'] > 0) {
                    $observaciones .= "\n- Saldo en BOB del periodo anterior depositado en el periodo actual " . $this->datos[$this->bancos[$i]]['ajustes']['BOB']['monto1'];
                }

                if ($this->datos[$this->bancos[$i]]['ajustes']['BOB']['monto2'] > 0) {
                    $observaciones .= "\n- Saldo en BOB del periodo actual depositado en el periodo siguiente " . $this->datos[$this->bancos[$i]]['ajustes']['BOB']['monto2'];
                }

            }



            if (isset($this->datos[$this->bancos[$i]]['ajustes']['USD']['monto1'])) {
                if ($this->datos[$this->bancos[$i]]['ajustes']['USD']['monto1'] > 0) {
                    $observaciones .= "\n- Saldo en BOB del periodo anterior depositado en el periodo actual " . $this->datos[$this->bancos[$i]]['ajustes']['USD']['monto1'];
                }

                if ($this->datos[$this->bancos[$i]]['ajustes']['USD']['monto2'] > 0) {
                    $observaciones .= "\n- Saldo en BOB del periodo actual depositado en el periodo siguiente " . $this->datos[$this->bancos[$i]]['ajustes']['USD']['monto2'];
                }

            }
            $this->docexcel->getActiveSheet()->getStyle("H$fila:H$fila")->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,$fila,$observaciones);


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