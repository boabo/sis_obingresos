<?php
// Extend the TCPDF class to create custom MultiRow
class RBoletoBOPDF extends  ReportePDF {
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
        $this->SetTopMargin(15);
        $this->SetRightMargin(10);

        $this->AddPage();

        //$datos_maestro = $this->objParam->getParametro('datos_maestro');

        $datos_detalle = $this->objParam->getParametro('datos_detalle');
        $datos = $this->objParam->getParametro('datos');
        //var_dump($datos[0]);exit;
        //var_dump($datos_detalle);exit;
        //var_dump($datos_detalle->elementosTkt->fns->fn_V2->codigo_tarifa->string);exit;

        $this->SetFont('Courier','B',10);


        $this->Cell(0,5,'BILLETE ELECTRONICO',0,1,'C');
        $this->Cell(0,5,'RECIBO DE ITINERARIO DEL PASAJERO',0,1,'C');
        $this->ln();

        $this->SetFont('Courier','',10);
        $this->Cell(70,5,$datos[0]['nombre_ofi'],0,0,'L');
        $this->Cell(15,5,'FECHA',0,0,'R');
        $this->Cell(80,5,': ' . date_format(date_create($datos[0]['fecha_create']), 'd/m/Y'),0,1,'L');
        $this->SetFont('Courier','',10);

        $y = $this->getY();
        $this->multiCell(70,5,$datos[0]['direccion_ofi'],0,'L');
        $this->setY($y);
        $this->setX(80);
        $this->Cell(15,5,'AGENTE',0,0,'R');
        $this->Cell(80,5,': '.$datos[0]['agente'],0,1,'L');
        $this->ln();
        $this->setX(80);
        //$this->Cell(70,5,$datos[0]['direccion_ofi'],0,0,'L');
        $this->SetFont('Courier','',10);
        $this->Cell(15,5,'NOMBRE',0,0,'R');
        $this->Cell(80,5,': ' . $datos_detalle->pasajeros->pasajeroDR->apdos_nombre,0,1,'L');
        $this->SetFont('Courier','',10);
        $this->Cell(70,5,'IATA: '.$datos[0]['codigo_iata'],0,0,'L');

        $this->SetFont('Courier','B',10);
        $this->Cell(15,5,'NIT',0,0,'R');
        $this->Cell(80,5,': ' . substr($datos[0]['endoso'], 2),0,1,'L');

        $this->SetFont('Courier','',10);

        $this->Cell(70,5,'TELEFONO: ' . $datos[0]['telefono_ofi']/*$datos_maestro[0]['telefono']*/,0,0,'L');
//var_dump($datos_detalle->pasajeros->pasajeroDR->Tkts->string);exit;

        $this->SetFont('Courier','',10);
        $this->Cell(15,5,'',0,0,'R');
        $this->Cell(80,5,'',0,1,'L');
        $this->ln();
        $this->Cell(85,5,'COMPANIA EMISORA',0,0,'L');
        $this->Cell(80,5,': BOLIVIANA DE AVIACION',0,1,'L');

        $this->Cell(85,5,'NIT',0,0,'L');
        $this->Cell(80,5,': 154422029',0,1,'L');

        $this->SetFont('Courier','',10);
        $this->Cell(85,5,'NUMERO DE BILLETE',0,0,'L');
        $this->Cell(80,5,': ETKT ' .$datos_detalle->pasajeros->pasajeroDR->Tkts->string,0,1,'L');


        $this->Cell(85,5,'FORMA DE IDENTIFICACIÓN',0,0,'L');
        $this->Cell(80,5,': NATIONAL IDENTITY ' .$datos[0]['forma_identificacion'],0,1,'L');

        $this->Cell(85,5,'LOC. RESERVA',0,0,'L');
        $this->Cell(80,5,': ' .$datos_detalle->localizador_resiber,0,1,'L');

        $this->ln();

        $this->Cell(40,5,'DE /PARA',0,0,'L');
        $this->Cell(15,5,'VUELO',0,0,'L');
        $this->Cell(10,5,'CL',0,0,'L');
        $this->Cell(17,5,'SALIDA',0,0,'L');
        $this->Cell(17,5,'LLEGADA',0,0,'L');

        //$this->Cell(10,5,'CHE',0,0,'L');
        $this->Cell(25,5,'BASE TARIFA',0,0,'L');
        $this->Cell(10,5,'NVA',0,0,'L');
        $this->Cell(15,5,'NVD',0,0,'L');
        $this->Cell(15,5,'BAG',0,0,'L');
        $this->Cell(15,5,'ST',0,0,'L');

        $this->SetFont('Courier','',10);
        $this->ln();
        $cantidad = 0;
        $validez = $datos_detalle->tl->fecha_limite;
        $tipo_vuelo = 'nacional';
        $solo_y = 'si';
        $solo_b = 'si';
        $solo_yb = 'si';
        $contador_tarifa = 0;
        $contador_vuelo = 0;
        //var_dump($datos[0]['cadena_tasa']);exit;
        foreach ($datos as $value) {

            //if(gettype($value)==='object'){
                if ($validez != $value['fecha_limite']) {
                    $validez = 'combinability';
                }

                /*if ($value['pais_origen'] != 'BO' || $value['pais_destino'] != 'BO') {
                    $tipo_vuelo = 'internacional';
                }*/

                $y = $this->getY();
                $this->multiCell(40,5,$value['origen'] ,0,'L');
                $y2 = $this->getY();
                $this->setXY(50,$y);

                $this->Cell(15,5,$value['linea'].$value['num_vuelo'],0,0,'L');
                $this->Cell(10,5,$value['clase'],0,0,'L');
                $this->Cell(17,5,$value['fecha_salida'],0,0,'L');
                $this->Cell(17,5,$value['fecha_salida'],0,0,'L');
                $this->Cell(25,5,$value['codigo_tarifa'],0,0,'L');
                $this->Cell(10,5,''/*$value['equipaje']*/,0,0,'L');
                $this->Cell(15,5,$value['fecha_salida']/*$value['equipaje']*/,0,0,'L');
                $this->Cell(15,5,'20K'/*$value['equipaje']*/,0,0,'L');
                $this->Cell(15,5,$value['estado'],0,0,'L');

                /*if ($value['cupon'] != '1' && $value['retorno'] != 'si') {
                    $this->Cell(15, 5, $value['conexion'], 0, 1, 'L');
                } else {
                    $this->Cell(15, 5, '', 0, 1, 'L');
                }*/

                $this->setX(75);
                $this->Cell(17,15,$value['hora_salida'],0,0,'L');
                $this->Cell(17,15,$value['hora_llegada'],0,1,'L');

                $this->setY($y2);
                $this->multiCell(40,5,$value['destino'],0,'L');

                $this->ln();
                $cantidad++;
                if ($cantidad == 8) {
                    $this->AddPage();
                }

                if ($value['clase'] != 'Y') {
                    $solo_y = 'no';
                }

                if ($value['clase'] != 'B') {
                    $solo_b = 'no';
                }

                if ($value['clase'] != 'B' && $value['clase'] != 'Y') {
                    $solo_yb = 'no';
                }
            /*}else if(gettype($value)==='array'){

                if ($value['pais_origen'] != 'BO' || $value['pais_destino'] != 'BO') {
                    $tipo_vuelo = 'internacional';
                }

                $y = $this->getY();
                $this->multiCell(40,5,$value['desde'] ,0,'L');
                $y2 = $this->getY();
                $this->setXY(50,$y);

                $this->Cell(25,5,$value['vuelo'],0,0,'R');
                $this->Cell(10,5,$value['clase'],0,0,'C');
                $this->Cell(20,5,$value['fecha_origen'],0,0,'L');
                $this->Cell(20,5,$value['fecha_destino'],0,0,'L');
                $this->Cell(30,5,$datos_detalle->elementosTkt->fns->fn_V2->codigo_tarifa->string[$contador_tarifa],0,0,'L');
                $contador_tarifa++;
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

                if ($value['clase'] != 'Y') {
                    $solo_y = 'no';
                }

                if ($value['clase'] != 'B') {
                    $solo_b = 'no';
                }

                if ($value['clase'] != 'B' && $value['clase'] != 'Y') {
                    $solo_yb = 'no';
                }
            }*/

        }//var_dump($contador);exit;

        $this->SetFont('Courier','',9);

        $this->Cell(45,5,'EN FACTURACIÓN, DEBERA PRESENTAR UN DOCUMENTO DE IDENTIDAD CON IDENTIDAD CON FOTOGRAFIA Y',0,1,'L');
        $this->Cell(100,5,'EL DOCUMENTO USADO COMO REFERENCIA AL HACER LA RESERVA.',0,1,'L');

        /*if($datos[0]['endoso'] != null || $datos[0]['endoso'] != '') {
            $this->Cell(45, 5, 'ENDOSO', 0, 0, 'L');
            $this->Cell(100, 5, ': ' . $datos[0]['endoso'], 0, 1, 'L');
        }*/

        if(($datos[0]['tipo_cambio'] != null || $datos[0]['tipo_cambio'] != '') && $datos[0]['moneda_tarifa']=='USD') {
            $this->Cell(45, 5, 'TARIFA DE INTERCAMBIO', 0, 0, 'L');
            $this->Cell(100, 5, ': ' .number_format($datos[0]['tipo_cambio'], 2, ',', '.'), 0, 1, 'L');
        }

        if ($cantidad == 7) {
            $this->AddPage();
        }

        $this->ln();

        $this->Cell(45,5,'CALCULO DE TARIFA',0,0,'L');
        $this->Cell(3,5,':',0,0,'R');
        $this->multiCell(150,5, $datos_detalle->elementosTkt->fcs->fc->texto.$datos[0]['calculo_tarifa'],0,'L');
        $this->Cell(45,5,'TARIFA AEREA',0,0,'L');
        $this->Cell(150,5,': ' . $datos[0]['importe_tarifa'] ,0,1,'L');
        $this->Cell(45,5,'TASA',0,0,'L');
        $this->Cell(150,5,': ' . $datos[0]['tasa']/*$datos_maestro[0]['tasas_impuestos']*/ /*. ' (' . 'Detalle Tasa' $datos_maestro[0]['detalle_tasas']  . ')'*/,0,1,'L');

        $rc_iva = $datos[0]['rc_iva']<0?0:$datos[0]['rc_iva'];
        $this->Cell(45,5,'SUBJECT TO TAX ',0,0,'L');
        $this->Cell(150,5,': '.$datos[0]['moneda_iva'].' ' .$rc_iva,0,1,'L');
        $this->Cell(150,5,'CREDIT (T-IVA)' ,0,1,'L');
        $this->SetFont('Courier','B',10);
        $this->Cell(45,5,'TOTAL',0,0,'L');
        $total_tarifa = $datos_detalle->elementosTkt->fns->fn_V2->importe_tarifa  + $datos[0]['rc_iva'];
        $this->Cell(150,5,': ' .$datos[0]['importe_total']/*$datos_maestro[0]['total']*/,0,1,'L');

        if ($cantidad == 6 || $cantidad == 5)  {
            $this->AddPage();
        }

        $this->ln();

        $this->SetFont('Courier','',8);
        //////texto
        /*$this->Cell(45,5,'AVISO',0,1,'L');
        $this->Cell(45,5,'EL TRANSPORTE Y OTROS SERVICIOS PROVISTOS POR LA COMPAÑÍA ESTÁN SUJETOS A LAS
                            CONDICIONES DE TRANSPORTE, LAS CUÁLES SE INCORPORAN POR REFERENCIA. ESTAS
                            CONDICIONES PUEDEN SER OBTENIDAS DE LA COMPAÑÍA EMISORA.
                            ' . $validez,0,1,'L');
        $this->Cell(45,5,'EL ITINERARIO/RECIBO CONSTITUYE EL BILLETE DE PASAJE A EFECTOS DEL ARTÍCULO 3
                            DE LA CONVENCIÓN DE VARSOVIA, A MENOS QUE EL TRANSPORTISTA ENTREGUE AL
                            PASAJERO OTRO DOCUMENTO QUE CUMPLA CON LOS REQUISITOS DEL ARTÍCULO 3.
                            ' . $validez,0,1,'L');
        $this->Cell(45,5,'SE INFORMA A LOS PASAJEROS QUE REALICEN VIAJES EN LOS QUE EL PUNTO DE DESTINO
                            O UNA O MAS ESCALAS INTERMEDIAS SE EFECTUEN EN UN PAIS QUE NO SEA EL DE
                            PARTIDA DE SU VUELO, QUE PUEDEN SER DE APLICACION A LA TOTALIDAD DE SU VIAJE,
                            INCLUIDA CUALQUIER PARTE DEL MISMO DENTRO DE UN PAIS, LOS TRATADOS
                            INTERNACIONALES COMO LA CONVENCION DE MONTREAL O SU PREDECESOR LA CONVENCION
                             DE VARSOVIA, INCLUYENDO SUS MODIFICACIONES (EL SISTEMA DE CONVENCION DE
                            VARSOVIA).  EN EL CASO DE AQUELLOS PASAJEROS, EL TRATADO APLICABLE, INCLUYENDO
                            LAS CONDICIONES ESPECIALES DEL TRANSPORTE INCORPORADAS A CUALQUIER TARIFA
                            APLICABLE, RIGE Y PUEDE LIMITAR LA RESPONSABILIDAD DEL TRANSPORTISTA EN CASOS
                            DE MUERTE O LESIONES PERSONALES, PERDIDA O DANOS AL EQUIPAJE Y RETRASOS.
                            ' . $validez,0,1,'L');
        $this->Cell(45,5,'EL TRANSPORTE DE MATERIALES PELIGROSOS TALES COMO AEROSOLES, FUEGOS
                            ARTIFICIALES Y LÍQUIDOS INFLAMABLES A BORDO DEL AVIÓN QUEDA ESTRICTAMENTE
                            PROHIBIDO. SI USTED NO COMPRENDE ESTAS RESTRICCIONES, SÍRVASE OBTENER MAYOR
                            INFORMACIÓN A TRAVÉS DE SU COMPAÑÍA AÉREA.
                            ' . $validez,0,1,'L');*/

        ////////texto

        $this->Cell(45,5,'VALIDEZ DEL BILLETE',0,0,'L');
        $this->Cell(150,5,': 1 AñO',0,1,'L');

        if ($validez == '1') {
            $validez = '1 MES';
        } else if($validez == 'combinability') {
            $validez = 'EN CASO DE DOS O MÁS CLASES DE RESERVA, LA REGLA MÁS RESTRICTIVA SERÁ APLICADA';
        } else {
            $validez = $validez . ' MESES';
        }

        $this->Cell(45,5,'VALIDEZ DE LA TARIFA',0,0,'L');
        $this->Cell(150,5,': ' . $validez,0,1,'L');

        $impuestos = /*$datos_maestro[0]['origen']*/'BO'=='BO'?' +  IMPUESTO BOLIVIANO RETENIDO':'';

        $this->Cell(45,5,'REEMBOLSO',0,0,'L');
        $this->Cell(3,5,':',0,0,'R');
        if ($tipo_vuelo == 'nacional') {
            if ($solo_y == 'si') {
                $this->MultiCell(150, 5, 'NO HAY MULTA ' . $impuestos . "\n", 0, 'J');
            } else {
                $this->MultiCell(150, 5, 'EL BILLETE CON TARIFA A PARTIR DE USD71.00 LA MULTA ES DE USD30.00 Y EL BILLETE CUYA TARIFA ES MENOR DE USD71.00 LA MULTA ES DE USD20.00 ' . $impuestos . "\n", 0, 'J');
            }

        } else {
            $this->Cell(150, 5, 'MULTA USD60.00 ' . $impuestos, 0,1, 'L');
        }
        $this->Cell(45,5,'NO SHOW',0,0,'L');
        if ($tipo_vuelo == 'nacional') {
            $this->Cell(150,5,': MULTA USD5.00',0,1,'L');
        } else {
            $this->Cell(150,5,': MULTA USD50.00',0,1,'L');
        }


        $this->Cell(45,5,'ALTERACIÓN DE FECHA O RUTA',0,0,'L');
        $this->Cell(3,5,':',0,0,'R');
        if ($tipo_vuelo == 'nacional') {
            if ($solo_y == 'si' || $solo_b == 'si' || $solo_yb == 'si') {
                $this->MultiCell(145,5,'NO HAY MULTA.' . "\n",0,'J');
            } else {
                $this->MultiCell(145,5,'MULTA USD5.00 DE ACUERDO A DISPONIBILIDAD, EN CASO DE TARIFA SUPERIOR SERÁ COBRADA LA DIFERENCIA + MULTA USD5.00 ' . $impuestos . "\n",0,'J');
            }
        } else {
            $this->MultiCell(145,5,'MULTA USD50.00 DE ACUERDO A DISPONIBILIDAD, EN CASO DE TARIFA SUPERIOR SERÁ COBRADA LA DIFERENCIA + MULTA USD50.00 ' . $impuestos . "\n",0,'J');
        }




        $this->Cell(45,5,'CAMBIO DE NOMBRE',0,0,'L');
        $this->Cell(3,5,':',0,0,'R');
        $this->MultiCell(145,5,'PERMITIDA DESDE QUE NO CAMBIE EL CARÁCTER PERSONAL DEL PASAJERO, EL BILLETE ES INTRANSFERIBLE.',0,'J');

        $this->Cell(45,5,'ARREPENTIMIENTO',0,0,'L');
        $this->Cell(3,5,':',0,0,'R');
        $this->MultiCell(145,5,'ES PERMITIDO DESISTIR DE LA COMPRA DEL PASAJE AÉREO SIN CUALQUIER AUTO, DESDE QUE LO HAGA EN EL PLAZO DE HASTA 24HS, A CONTAR DE LA FECHA DEL RECEPCIÓN DEL COMPROBANTE. La REGLA APLICABLE PARA COMPRAS HECES CON ANTECEDENCIA IGUAL O SUPERIOR A 7 (SIETE) DÍAS EN RELACIÓN A LA FECHA DE EMBARQUE. ULTRAPASADO ESTE PLAZO, APLICABLE MULTA DE LA REGLA ARANCELARIA.',0,'J');

        $this->Cell(45,5,'EQUIPAJE DE MANO',0,0,'L');
        if ($tipo_vuelo == 'nacional') {
            $this->Cell(150, 5, ': 1 PIEZA 5 KG', 0, 1, 'L');
        } else {
            $this->Cell(150, 5, ': 1 PIEZA 7 KG', 0, 1, 'L');
        }

        $this->Cell(45,5,'BAGAJE DESPACHADA',0,0,'L');

        if ($tipo_vuelo == 'nacional') {
            $this->Cell(150, 5, ': 1 PIEZA 20 KG', 0, 1, 'L');
        } else {
            $this->Cell(150, 5, ': 1 PIEZA 30 KG', 0, 1, 'L');
        }


    }

}
?>