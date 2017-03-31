<?php
// Extend the TCPDF class to create custom MultiRow
class RBoletoBRPDF extends  ReportePDF {
    var $datos_titulo;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $num_boleta=1;
    function Header() {


    }
    function Footer() {
        $this->setY(-15);
        $this->Cell($ancho, 0, $this->getAliasNumPage(), '', 0, 'C');

    }

    function generarReporte() {
        $this->setFontSubsetting(false);
        $this->SetLeftMargin(10);
        $this->SetRightMargin(5);

        $this->AddPage();

        $datos_maestro = $this->objParam->getParametro('datos_maestro');
        $datos_detalle = $this->objParam->getParametro('datos_detalle');


        $this->SetFont('Courier','B',10);


        $this->Cell(0,5,'BILHETE ELETRONICO',0,1,'C');
        $this->Cell(0,5,'RECIBO DE ITINERARIO DO PASSAGEIRO',0,1,'C');
        $this->ln();


        $this->Cell(70,5,'BOLIVIANA DE AVIACION',0,0,'L');
        $this->Cell(15,5,'DATA',0,0,'R');
        $this->Cell(80,5,': ' .$datos_maestro[0]['fecha_emision'],0,1,'L');
        $this->SetFont('Courier','',10);

        $y = $this->getY();
        $this->multiCell(70,5,$datos_maestro[0]['direccion'],0,'L');
        $this->setY($y);
        $this->setX(80);
        $this->Cell(15,5,'AGENTE',0,0,'R');
        $this->Cell(80,5,': ' . $datos_maestro[0]['codigo_punto_venta'],0,1,'L');
        $this->ln();

        $this->Cell(70,5,$datos_maestro[0]['nombre_punto_venta'],0,0,'L');
        $this->SetFont('Courier','B',10);
        $this->Cell(15,5,'NOME',0,0,'R');
        $this->Cell(80,5,': ' . $datos_maestro[0]['pasajero'],0,1,'L');
        $this->SetFont('Courier','',10);
        $this->Cell(70,5,'IATA: 56991266',0,1,'L');

        $this->Cell(70,5,'TELEFONE: ' . $datos_maestro[0]['telefono'],0,1,'L');

        $this->ln();

        $this->SetFont('Courier','B',10);
        $this->Cell(85,5,'NUMERO DO BILHETE',0,0,'L');
        $this->Cell(80,5,': ETKT ' .$datos_maestro[0]['nro_boleto'],0,1,'L');

        $this->Cell(85,5,'CODIGO DE RESERVA',0,0,'L');
        $this->Cell(80,5,': ' .$datos_maestro[0]['localizador'],0,1,'L');

        $this->ln();

        $this->Cell(40,5,'DE /PARA',0,0,'L');
        $this->Cell(25,5,'VOO',0,0,'L');
        $this->Cell(10,5,'CL',0,0,'L');
        $this->Cell(20,5,'SAI',0,0,'L');
        $this->Cell(20,5,'CHE',0,0,'L');
        $this->Cell(30,5,'BASE TARIFA',0,0,'L');
        $this->Cell(15,5,'MAL',0,0,'L');
        $this->Cell(10,5,'ST',0,0,'L');
        $this->Cell(15,5,'CX',0,1,'L');
        $this->SetFont('Courier','',10);
        $this->ln();
        $cantidad = 0;

        foreach ($datos_detalle as $value) {


            $y = $this->getY();
            $this->multiCell(40,5,$value['desde'] . ' - ',0,'L');
            $y2 = $this->getY();
            $this->setXY(50,$y);

            $this->Cell(25,5,$value['vuelo'],0,0,'L');
            $this->Cell(10,5,$value['clase'],0,0,'L');
            $this->Cell(20,5,$value['fecha_origen'],0,0,'L');
            $this->Cell(20,5,$value['fecha_destino'],0,0,'L');
            $this->Cell(30,5,$value['tarifa'],0,0,'L');
            $this->Cell(15,5,$value['equipaje'],0,0,'L');
            $this->Cell(10,5,$value['flight_status'],0,0,'L');

            if ($value['cupon'] != '1' && $value['retorno'] != 'si') {
                $this->Cell(15, 5, $value['conexion'], 0, 1, 'L');
            } else {
                $this->Cell(15, 5, '', 0, 1, 'L');
            }

            $this->setX(85);
            $this->Cell(20,5,$value['hora_origen'],0,0,'L');
            $this->Cell(20,5,$value['hora_destino'],0,1,'L');

            $this->setY($y2);
            $this->multiCell(40,5,$value['hacia'],0,'L');

            $this->ln();
            $cantidad++;
            if ($cantidad == 8) {
                $this->AddPage();
            }

        }

        $this->Cell(30,5,'ENDOSOS',0,0,'L');
        $this->Cell(100,5,': '. $datos_maestro[0]['endoso'],0,1,'L');

        $this->Cell(30,5,'COD DE VIAGEM',0,0,'L');
        $this->Cell(100,5,': ',0,1,'L');

        $this->Cell(30,5,'PAGAMENTO',0,0,'L');
        $this->Cell(100,5,': ' . $datos_maestro[0]['forma_pago'],0,1,'L');

        if ($cantidad == 7) {
            $this->AddPage();
        }

        $this->ln();

        $this->Cell(40,5,'CALCULO DA TARIFA',0,0,'L');
        $this->multiCell(150,5,': ' . $datos_maestro[0]['fare_calc'],0,'L');
        $this->Cell(40,5,'TARIFA AEREA',0,0,'L');
        $this->Cell(150,5,': ' . $datos_maestro[0]['neto'],0,1,'L');
        $this->Cell(40,5,'TAXA',0,0,'L');
        $this->Cell(150,5,': ' . $datos_maestro[0]['tasas_impuestos'] . ' (' . $datos_maestro[0]['detalle_tasas']  . ')',0,1,'L');

        $this->SetFont('Courier','B',10);
        $this->Cell(40,5,'TOTAL',0,0,'L');
        $this->Cell(150,5,': ' . $datos_maestro[0]['total'],0,1,'L');

        if ($cantidad == 6 || $cantidad == 5)  {
            $this->AddPage();
        }

        $this->ln();

        $this->SetFont('Courier','',10);

        $this->Cell(50,5,'VALIDADE DO BILHETE',0,0,'L');
        $this->Cell(150,5,': 1 ANO',0,1,'L');

        $this->Cell(50,5,'VALIDADE FARE',0,0,'L');
        $this->Cell(150,5,': ',0,1,'L');

        $impuestos = $datos_maestro[0]['origen']=='BO'?' + IMPOSTOS BOLIVIA':'';

        $this->Cell(50,5,'REEMBOLSO',0,0,'L');
        $this->Cell(150,5,': PENA 60 USD' . $impuestos ,0,1,'L');

        $this->Cell(50,5,'NO SHOW',0,0,'L');
        $this->Cell(150,5,': MULTAS 50 USD',0,1,'L');

        $this->Cell(50,5,'MUDAR DATA (EXCHANGE)',0,0,'L');
        $this->Cell(150,5,': AJUSTE DA TAXA + PENA 50 USD',0,1,'L');

        $this->Cell(50,5,'TEMPOS DE CONEXÃO',0,0,'L');
        $this->Cell(150,5,': '.$datos_maestro[0]['conexion'],0,1,'L');

        $this->Cell(50,5,'BAGAGEM DE MÃO',0,0,'L');
        $this->Cell(150,5,': 1 PARTE 7 KG',0,1,'L');

        $this->Cell(50,5,'BAGAGEM REGISTADA',0,0,'L');
        $this->Cell(150,5,': 1 PARTE 30 KG',0,1,'L');










    }

}
?>