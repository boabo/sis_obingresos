<?php

class RResumenEstadoCC extends  ReportePDF {
    private  $fun = array();

    function Header() {
        //$this->AddPage();
        $this->ln(5);
        $height = 20;
        $this->SetFontSize(15);
        $this->SetFont('','B');
        $this->MultiCell(0, $height,"\n".'RESUMEN ESTADO CUENTA CORRIENTE', 0, 'C', 0, '', '');

        $this->ln(20);
        $this->customy = $this->getY();
        $this->reporteRequerimiento();

    }

    function reporteRequerimiento()
    {
        foreach ($this->datos as $Row) {
            $this->SetFont('times', 'B', 14);
            if ($Row['tipo'] == 'boleta_garantia'){
                $tipo = 'Boleta Garantia';
            }elseif ($Row['tipo'] == 'saldo_anterior'){
                $tipo = 'Saldo Anterior';
            }elseif ($Row['tipo'] == 'comision'){
                $tipo = 'Comision';
            }elseif ($Row['tipo'] == 'boleto'){
                $tipo = 'Boleto';
            }
            $this->Cell(50, 7, $tipo, 0, 0, 'L', 0, '', 0);
            $this->Cell(60, 7, '' . number_format($Row['monto'], 2, ',', '.'), 0, 0, 'C', 0, '', 0);
            $this->Cell(0, 7, '' . $Row['moneda'], 0, 0, 'C', 0, '', 0);
            $this->ln();

        }

    }

    function setDatos($datos) {
        $this->datos = $datos;
    }
    function generarReporte() {
        $this->setFontSubsetting(false);
        $this->AddPage();
    }

}

?>