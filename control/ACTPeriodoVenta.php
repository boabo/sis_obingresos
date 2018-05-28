<?php
/**
*@package pXP
*@file gen-ACTPeriodoVenta.php
*@author  (jrivera)
*@date 08-04-2016 22:44:37
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

include(dirname(__FILE__).'/../../lib/rest/NetOBRestClient.php');
require_once(dirname(__FILE__).'/../reportes/REstadoCuentaXLS.php');
class ACTPeriodoVenta extends ACTbase{    
			
	function listarPeriodoVenta(){
		$this->objParam->defecto('ordenacion','id_periodo_venta');

		$this->objParam->defecto('dir_ordenacion','asc');
		

		if($this->objParam->getParametro('id_gestion') != '') {
                $this->objParam->addFiltro(" perven.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        }
		
		if($this->objParam->getParametro('tipo') != '') {
                $this->objParam->addFiltro(" perven.id_tipo_periodo = " . $this->objParam->getParametro('tipo'));
        }
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPeriodoVenta','listarPeriodoVenta');
		} else{
			$this->objFunc=$this->create('MODPeriodoVenta');
			
			$this->res=$this->objFunc->listarPeriodoVenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function listarDetallePeriodoAgencia(){
        $this->objParam->defecto('ordenacion','fecha');

        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('cierre_periodo') != '') {
            $this->objParam->addFiltro(" me.cierre_periodo = ''" . $this->objParam->getParametro('cierre_periodo')."''");
        }

        if($this->objParam->getParametro('ajuste') != '') {
            $this->objParam->addFiltro(" me.ajuste = ''" . $this->objParam->getParametro('ajuste')."''");
        }

        if($this->objParam->getParametro('garantia') != '') {
            $this->objParam->addFiltro(" me.garantia = ''" . $this->objParam->getParametro('garantia')."''");
        }

        if($this->objParam->getParametro('tipo_movimiento') != '') {
            $this->objParam->addFiltro(" me.tipo = ''" . $this->objParam->getParametro('tipo_movimiento')."''");
        }

        $this->objFunc=$this->create('MODPeriodoVenta');
        $this->res=$this->objFunc->listarDetallePeriodoAgencia($this->objParam);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function ResumenEstadoCC(){



        $this->objFunc=$this->create('MODPeriodoVenta');
        $this->res=$this->objFunc->ResumenEstadoCC($this->objParam);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	function listarTotalesPeriodoAgencia(){
        $this->objParam->defecto('ordenacion','nombre');

        $this->objParam->defecto('dir_ordenacion','asc');

        $this->objFunc=$this->create('MODPeriodoVenta');
		
		if($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro(" pva.id_agencia = " . $this->objParam->getParametro('id_agencia'));
        }
		
		if($this->objParam->getParametro('periodo_cerrado') == 'no') {
            $this->objParam->addFiltro(" pva.estado = ''abierto''");
        }
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPeriodoVenta','listarTotalesPeriodoAgencia');
																							
		} else{
			$this->objFunc=$this->create('MODPeriodoVenta');
			
			$this->res=$this->objFunc->listarTotalesPeriodoAgencia($this->objParam);
			$temp = Array();

	        $temp['total_credito_mb'] = $this->res->extraData['total_credito_mb'];
	        $temp['total_credito_me'] = $this->res->extraData['total_credito_me'];
	        $temp['total_boletos_mb'] = $this->res->extraData['total_boletos_mb'];
	        $temp['total_boletos_usd'] = $this->res->extraData['total_boletos_usd'];
	        $temp['total_comision_mb'] = $this->res->extraData['total_comision_mb'];
	        $temp['total_comision_usd'] = $this->res->extraData['total_comision_usd'];
	        $temp['total_debito_mb'] = $this->res->extraData['total_debito_mb'];
	        $temp['total_debito_usd'] = $this->res->extraData['total_debito_usd'];
	        $temp['total_neto_mb'] = $this->res->extraData['total_neto_mb'];
	        $temp['total_neto_usd'] = $this->res->extraData['total_neto_usd'];	
	
	        $temp['tipo_reg'] = 'summary';
	        $temp['id_periodo_venta_agencia'] = 0;
	
	        $this->res->total++;
	
	        $this->res->addLastRecDatos($temp);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
        
    }

    function modificarPeriodoVenta(){
        $this->objFunc=$this->create('MODPeriodoVenta');

        $this->res=$this->objFunc->modificarPeriodoVenta($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
				
	function insertarPeriodoVenta(){
		$this->objFunc=$this->create('MODPeriodoVenta');	

        $this->res=$this->objFunc->insertarPeriodoVenta($this->objParam);

        if ($this->res->getTipo() == 'EXITO') {

            if (!isset($_SESSION['_OBNET_REST_URI']) ) {
                throw new Exception("No existe parametrizacion completa para conexion REST a .NET", 3);
            }

            $datos = $this->res->getDatos();

            $this->objParam->addParametro('id_periodo_venta', $datos['id_periodo_venta']);
			
			$this->objParam->addParametroConsulta('puntero', '0');
			$this->objParam->addParametroConsulta('cantidad', '10000');
			$this->objParam->defecto('ordenacion','nombre');
        	$this->objParam->defecto('dir_ordenacion','asc');
            $this->objFunc = $this->create('MODPeriodoVenta');
            $this->res = $this->objFunc->listarTotalesPeriodoAgencia($this->objParam);

            if ($this->res->getTipo() == 'EXITO') {
                $datos = $this->res->getDatos();

                $rest_uri = str_replace('http://','',$_SESSION['_OBNET_REST_URI']);
                $rest_uri = str_replace('https://','',$rest_uri);


                $netOBRestClient = NetOBRestClient::connect($_SESSION['_OBNET_REST_URI'], '');
                $netOBRestClient->setHeaders(array('Content-Type: json;'));

                foreach ($datos as $value){

                    $res = $netOBRestClient->doPost('InsertarTotalPeriodo',
                        array(	"idPeriodoVentaAgencia"=> $value['id_periodo_venta_agencia'],
                            "CodigoPeriodo"=> $value['codigo_periodo'],
                                "id_agencia_ERP"=> $value['id_agencia'],
                                "medioPago"=> $value['medio_pago'],
                                "moneda_restrictiva"=> $value['moneda_restrictiva'],
                                "Mes"=> $value['mes'],
                                "Gestion"=> $value['gestion'],
                                "idPeriodoVenta"=> $value['id_periodo_venta'],
                                "Desde"=> $value['fecha_ini2'],
                                "Hasta"=> $value['fecha_fin2'],
                                "TotalCreditos_mb"=> $value['total_credito_mb'],
                                "TotalBoletos_mb"=> $value['total_boletos_mb'],
                                "TotalComision_mb"=> $value['total_comision_mb'],
                                "TotalDebitos_mb"=> $value['total_debito_mb'],
                                "TotalCreditos_me"=> $value['total_credito_me'],
                                "TotalBoletos_me"=> $value['total_boletos_usd'],
                                "TotalComision_me"=> $value['total_comision_usd'],
                                "TotalDebitos_me"=> $value['total_debito_usd'],
                                "TotalNetos_mb"=> $value['total_neto_mb'],
                                "TotalNetos_me"=> $value['total_neto_usd'],
                                "listaTKT"=>$value['billetes']
                        ));
                    echo $res;


                }
            }

        }

		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarPeriodoVenta(){
			$this->objFunc=$this->create('MODPeriodoVenta');	
		$this->res=$this->objFunc->eliminarPeriodoVenta($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function validarBoletosPortal ()
    {


        //1 llamar a servicio de generacion de tickets emitidos Portal, insertar en la tabla y generar observaciones localmente
        // y devolver la observaciones en un arreglo

        //$netOBRestClient = NetOBRestClient::connect($_SESSION['_OBNET_REST_URI'], '');
        //$netOBRestClient->setHeaders(array('Content-Type: json;'));
        //$res = $netOBRestClient->doPost('EmisionBoa',
        /*    array(	"credenciales"=> $_SESSION['_OBNET_REST_CRED'],
                "idioma"=> "ES",
                "fecha"=> $this->objParam->getParametro('fecha_emision'),
                "ip"=> "127.0.0.1",
                "xmlJson"=> false
            ));*/
        //$this->objParam->addParametro('detalle',$res);
        
        $this->objFunc = $this->create('MODDetalleBoletosWeb');
		$this->res = $this->objFunc->validarBoletos($this->objParam);

        //1.1 si hay error devolver el error

        //1.2.2 si no hay error llamar al servicio de registro de observaciones Portal enviando las observaciones
        //del punto 1.2
        
        //1.2.2.1 si hay error devolver el error
        
        //1.2.2.2 si no hay error se inserta el periodo de venta banca
        //inserta totales de preiodo venta
        //1.2.2.3 si no hay error se inserta el periodo de venta cuenta corriente
        //inserta periodo de venta cuenta corriente
        $this->res->imprimirRespuesta($this->res->generarJson());

    }
    function EstadoCuentaDatos(){
        $this->objFunc = $this->create('MODPeriodoVenta');
        $cbteHeader = $this->objFunc->EstadoCuenta($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }
    function EstadoCuentaDesDatos(){
        $this->objFunc = $this->create('MODPeriodoVenta');
        $cbteHeader = $this->objFunc->EstadoCuentaDes($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }
    function EstadoCuenta (){
        $dataSource = $this->EstadoCuentaDatos();
        $dataSourceDes= $this->EstadoCuentaDesDatos();
        //var_dump(($dataSource->getDatos()));exit;
        $nombreArchivo = uniqid(md5(session_id()).'Estado Cuentas').'.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $reporte =new REstadoCuentaXLS($this->objParam);
        $reporte->datosHeader($dataSource->getDatos(),$dataSourceDes->getDatos());
        $reporte->generarReporte();
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());



    }

			
}

?>