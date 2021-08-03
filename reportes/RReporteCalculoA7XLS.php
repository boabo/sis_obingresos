<?php

class RReporteCalculoA7XLS{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    var $datos_detalle;
    var $datos_titulo;
    public  $url_archivo;
    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("BoA")
            ->setLastModifiedBy("BoA")
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


        $datos = $this->objParam->getParametro('datos');
        //$this->array_sort_by($datos,'nro_vuelo');
        //var_dump('$datos', $datos);exit;

        $fecha_desde = $this->objParam->getParametro('fecha_ini');
        $fecha_hasta = $this->objParam->getParametro('fecha_fin');

        $numberFormat = '#,##0.00';

        $index = 0;

        $this->addHoja('CALCULO A7',$index);

        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);



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

        $this->docexcel->getActiveSheet()->getStyle('A1:O4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:O2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:N2');
        $this->docexcel->getActiveSheet()->setCellValue('A1','REPORTE RESUMEN CALCULO A7');

        $this->docexcel->getActiveSheet()->getStyle('A3:N4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A3:N3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','Del: '.$fecha_desde.' Al: '.$fecha_hasta);
        $this->docexcel->getActiveSheet()->mergeCells('A4:N4');
        $this->docexcel->getActiveSheet()->setCellValue('A4','Resumen x Nro. de Vuelo');

        $this->docexcel->getActiveSheet()->setCellValue('O1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('O2', date('d/m/Y'));

        $this->docexcel->getActiveSheet()->getStyle('A5:O6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A5:N5');
        $this->docexcel->getActiveSheet()->setCellValue('A5','CALCULO A7');


        $this->docexcel->getActiveSheet()->setCellValue('A6','ID VUELO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','FECHA VUELO');
        $this->docexcel->getActiveSheet()->setCellValue('C6','NRO. VUELO');
        $this->docexcel->getActiveSheet()->setCellValue('D6','STATUS VUELO');
        $this->docexcel->getActiveSheet()->setCellValue('E6','RUTA BOA');
        $this->docexcel->getActiveSheet()->setCellValue('F6','MATRICULA BOA');
        $this->docexcel->getActiveSheet()->setCellValue('G6','MATRICULA SABSA');
        $this->docexcel->getActiveSheet()->setCellValue('H6','A7 NACIONAL');
        $this->docexcel->getActiveSheet()->setCellValue('I6','A7 INTERNACIONAL');
        $this->docexcel->getActiveSheet()->setCellValue('J6','SIN A7');
        $this->docexcel->getActiveSheet()->setCellValue('K6','TOTAL PAX BOA');
        $this->docexcel->getActiveSheet()->setCellValue('L6','IMPORTE BOA (Bs.)');
        $this->docexcel->getActiveSheet()->setCellValue('M6','TOTAL PAX SABSA');
        $this->docexcel->getActiveSheet()->setCellValue('N6','IMPORTE SABSA (Bs.)');
        $this->docexcel->getActiveSheet()->setCellValue('O6','IMP. BOA -  IMP. SABSA (Bs.)');

        $fila = 7;
        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');

        $parcial_a7_nacional = 0;
        $parcial_a7_internacional = 0;
        $parcial_sin_a7 = 0;
        $parcial_pax_boa = 0;
        $parcial_importe_boa = 0;
        $parcial_pax_sabsa = 0;
        $parcial_importe_sabsa = 0;
        $parcial_boa_menos_saba = 0;

        $total_a7_nacional = 0;
        $total_a7_internacional = 0;
        $total_sin_a7 = 0;
        $total_pax_boa = 0;
        $total_importe_boa = 0;
        $total_pax_sabsa = 0;
        $total_importe_sabsa = 0;
        $total_boa_menos_saba = 0;

        $vuelo_id  = '';
        $fecha_vuelo = '';
        $nro_vuelo = '';
        $status_vuelo = '';
        $ruta_boa = '';
        $matricula_boa = '';
        $matricula_sabsa = '';
        $index_color = 0;

        $filas_grupo = $fila;
        foreach ($datos as  $rec){

            $styleGroup = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => $color_cell[$index_color]
                    )
                )
            );

            if ( $nro_vuelo != $rec['nro_vuelo'] && $nro_vuelo != '' ){

                $total_a7_nacional += $parcial_a7_nacional;
                $total_a7_internacional += $parcial_a7_internacional;
                $total_sin_a7 += $parcial_sin_a7;
                $total_pax_boa += $parcial_pax_boa;
                $total_importe_boa += $parcial_importe_boa;
                $total_pax_sabsa += $parcial_pax_sabsa;
                $total_importe_sabsa += $parcial_importe_sabsa;
                $total_boa_menos_saba += $parcial_boa_menos_saba;

                $this->docexcel->getActiveSheet()->getStyle('A'.$filas_grupo.':O'.($fila+1))->applyFromArray($styleGroup);

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $vuelo_id);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, DateTime::createFromFormat('Y-m-d', $fecha_vuelo)->format('d/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $nro_vuelo);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $status_vuelo);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $ruta_boa);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $matricula_boa);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $matricula_sabsa);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $parcial_a7_nacional);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $parcial_a7_internacional);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $parcial_sin_a7);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $parcial_pax_boa);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $parcial_importe_boa);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $parcial_pax_sabsa);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $parcial_importe_sabsa);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $parcial_boa_menos_saba);

                $parcial_a7_nacional = 0;
                $parcial_a7_internacional = 0;
                $parcial_sin_a7 = 0;
                $parcial_pax_boa = 0;
                $parcial_importe_boa = 0;
                $parcial_pax_sabsa = 0;
                $parcial_importe_sabsa = 0;
                $parcial_boa_menos_saba = 0;

                $parcial_a7_nacional += $rec['total_nac'];
                $parcial_a7_internacional += $rec['total_inter'];
                $parcial_sin_a7 += $rec['total_cero'];
                $parcial_pax_boa += $rec['nro_pax_boa'];
                $parcial_importe_boa += $rec['importe_boa'];
                $parcial_pax_sabsa += $rec['nro_pax_sabsa'];
                $parcial_importe_sabsa += $rec['importe_sabsa'];
                $parcial_boa_menos_saba += $rec['diferencia'];


                $fila++;
                $index_color++;
                if($index_color == 2){
                    $index_color = 0;
                }
                $filas_grupo = $fila;
            }else{

                /*if ($nro_vuelo == $rec['nro_vuelo'] && $fecha_vuelo != $rec['fecha_vuelo']){

                    $total_a7_nacional += $parcial_a7_nacional;
                    $total_a7_internacional += $parcial_a7_internacional;
                    $total_sin_a7 += $parcial_sin_a7;
                    $total_pax_boa += $parcial_pax_boa;
                    $total_importe_boa += $parcial_importe_boa;
                    $total_pax_sabsa += $parcial_pax_sabsa;
                    $total_importe_sabsa += $parcial_importe_sabsa;
                    $total_boa_menos_saba += $parcial_boa_menos_saba;

                    $this->docexcel->getActiveSheet()->getStyle('A'.$filas_grupo.':O'.($fila+1))->applyFromArray($styleGroup);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $vuelo_id);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, DateTime::createFromFormat('Y-m-d', $fecha_vuelo)->format('d/m/Y'));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $nro_vuelo);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $status_vuelo);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $ruta_boa);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $matricula_boa);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $matricula_sabsa);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $parcial_a7_nacional);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $parcial_a7_internacional);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $parcial_sin_a7);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $parcial_pax_boa);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $parcial_importe_boa);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $parcial_pax_sabsa);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $parcial_importe_sabsa);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $parcial_boa_menos_saba);

                    $parcial_a7_nacional = 0;
                    $parcial_a7_internacional = 0;
                    $parcial_sin_a7 = 0;
                    $parcial_pax_boa = 0;
                    $parcial_importe_boa = 0;
                    $parcial_pax_sabsa = 0;
                    $parcial_importe_sabsa = 0;
                    $parcial_boa_menos_saba = 0;

                    $parcial_a7_nacional += $rec['total_nac'];
                    $parcial_a7_internacional += $rec['total_inter'];
                    $parcial_sin_a7 += $rec['total_cero'];
                    $parcial_pax_boa += $rec['nro_pax_boa'];
                    $parcial_importe_boa += $rec['importe_boa'];
                    $parcial_pax_sabsa += $rec['nro_pax_sabsa'];
                    $parcial_importe_sabsa += $rec['importe_sabsa'];
                    $parcial_boa_menos_saba += $rec['diferencia'];




                    $fila++;
                    if($index_color == 2){
                        $index_color = 0;
                    }
                    $filas_grupo = $fila;

                }else{*/
                    $parcial_a7_nacional += $rec['total_nac'];
                    $parcial_a7_internacional += $rec['total_inter'];
                    $parcial_sin_a7 += $rec['total_cero'];
                    $parcial_pax_boa += $rec['nro_pax_boa'];
                    $parcial_importe_boa += $rec['importe_boa'];
                    $parcial_pax_sabsa += $rec['nro_pax_sabsa'];
                    $parcial_importe_sabsa += $rec['importe_sabsa'];
                    $parcial_boa_menos_saba += $rec['diferencia'];
                //}
            }


            $vuelo_id  = $rec['vuelo_id'];
            $fecha_vuelo = $rec['fecha_vuelo'];
            $nro_vuelo = $rec['nro_vuelo'];
            $status_vuelo = $rec['status'];
            $ruta_boa = $rec['ruta_vl'];
            $matricula_boa = $rec['matricula_boa'];
            $matricula_sabsa = $rec['matricula_sabsa'];


        }


        $this->docexcel->getActiveSheet()->getStyle('A'.$filas_grupo.':O'.($fila))->applyFromArray($styleGroup);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $vuelo_id);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, DateTime::createFromFormat('Y-m-d', $fecha_vuelo)->format('d/m/Y'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $nro_vuelo);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $status_vuelo);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $ruta_boa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $matricula_boa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $matricula_sabsa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $parcial_a7_nacional);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $parcial_a7_internacional);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $parcial_sin_a7);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $parcial_pax_boa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $parcial_importe_boa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $parcial_pax_sabsa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $parcial_importe_sabsa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $parcial_boa_menos_saba);

        $fila++;

        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':O'.$fila)->getFont()->setBold(true);
        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getActiveSheet()->setCellValue('A'.$fila,'TOTALES');

        $styleGroup = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => $color_cell[2]
                )
            )
        );

        $total_a7_nacional += $parcial_a7_nacional;
        $total_a7_internacional += $parcial_a7_internacional;
        $total_sin_a7 += $parcial_sin_a7;
        $total_pax_boa += $parcial_pax_boa;
        $total_importe_boa += $parcial_importe_boa;
        $total_pax_sabsa += $parcial_pax_sabsa;
        $total_importe_sabsa += $parcial_importe_sabsa;
        $total_boa_menos_saba += $parcial_boa_menos_saba;

        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':O'.($fila))->applyFromArray($styleGroup);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $total_a7_nacional);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $total_a7_internacional);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $total_sin_a7);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $total_pax_boa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $total_importe_boa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $total_pax_sabsa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $total_importe_sabsa);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $total_boa_menos_saba);
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
        //$this->imprimeDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>