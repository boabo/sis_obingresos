<?php
class REMcos extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
        var $codigo;

    function Header() {
        $this->Ln(3);
        $this->SetMargins(3, 53, 5,false);
		    $fecha_ini = $this->objParam->getParametro('fecha_ini');
        $fecha_fin = $this->objParam->getParametro('fecha_fin');

        $agencia='<span style="color:#274d80;">'.$this->objParam->getParametro('punto_venta_nombre').'</span>';
        $codigo_agencia='<span style="color:#274d80;">'.$this->objParam->getParametro('codigo_punto_venta').'</span>';
        if($this->objParam->getParametro('filtro_mes')=='true'){
          $fecha = "DEL: ".$fecha_ini."                       AL: ".$fecha_fin;
        }else{
          $fecha = "DEL: ".$fecha_ini;
        }

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 16,5,40,20);
        $this->ln(5);
        $this->SetFont('','B', 20);
        // $this->SetTextColor(43,	67,	100);
        $this->Cell(0,5,"\t\t\t"."REPORTE  DE  EMISION  DE  MCOs",0,1,'C');
        $this->SetFont('','B', 11);
        $this->Cell(0,5,$fecha, 0,1,'C');
        $this->Ln(4);

        $tbl = '<table border="0" style="font-size: 10pt;">
                <tr>
                    <td width="100%"><span style="color:black;">Sistema de Ingresos</span></td>
                </tr>
                <tr>
                    <td width="30%" height="35px">Agencia: '.$codigo_agencia.' '.$agencia.'</td>
                </tr>
                </table>
                ';

        $this->writeHTML ($tbl);
        $this->Ln(-1);
        $this->generarCabecera();

    }
    function generarCabecera(){

        $this->SetFont('','B',8);
        $this->tablewidthsHD=array(30,18,20,25,30,25,30,50,20,20);
        $this->tablealignsHD=array('C','C','C','C','C','C','C','C','C','C');
        $this->tablenumbersHD=array(0,0,0,0,0,0,0,0,0,0);
        // $this->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(0, 0, 0)));
        $this->tablebordersHD=array('TB','TB','TBL','TBL','TBL','TBL','TBL','TBL','TBL','TBL');
        $this->tabletextcolorHD=array();
        $RowArray = array(
                        's1' => 'Cajero',
                        's2' => 'Fecha',
                        's3' => 'T-Concepto',
                        's4' => 'Nro. MCO',
                        's5' => 'Nombre del Pasajero',
                        's6' => 'Canje TKT/MCO',
                        's7' => 'Emitido por',
                        's8' => 'Motivo de Emision',
                        's9' => 'Importe-M/L',
                        's10' => 'Importe-USD');

         $this->MultiRowHeader($RowArray,false,1);
    }

    function setDatos($datos) {
        $this->datos = $datos;
        // var_dump($this->datos);exit;
    }
    function generarReporte() {

        $this->AddPage();
        $this->SetMargins(3, 0, 5,false);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // $this->Ln(0.5);
        $this->Ln();
        $this->SetFont('','',7);
        $totales = 0;
        foreach( $this->datos as $record){
        $this->tablewidthsHD=array();
            $this->tableborders=array('LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB');
            // $this->tableborders=array('','','','','','','','','');
            $this->tablewidths=array(30,18,20,25,30,25,30,50,20,20);
            $this->tablealigns=array('L','L','C','L','L','L', 'L','L','R','R');
            $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0);
            $monto = number_format($record['valor_total'], 2, ',','.');
            if ($record['codi_moneda'] == 'BOB') {
                  $valor_total_boli = $monto;
                  $valor_total_dol = 0.00;
            } else {
                  $valor_total_dol = $monto;
                  $valor_total_boli = 0.00;
            }

            $RowArray = array(
                       's1' => $record['cajero'],
                       's2' => date('d/m/Y', strtotime($record['fecha_emision'])),
                       's3' => $record['t_concepto'],
                       's4' => $record['nro_mco'],
                       's5' => $record['pax'],
                       's6' => $record['tkt'],
                       's7' => $record['desc_funcionario2'],
                       's8' => $record['motivo'],
                       's9' => $valor_total_boli,
                       's10' => $valor_total_dol
                        );
            $this-> MultiRow($RowArray,false,1);
            $totales += $record['valor_total'];
        }
        $RowArray = array(
           's1' => '',
           's2' => '',
           's3' => '',
           's4' => '',
           's5' => '',
           's6' => '',
           's7' => '',
           's8' => 'TOTALES',
           's9' => number_format($totales, 2, ',', '.'),
           's10' => 0.00
          );

        $this->SetFont('','B',9);
        $this->tableborders=array('LTB','LTB','B','B','B','B','B','LRTB','LRTB','LRTB');
        $this->tablealigns=array('L','L','C','L','L','L', 'L','L','R','R');
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0);
        $this-> MultiRow($RowArray,$fill,1);

    }

}
?>
