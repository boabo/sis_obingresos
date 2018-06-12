<?php
class REstadoCuentaCorriente
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $objParam;
    public  $url_archivo;
    public  $fila = 0;

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
    function datosHeader ($totales,$resumen) {
        $this->datos_titulo = $totales;
        $this->resumen = $resumen;
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
                'color' => array(
                    'rgb' => '59A1EA'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
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
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'ESTADO DE CUENTA' );
        $this->docexcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:J2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'AGENCIA: '.$this->datos_titulo[0]["nombre"] );
        $this->docexcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:J3');

        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->setCellValue('A5','CREDITO');
        $this->docexcel->getActiveSheet()->mergeCells('A5:D5');
        $this->docexcel->getActiveSheet()->setCellValue('E5','DEBITO');
        $this->docexcel->getActiveSheet()->mergeCells('E5:I5');
        $this->docexcel->getActiveSheet()->setCellValue('J5','SALDO');
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);

        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->setCellValue('A6','Nro.');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Fecha Tran.');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Deposito');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Total');
        $this->docexcel->getActiveSheet()->setCellValue('E6','Fecha Tran.');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Cod. Reserva Boleto');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Neto');
        $this->docexcel->getActiveSheet()->setCellValue('H6','Tasa');
        $this->docexcel->getActiveSheet()->setCellValue('I6','Total');
        $this->docexcel->getActiveSheet()->setCellValue('J6','');
        $this->docexcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:J6')->applyFromArray($styleTitulos1);





    }

    function generarDatos(){
        $this->imprimeCabecera();
        $bordes = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )

        );
        $styleTitulos = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $numero = 1;
        $fila = 7;
        $datos = $this->datos_titulo;
       //var_dump($datos);exit;
        foreach ($datos as $value){

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            if ( $value['tipo'] == 'credito') {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila,  date_format(date_create($value["fecha"]), 'd/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['autorizacion__nro_deposito']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['importe']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, 0);

            }else{
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, 'comision');
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['comision']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, date_format(date_create($value["fecha"]), 'd/m/Y'));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['autorizacion__nro_deposito']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['neto']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, round($value['importe']-$value['neto']));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, round($value['comision']-$value['importe']));
            }
            $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("E$fila:F$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:J$fila")->applyFromArray($bordes);
            $numero++;
            $fila++;

            $this->fila =  $fila;
        }

        $fill = $this->fila+3;
        $resumen = $this->resumen;

        foreach ($resumen as $value){

            if ($value['tipo'] == 'boleta_garantia'){
                $tipo = 'Boleta Garantia';
            }elseif ($value['tipo'] == 'saldo_anterior'){
                $tipo = 'Saldo Anterior';
            }elseif ($value['tipo'] == 'comision'){
                $tipo = 'Comision';
            }elseif ($value['tipo'] == 'boleto'){
                $tipo = 'Boleto';
            }
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fill , $tipo);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fill , $value['monto']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fill , $value['moneda']);
            $fill++;
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