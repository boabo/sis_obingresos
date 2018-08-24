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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,1,'ESTADO DE CUENTA' );
        $this->docexcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A1:K1');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'(Expresado Bolivianos)' );
        $this->docexcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A2:K2');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'Hasta: '. $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A3:K3');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,4,'Fecha Reg: '. date("d-m-Y H:i:s"));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,4,'Tipo Agencia: '. $this->objParam->getParametro('tipo_agencia'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,4,'Forma de Pago: '. $this->objParam->getParametro('forma_pago'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,4,'Por: '. $_SESSION['_LOGIN']);
        $this->docexcel->getActiveSheet()->getStyle('A4:K4')->applyFromArray($styleTitulos);
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

        $this->docexcel->getActiveSheet()->setCellValue('A6','NRO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','NOMBRE');
        $this->docexcel->getActiveSheet()->setCellValue('C6','OFFICELD');
        $this->docexcel->getActiveSheet()->setCellValue('D6','TIPO AGENCIA');
        $this->docexcel->getActiveSheet()->setCellValue('E6','FORMA DE PAGO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','CIUDAD');
        $this->docexcel->getActiveSheet()->setCellValue('G6','CREDITOS');
        $this->docexcel->getActiveSheet()->setCellValue('H6','GARANTIA');
        $this->docexcel->getActiveSheet()->setCellValue('I6','DEBITOS');
        $this->docexcel->getActiveSheet()->setCellValue('J6','AJUSTES');
        $this->docexcel->getActiveSheet()->setCellValue('K6','SALDOS');
        $this->docexcel->getActiveSheet()->getStyle('A6:K6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->getStyle('A6:K6')->getAlignment()->setWrapText(true);


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
        $numero = 1;
        $datos = $this->objParam->getParametro('datos');
        //var_dump($datos);exit;
        foreach ($datos as $value) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nombre']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['codigo_int']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['tipo_agencia']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['formas_pago']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['codigo_ciudad']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_creditos']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['garantia']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['monto_debitos']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_ajustes']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['saldo']);
            $this->docexcel->getActiveSheet()->getStyle("G$fila:J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $numero++;
            $fila++;
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