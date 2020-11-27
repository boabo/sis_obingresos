<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/mypdf.php';
require_once dirname(__FILE__).'/../../pxp/pxpReport/Report.php';

class CustomReportLibroBancos extends MYPDF {


    private $dataSource;

    public function setDataSource(DataSource $dataSource) {
        $this->dataSource = $dataSource;
        //var_dump($this->dataSource);exit;
    }

    public function getDataSource() {
        return $this->dataSource;
    }

    public function Header() {

        $height = 20;
        $height2 = 4;

        $fecha_desde = $this->getDataSource()->getParameter('fecha_desde');
        $fecha_hasta = $this->getDataSource()->getParameter('fecha_hasta');
        $tipo_generacion = strtoupper($this->getDataSource()->getParameter('tipo_generacion'));

        $this->Image(dirname(__FILE__).'/../../pxp/lib'.$_SESSION['_DIR_LOGO'], 10, 10, 36);
        $this->Cell(20, $height, '', 0, 0, 'C', false, '', 1, false, 'T', 'C');

        $this->SetFontSize(16);
        $this->SetFont('','B');

        $this->Cell(230, $height2, 'CONTROL DE CORRELATIVIDAD', 0, 0, 'C', false, '', 1, false, 'T', 'C');

        //var_dump('reporte', $this->getDataSource()->getParameter('id_punto_venta')); exit;
        $this->Ln();
        $this->SetFontSize(11);
        $this->Ln(3.5);
        $this->Cell(265, $height2, 'Del '.$fecha_desde. ' al '.$fecha_hasta, 0, 0, 'C', false, '', 1, false, 'T', 'C');
        $this->Ln();
        //$this->Ln(3.5);

        if ($tipo_generacion =='RECIBO' ){

            $this->Cell(265, $height2, 'Tipo: '.$tipo_generacion, 0, 0, 'C', false, '', 1, false, 'T', 'C');

            $this->Ln();
            $this->Ln();

            $width1 = 25;
            $width2 = 70;
            $width3 = 40;
            $width4 = 30;
            $width5 = 30;
            $width6 = 30;
            $width7 = 70;

            $height = 5;
            $blackAll = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $this->SetFillColor(224,224,224, true);
            $this->setTextColor(0,0,0);


                $this->Cell($width1, $height, 'Estación', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
                $this->Cell($width7, $height, 'Sucursal', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
                $this->Cell($width2, $height, 'Punto de Venta', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
                $this->Cell($width4, $height, 'Del Número', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
                $this->Cell($width5, $height, 'Al Número', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
                $this->Cell($width6, $height, 'Cantidad', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');

        }else{//FACTURAS
            $this->Cell(265, $height2, 'Tipo: FACTURACION '.$tipo_generacion, 0, 0, 'C', false, '', 1, false, 'T', 'C');

            $this->Ln();
            $this->Ln();

            $width1 = 18;
            $width2 = 65;
            $width3 = 40;
            $width4 = 25;
            $width5 = 25;
            $width6 = 25;
            $width7 = 65;

            $height = 5;
            $blackAll = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $this->SetFillColor(224,224,224, true);
            $this->setTextColor(0,0,0);


            $this->Cell($width1, $height, 'Estación', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
            $this->Cell($width7, $height, 'Sucursal', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
            $this->Cell($width2, $height, 'Punto de Venta', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
            $this->Cell($width3, $height, 'Nro Autorización', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
            $this->Cell($width4, $height, 'Del Número', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
            $this->Cell($width5, $height, 'Al Número', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');
            $this->Cell($width6, $height, 'Cantidad', $blackAll, 0, 'C', true, '', 1, false, 'T', 'C');

        }


    }

    public function Footer() {
        $this->SetFontSize(7);
        $this->setY(-10);
        $ormargins = $this->getOriginalMargins();
        $this->SetTextColor(0, 0, 0);
        //set style for cell border
        $line_width = 0.85 / $this->getScaleFactor();
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
        $this->Ln(2);
        $cur_y = $this->GetY();
        //$this->Cell($ancho, 0, 'Generado por XPHS', 'T', 0, 'L');
        $this->Cell($ancho, 0, 'Usuario: '.$_SESSION['_LOGIN'], '', 0, 'L');
        $pagenumtxt = 'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        //$fecha_rep = date("d-m-Y H:i:s");
        //$nuevafecha = strtotime ( '-10 days' , strtotime ($fecha_rep) ) ;
        //$fecha_rep = date("d-m-Y H:i:s", $nuevafecha);
        //$this->Cell($ancho, 0, "Fecha impresión: ".$fecha_rep, '', 0, 'R');
        $this->Ln($line_width);
    }
}


Class RCorrelativoFac extends Report {

    function write($fileName) {
        $pdf = new CustomReportLibroBancos('P', PDF_UNIT, "LETTER", true, 'UTF-8', false);
        $pdf->setDataSource($this->getDataSource());
        // set document information
        $pdf->SetCreator(PDF_CREATOR);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings

        // add a page
        //$pdf->AddPage();

        $pdf->AddPage('L', 'A4');

        $this->writeDetalles($this->getDataSource(), $pdf);

        $pdf->Output($fileName, 'F');
    }

    function writeDetalles (DataSource $dataSource, TCPDF $pdf) {
        $blackAll = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $widthMarginLeft = 1;
        $tipo_generacion = strtoupper($this->getDataSource()->getParameter('tipo_generacion'));

        if ($tipo_generacion =='RECIBO' ){

            $width1 = 25;
            $width2 = 70;
            $width3 = 40;
            $width4 = 30;
            $width5 = 30;
            $width6 = 30;
            $width7 = 70;

            $pdf->Ln();
            $pdf->SetFontSize(7.5);
            $pdf->SetFont('', 'B');
            //$height = 5;
            $pdf->SetFillColor(224,224,224, true);
            $pdf->setTextColor(0,0,0);

            $pdf->Ln();
            $pdf->SetFontSize(8);
            $pdf->SetFont('dejavusans','');


            $pdf->SetFillColor(255,255,255, true);
            $pdf->tablewidths=array($width1,$width7,$width2,$width4,$width5,$width6);
            $pdf->tablealigns=array('C','C','C','C','C','C');
            $pdf->tablenumbers=array(0,0,0,0,0,0);


           // $RowArray;
            foreach($dataSource->getDataset() as $row) {

                    $RowArray = array(
                        'estacion'  =>  $row['estacion'],
                        'sucursal'  =>  $row['sucursal'],
                        'punto_venta'  => $row['punto_venta'],
                        'nro_desde' => $row['nro_desde'],
                        'nro_hasta' => $row['nro_hasta'],
                        'cantidad' => $row['cantidad']
                    );

                $pdf-> MultiRow($RowArray, $fill = false, $border = 1) ;

            }

        }else{ //FACTURAS
            $width1 = 18;
            $width2 = 65;
            $width3 = 40;
            $width4 = 25;
            $width5 = 25;
            $width6 = 25;
            $width7 = 65;

            $pdf->Ln();
            $pdf->SetFontSize(7.5);
            $pdf->SetFont('', 'B');
            //$height = 5;
            $pdf->SetFillColor(224,224,224, true);
            $pdf->setTextColor(0,0,0);

            $pdf->Ln();
            $pdf->SetFontSize(8);
            $pdf->SetFont('dejavusans','');


            $pdf->SetFillColor(255,255,255, true);
            $pdf->tablewidths=array($width1,$width7,$width2,$width3,$width4,$width5,$width6);
            $pdf->tablealigns=array('C','C','C','C','C','C','C');
            $pdf->tablenumbers=array(0,0,0,0,0,0,0);


            // $RowArray;
            foreach($dataSource->getDataset() as $row) {

                $RowArray = array(
                    'estacion'  =>  $row['estacion'],
                    'sucursal'  =>  $row['sucursal'],
                    'punto_venta'  => $row['punto_venta'],
                    'nroaut'  => $row['nroaut'],
                    'nro_desde' => $row['nro_desde'],
                    'nro_hasta' => $row['nro_hasta'],
                    'cantidad' => $row['cantidad']
                );

                $pdf-> MultiRow($RowArray, $fill = false, $border = 1) ;

            }

        }
        //$pdf->SetFont('','B');

    }
}
?>