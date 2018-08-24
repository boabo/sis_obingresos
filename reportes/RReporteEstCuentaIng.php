<?php
class RReporteEstCuentaIng
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
    function datosHeader ($totales) {
        $this->datos_titulo = $totales;
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
                    'rgb' => 'F5B041'
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
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'ESTADO DE CUENTA' );
        $this->docexcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:G2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'AGENCIA: '.$this->datos_titulo[0]["nombre"] );
        $this->docexcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:G3');

        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->setCellValue('A6','PERIODOS');
        $this->docexcel->getActiveSheet()->setCellValue('B6','IMPORTE A PAGAR');
        $this->docexcel->getActiveSheet()->setCellValue('C6','FECHA DE PAGO S/G CALENDARIO BSP');
        $this->docexcel->getActiveSheet()->setCellValue('D6','IMPORTE DEPOSITADO');
        $this->docexcel->getActiveSheet()->setCellValue('E6','FECHA DE DEPÓSITO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','NRO DE DEPÓSITO');
        $this->docexcel->getActiveSheet()->setCellValue('G6','OBSERVACIONES');
        $this->docexcel->getActiveSheet()->getStyle('A6:G6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setWrapText(true);


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
        $fila = 7;
        $fill = 7;
        $datos = $this->datos_titulo;
        foreach ($datos as $value){
                if($value['tipo'] == 'boletos' ) {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['periodo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['monto_debito']);
                    array_push( $this->montoDebito,$value['monto_debito']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila,date_format(date_create($value["fecha_pago"]), 'd/m/Y') );
                    $fila++;
                    $this->filles = $fila;
                }else{
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fill , $value['monto_deposito']);
                    array_push($this->monto, $value['monto_deposito']);
                    $this->garante = $value['garante'];
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fill ,date_format(date_create($value["fecha"]), 'd/m/Y') );
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fill , $value['nro_deposito']);
                    $this->docexcel->getActiveSheet()->getStyle("D$fill:D$fill")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                    $this->docexcel->getActiveSheet()->getStyle("E$fill:E$fill")->applyFromArray($styleTitulos);
                    $fill ++;
                    $this->fill = $fill;
                }
               if ($fill > $fila){
                   $this->docexcel->getActiveSheet()->getStyle("A$fill:G$fill")->applyFromArray($bordes);
               }else{
                   $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($bordes);

               }

            $this->docexcel->getActiveSheet()->getStyle("C$fila:C$fila")->applyFromArray($styleTitulos);
            $this->docexcel->getActiveSheet()->getStyle("B$fila:B$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


        }
        $subtitulo = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 10,
                'name'  => 'Arial',
            )
        );
        $saldos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 10,
                'name'  => 'Arial',
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '27AE60'
                )
            ),
        );
        $deudas = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 10,
                'name'  => 'Arial',
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'CD6155'
                )
            ),
        );
        if ($this->filles > $this->fill){
            $this->pika = $this->filles ;
        }else{
            $this->pika = $this->fill ;
        }
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$this->pika,'Total');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$this->pika,array_sum($this->montoDebito));
        $this->docexcel->getActiveSheet()->getStyle("B$this->pika:B$this->pika")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,$this->pika,'Total');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,$this->pika,array_sum($this->monto));
        $this->docexcel->getActiveSheet()->getStyle("D$this->pika:D$this->pika")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        if ($this->garante > 0) {
            $aux = $this->pika + 1;
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $this->pika + 1, 'Boleta Garantia');
            $this->docexcel->getActiveSheet()->getStyle("C$aux:C$aux")->applyFromArray($subtitulo);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $this->pika + 1, $this->garante);
            $this->docexcel->getActiveSheet()->getStyle("D$aux:D$aux")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $boole = $this->pika+3;
        $this->docexcel->getActiveSheet()->getStyle("A$boole:D$boole" )->applyFromArray($subtitulo);
        $this->docexcel->getActiveSheet()->getStyle("B$boole:B$boole" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$this->pika+2,'MONTO DE LA DEUDA');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$this->pika+2,array_sum($this->montoDebito));



        $bool = $this->pika+2;
        $this->docexcel->getActiveSheet()->getStyle("A$bool:B$bool" )->applyFromArray($subtitulo);
        $this->docexcel->getActiveSheet()->getStyle("B$bool:B$bool" )->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$this->pika+3,'MONTO PAGADO');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$this->pika+3,array_sum($this->monto)+ $this->garante);

        $saldo = array_sum($this->monto) + $this->garante - array_sum($this->montoDebito);
        if($saldo > 0 ){
            $cap = $this->pika + 4;
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$this->pika+4,'IMPORTE  A FAVOTR  AGT');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$this->pika+4,$saldo);
            $this->docexcel->getActiveSheet()->getStyle("B$cap:B$cap")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $saldo = $this->pika+4;
            $this->docexcel->getActiveSheet()->getStyle("A$saldo:B$saldo" )->applyFromArray($saldos);

        }else{
            $cap = $this->pika + 4;
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,$this->pika+4,'DEUDA  AGT');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,$this->pika+4,$saldo);
            $this->docexcel->getActiveSheet()->getStyle("B$cap:B$cap")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $deuda = $this->pika+4;
            $this->docexcel->getActiveSheet()->getStyle("A$deuda:B$deuda" )->applyFromArray($deudas);

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