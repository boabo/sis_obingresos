<?php

class RReporteCalculoOverNoIataXLS
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    var $datos_detalle;
    var $datos_titulo;
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

    public function addHoja($name,$index){

        $this->docexcel->createSheet($index)->setTitle($name);
        $this->docexcel->setActiveSheetIndex($index);
        return $this->docexcel;
    }

    function array_sort_by(&$arrIni, $col, $order = SORT_ASC){
        $arrAux = array();
        foreach ($arrIni as $key=> $row)
        {
            $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
            $arrAux[$key] = strtolower($arrAux[$key]);
        }
        array_multisort($arrAux, $order, $arrIni);
    }

    function hiddenString($str, $start = 1, $end = 1){
        $len = strlen($str);
        return substr($str, 0, $start) . str_repeat('X', $len - ($start + $end)) . substr($str, $len - $end, $end);
    }

    function imprimeDatos(){


        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'ffffff'
                )

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '4682b4'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            ));

        $this->styleVacio = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FA8072'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );


        $datos = $this->objParam->getParametro('datos');//print_r($datos);exit;

        $this->array_sort_by($datos, 'OfficeId');

        $tipo = $this->objParam->getParametro('tipo');
        $fecha_desde = $this->objParam->getParametro('fecha_desde');
        $fecha_hasta = $this->objParam->getParametro('fecha_hasta');

        $fecha = date('d/m/Y');
        $numberFormat = '#,##0.00';

        $index = 0;
        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');



        /*CALCULO NO-IATA*/
        $this->addHoja($tipo, $index);

        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,6);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);


        /*logo*/
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('BoA ERP');
        $objDrawing->setDescription('BoA ERP');
        $objDrawing->setPath('../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWidth(105);
        $objDrawing->setHeight(75);
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        /*logo*/


        $this->docexcel->getActiveSheet()->getStyle('A1:G4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:G2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('B2:G2');
        $this->docexcel->getActiveSheet()->setCellValue('G1','Fecha: '.$fecha);
        $this->docexcel->getActiveSheet()->setCellValue('B2','REPORTE ACMs GENERADOS POR COMISIONES');

        $this->docexcel->getActiveSheet()->setCellValue('B3','Desde: ');
        $this->docexcel->getActiveSheet()->setCellValue('C3',date_format(date_create($fecha_desde),'d/m/Y    '));
        $this->docexcel->getActiveSheet()->setCellValue('D3','Hasta: ');
        $this->docexcel->getActiveSheet()->setCellValue('E3',date_format(date_create($fecha_hasta),'d/m/Y    '));
        $this->docexcel->getActiveSheet()->setCellValue('F3','Agencias: ');
        $this->docexcel->getActiveSheet()->setCellValue('G3',$tipo);

        /*$this->docexcel->getActiveSheet()->getStyle('A3:R4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:Q3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:Q4');
        $this->docexcel->getActiveSheet()->setCellValue('R1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('R2', $fecha);*/

        $this->docexcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->setCellValue('A5','Numero ACM');
        $this->docexcel->getActiveSheet()->setCellValue('B5','Tipo Documento');
        $this->docexcel->getActiveSheet()->setCellValue('C5','Desde');
        $this->docexcel->getActiveSheet()->setCellValue('D5','Hasta');
        $this->docexcel->getActiveSheet()->setCellValue('E5','% Comision');
        $this->docexcel->getActiveSheet()->setCellValue('F5','Moneda');
        $this->docexcel->getActiveSheet()->setCellValue('G5','Importe Comision');



        $fila = 6;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');
        $departamentos = array('CBB'=>'COCHABAMBA', 'CIJ'=>'COBIJA', 'LPB'=>'LA PAZ', 'ORU'=>'ORURO', 'POI'=>'POTOSI', 'SRE'=>'SUCRE', 'SRZ'=>'SANTA CRUZ', 'TDD'=>'TRINIDAD', 'TJA'=>'TARIJA');

        $estacion = '';
        foreach ($datos as $key => $rec){

            if( $estacion != substr($rec->OfficeId, 0, 3) ) {
                $estacion = substr($rec->OfficeId, 0, 3);
                $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, ' Estación: ' .$estacion .' '.$departamentos[$estacion]);
                $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[3]);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getFont()->setBold(true);

                $fila++;
            }

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $rec->DocumentNumber);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $rec->DocumentType);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec->From);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec->To);
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->PaymentDate != null || $rec->PaymentDate != '' ? DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y') : '');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec->CommissionPercent.' %');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec->Currency);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec->CommssionAmount);

            $fila++;
            $estacion = substr($rec->OfficeId, 0, 3);;
        }

        /*$this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '=SUM(J'.$index_total.':J'.($fila-1).')');*/
        //FIN PAGO ATC
    }

    function obtenerFechaEnLetra($fecha){
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $dia= date("d", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        // var_dump()
        $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
        return $dia.' de '.$mes.' del '.$anno;
    }
    function generarReporte(){
        $this->imprimeDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>