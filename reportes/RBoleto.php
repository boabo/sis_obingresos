<?php

class RBoleto
{
	function generarHtml ($datos) {			
			
			setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
				
			
			$html.='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
					   "http://www.w3.org/TR/html4/strict.dtd">
					<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<title>sis_ventas_facturacion</title>
						<meta name="author" content="kplian">
						    
					
					  <link rel="stylesheet" href="../../../sis_obingresos/control/print.css" type="text/css" media="print" charset="utf-8">
					  
					</head>';
					
						$html.='<body style="font-size: 11pt;">';
				if ($datos['pais'] == 'BO') {
					$datos['nit'] = 'NIT '.$datos['nit'];
				} else if ($datos['pais'] == 'AR') {
					$datos['nit'] = 'CUIT '.$datos['nit'];
				} 
				
				if ($datos['origen'] != 'BO' && $datos['pais'] == 'BO') {
					$datos['endoso'] = 'NO VALIDO PARA CREDITO FISCAL';
				}
									
				$html .= '<center>
				<p>
					<img src="../../../lib/imagenes/logo.jpg" alt="logo" width="120" height="60" />
				</p>
					<p style="text-align: center;"><b>
					    &nbsp;&nbsp;&nbsp;&nbsp;' . $datos['nit'] . '</br>
					    &nbsp;&nbsp;&nbsp;&nbsp;EMITIDO/ISSUED <BR>' . $datos['fecha_emision'] . '</br>
					    &nbsp;&nbsp;IATA ' . $datos['codigo_punto_venta'] . '</br>
					    &nbsp;&nbsp;' . $datos['nombre_punto_venta'] . ' <br /></b>				    
					</p>
					</center>
					<hr />
					<p style="text-align: center;"><b>
					    &nbsp;&nbsp;&nbsp;&nbsp;BILLETE ELECTRONICO-ELECTRONIC TIKET<br/>
					    &nbsp;&nbsp;&nbsp;&nbsp;RECIBO DEL PASAJERO-PASSENGER RECEIPT</b>
					</p>
					<hr />
					
					<table style="width: 295px;">						
					
					<tbody>
						<tr>
							<td style="width:40%;"><b>NOMBRE/NAME </b></td>
							<td style="width:60%;"> <b>'.str_replace('/','/ ',$datos['pasajero']) .'</b></td>
						</tr>
						<tr>
							<td style="width:40%;"><b>IDENTIFICACIÓN IDENTIFICATION </b></td>
							<td style="width:60%;"><b>'.$datos['identificacion'] . ' ' . $datos['tipo_identificacion'].'</b></td>							
						</tr>
						<tr>
							<td style="width:40%;"><b>NRO. BILLETE TICKET NUMBER </b></td>
							<td style="width:60%;"><b>'.$datos['nro_boleto'].'</b></td>							
						</tr>
						<tr>
							<td style="width:40%;"><b>ENDOSOS-RESTRICCIONES<br/> ENDORSEMENT-RESTRICTIONS </b></td>
							<td style="width:60%;">'.$datos['endoso'].'</td>							
						</tr>
						<tr>
							<td style="width:40%;"><b>FORMA DE PAGO<br/>FORM OF PAYMENT </b></td>
							<td style="width:60%;">'.$datos['forma_pago'].'</td>							
						</tr>
						<tr>
							<td style="width:40%;"><b>TARIFA ÁEREA<br/>BASIC FARE </b></td>
							<td style="width:60%;">'.$datos['neto'].'</td>							
						</tr>
						<tr>
							<td style="width:40%;"><b>TASAS<br/>TAXES </b></td>
							<td style="width:60%;"> '.$datos['tasas_impuestos'].'</td>							
						</tr>
						<tr>
							<td style="width:40%;"><b>TOTAL </b></td>
							<td style="width:60%;"><b>'.$datos['total'].'</b></td>							
						</tr>';
						if ($datos['origen'] == 'BO' && $datos['pais'] == 'BO') {
							$html.= '<tr>
										<td style="width:40%;"><b>SUJETO CREDITO FISCAL </b></td>
										<td style="width:60%;"> '.$datos['sujeto_credito'].'</td>							
									</tr>';
						}
						
					$html.= '
					</tbody>
					</table>
					<hr/>
					<p style="text-align: center;font-size: 10pt;"><b>
					    &nbsp;&nbsp;&nbsp;&nbsp;ITINERARIO</b>
					</p>';
					
					foreach ($datos['detalle'] as $item_detalle) {
					    $html .= '<hr/>
					    	<table style="width: 350px;font-size: 10pt;">	
					    	<tbody>
								<tr>
									<td style="width:90%;">Fecha/Date : ' . $item_detalle['fecha'] . '</td>
									<td style="width:10%;"></td>							
								</tr>
								<tr>
									<td style="width:90%;"><b>Vuelo/Flight : ' . $item_detalle['vuelo'] . '</b></td>
									<td style="width:10%;"><b>Hora/Time</b></td>							
								</tr>
								<tr>
									<td style="width:90%;">Desde/From : ' . $item_detalle['desde'] . '</td>
									<td style="width:10%;">' . $item_detalle['hora_origen'] . '</td>							
								</tr>
								<tr>
									<td style="width:90%;">A/To : ' . $item_detalle['hacia'] . '</td>
									<td style="width:10%;">' . $item_detalle['hora_destino'] . '</td>							
								</tr>
								<tr>
									<td colspan="2">Base Tarifa/Fare Basis  : ' . $item_detalle['tarifa'] . '</td>
																
								</tr>
								<tr>
									<td colspan="2">EQP/BAG  : ' . $item_detalle['equipaje'] . '</td>						
								</tr>
							</tbody>
							</table>';   	
									   
					
					}
						
					$html.= '				
					
					    <hr/>
					    
					    <p style="text-align: center;">
						    
					    <img src="../../../sis_obingresos/reportes/qr_contrato.jpg" alt="logo" width="120" height="120" style="margin-left: 120px;" />
					    
						    http://www.boa.bo/content/docs/contrato_transporte.pdf
						</p>
					    
						<script language="VBScript">
						Sub Print()
						       OLECMDID_PRINT = 6
						       OLECMDEXECOPT_DONTPROMPTUSER = 2
						       OLECMDEXECOPT_PROMPTUSER = 1
						       call WB.ExecWB(OLECMDID_PRINT, OLECMDEXECOPT_DONTPROMPTUSER,1)
						End Sub
						document.write "<object ID="WB" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>"
						</script>
						
						<script type="text/javascript"> 
						setTimeout(function(){
							 self.print();
							 
							}, 1000);					
						
						
						</script> 
											
				</body>
				</html>';
			

			
			
			return $html;
	}
}
?>
