<?php
class RReporteConciliacionCC
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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'CONCILIACION CUENTAS CORRIENTES DEL '. $this->objParam->getParametro('fecha_ini') . ' AL '. $this->objParam->getParametro('fecha_fin') .  '(' . $this->objParam->getParametro('reporte').')');

        
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(19);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(19);
		$this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(19);
		$this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(19);
		$this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(19);

		if ($this->objParam->getParametro('reporte') == 'diferencia_anulacion') {
			$this->docexcel->getActiveSheet()->setCellValue('A5','Autorizacion');
	        $this->docexcel->getActiveSheet()->setCellValue('B5','Billete');
	        $this->docexcel->getActiveSheet()->setCellValue('C5','Void');
	        $this->docexcel->getActiveSheet()->setCellValue('D5','Fecha');
			$this->docexcel->getActiveSheet()->setCellValue('E5','Agencia');
			$this->docexcel->getActiveSheet()->setCellValue('F5','Monto Movimiento');
			$this->docexcel->getActiveSheet()->setCellValue('G5','Monto Boleto');	
		} else if ($this->objParam->getParametro('reporte') == 'diferencia_pnr_vs_boletos') {
			$this->docexcel->getActiveSheet()->setCellValue('A5','pnr');
			$this->docexcel->getActiveSheet()->setCellValue('B5','Autorizacion');	        
	        $this->docexcel->getActiveSheet()->setCellValue('C5','Total PNR');
	        $this->docexcel->getActiveSheet()->setCellValue('D5','Total Boletos');
			$this->docexcel->getActiveSheet()->setCellValue('E5','Fecha');	
		}

        $this->docexcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:D2');
        

        $this->docexcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleTitulos2);


    }
    function reporteDiferenciaAnulacion()
    {  
        $fila = 6;
        $this->docexcel->setActiveSheetIndex(0);
		$datos = $this->objParam->getParametro('datos_archivo');
        for ($i=0; $i<count($datos);$i++) {  
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $datos[$i]['autorizacion']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $datos[$i]['billete']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $datos[$i]['void']);
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $datos[$i]['fecha']);
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $datos[$i]['codigo_int']);
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $datos[$i]['monto_movimiento']);
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $datos[$i]['monto_boleto']);
            $fila++;
			
        }
        $this->docexcel->setActiveSheetIndex(0);
    }
	
	function reporteDiferenciaPnrsBoletos()
    {  
        $fila = 6;
        $this->docexcel->setActiveSheetIndex(0);
		$datos = $this->objParam->getParametro('datos_archivo');
		
        for ($i=0; $i<count($datos);$i++) {  
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $datos[$i]['pnr']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $datos[$i]['autorizacion']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $datos[$i]['total_pnr']);
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $datos[$i]['total_boletos']);
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $datos[$i]['fecha']);
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