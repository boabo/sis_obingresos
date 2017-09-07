<?php
/**
*@package pXP
*@file gen-ACTDeposito.php
*@author  (jrivera)
*@date 06-01-2016 22:42:28
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/RReporteDepositoOgone.php');
require_once(dirname(__FILE__).'/../reportes/RReporteDepositoBancaInternet.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');
class ACTDeposito extends ACTbase{    
			
	function listarDeposito(){
		$this->objParam->defecto('ordenacion','id_deposito');

		$this->objParam->defecto('dir_ordenacion','desc');
		if ($this->objParam->getParametro('id_agencia') != '') {
			$this->objParam->addFiltro("dep.id_agencia = ". $this->objParam->getParametro('id_agencia'));
		}

        if ($this->objParam->getParametro('tipo') != '') {
            $this->objParam->addFiltro("dep.tipo = ''". $this->objParam->getParametro('tipo')."''");
        }

        if ($this->objParam->getParametro('estado') != '') {
            $this->objParam->addFiltro("dep.estado = ''". $this->objParam->getParametro('estado')."''");
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDeposito','listarDeposito');
		} else{
			$this->objFunc=$this->create('MODDeposito');
			
			$this->res=$this->objFunc->listarDeposito($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarDeposito(){
		$this->objFunc=$this->create('MODDeposito');	
		if($this->objParam->insertar('id_deposito')){
			$this->res=$this->objFunc->insertarDeposito($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarDeposito($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarDeposito(){
        $this->objFunc=$this->create('MODDeposito');
		$this->res=$this->objFunc->eliminarDeposito($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function cambiaEstadoDeposito(){
        $this->objFunc=$this->create('MODDeposito');
        $this->res=$this->objFunc->cambiaEstadoDeposito($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

	function subirCSVDeposito(){
        $arregloFiles = $this->objParam->getArregloFiles();
        $ext = pathinfo($arregloFiles['archivo']['name']);
        $extension = $ext['extension'];
        $error = 'no';
        $mensaje_completo = '';
        if ($this->objParam->getPArametro('tipo') == 'ogone') {
            //Valida extencio

            //Validar errores del archivo
            if (isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])) {
                if ($extension != 'csv' && $extension != 'CSV') {
                    $mensaje_completo = "La extensión del archivo debe ser CSV";
                    $error = 'error_fatal';
                }
                $upload_dir = "/tmp/";
                $file_path = $upload_dir . $arregloFiles['archivo']['name'];
                if (!move_uploaded_file($arregloFiles['archivo']['tmp_name'], $file_path)) {
                    $mensaje_completo = "Error al guardar el archivo csv en disco";
                    $error = 'error_fatal';
                }
            } else {
                $mensaje_completo = "No se subio el archivo";
                $error = 'error_fatal';
            }
            //armar respuesta en error fatal
            if ($error == 'error_fatal') {
                $this->mensajeRes = new Mensaje();
                $this->mensajeRes->setMensaje('ERROR', 'ACTDeposito.php', $mensaje_completo, $mensaje_completo, 'control');
                //si no es error fatal proceso el archivo
            } else {
                $lines = file($file_path);
                foreach ($lines as $line_num => $line) {
                    $line = str_replace("'", "", $line);
                    $arr_temp = explode('|', $line);

                    $this->objParam->addParametro('nro_deposito', $arr_temp[0]);
                    $this->objParam->addParametro('pnr', $arr_temp[1]);
                    $this->objParam->addParametro('descripcion', $arr_temp[23]);
                    $arr_temp[12] = str_replace(',', '.', $arr_temp[12]);
                    $this->objParam->addParametro('monto_deposito', $arr_temp[12]);
                    $this->objParam->addParametro('moneda', $arr_temp[13]);
                    $this->objParam->addParametro('estado', $arr_temp[4]);
                    $this->objParam->addParametro('fecha', $arr_temp[2]);
                    $this->objParam->addParametro('observaciones', $arr_temp[16]);
                    $this->objFunc = $this->create('MODDeposito');
                    $this->res = $this->objFunc->subirDatos($this->objParam); // cambiar


                    if ($this->res->getTipo() == 'ERROR') {
                        $error = 'error';
                        $mensaje_completo .= $this->res->getMensaje() . " \n";
                    }

                }

            }
            //armar respuesta en caso de exito o error en algunas tuplas
            if ($error == 'error') {
                $this->mensajeRes = new Mensaje();
                $this->mensajeRes->setMensaje('ERROR', 'ACTDeposito.php', 'Ocurrieron los siguientes errores : ' . $mensaje_completo,
                    $mensaje_completo, 'control');
            } else if ($error == 'no') {
                $this->mensajeRes = new Mensaje();
                $this->mensajeRes->setMensaje('EXITO', 'ACTDeposito.php', 'El archivo fue ejecutado con éxito',
                    'El archivo fue ejecutado con éxito', 'control');
            }
        } else if ($this->objParam->getPArametro('tipo') == 'worldpay') {
            if(isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])) {
                if (!in_array($extension, array('xls', 'xlsx', 'XLS', 'XLSX'))) {
                    $mensaje_completo = "La extensión del archivo debe ser XLS o XLSX";
                    $error = 'error_fatal';
                } else {
                    //procesa Archivo
                    $archivoExcel = new ExcelInput($arregloFiles['archivo']['tmp_name'], 'EXTWP');
                    $archivoExcel->recuperarColumnasExcel();
                    $arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();
                    foreach ($arrayArchivo as $fila) {

                        $this->objParam->addParametro('order_code', $fila['order_code']);
                        $this->objParam->addParametro('fecha', $fila['fecha']);
                        $this->objParam->addParametro('hora', $fila['hora']);
                        $this->objParam->addParametro('metodo_pago', $fila['metodo_pago']);
                        $this->objParam->addParametro('estado', $fila['estado']);
                        $this->objParam->addParametro('tarjeta', $fila['tarjeta']);
                        $this->objParam->addParametro('moneda', $fila['moneda']);
                        $this->objParam->addParametro('monto', $fila['monto']);

                        $this->objFunc = $this->create('MODDeposito');
                        $this->res = $this->objFunc->subirDatosWP($this->objParam);

                        if ($this->res->getTipo() == 'ERROR') {
                            $error = 'error';
                            $mensaje_completo = "Error al guardar el fila en tabla " . $this->res->getMensajeTec();
                            break;
                        }
                    }
                }
            } else {
                $mensaje_completo = "No se subio el archivo";
                $error = 'error_fatal';
            }
        }

        if ($error == 'error_fatal') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTDeposito.php',$mensaje_completo,
                $mensaje_completo,'control');
            //si no es error fatal proceso el archivo
        }

        if ($error == 'error') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTDeposito.php','Ocurrieron los siguientes errores : ' . $mensaje_completo,
                $mensaje_completo,'control');

        } else if ($error == 'no') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('EXITO','ACTDeposito.php','El archivo fue ejecutado con éxito',
                'El archivo fue ejecutado con éxito','control');
        }

        //devolver respuesta
        $this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
    }

    function reporteDepositoBancaInternet(){

        $this->objFunc = $this->create('MODDeposito');
        $this->res = $this->objFunc->reporteDepositoBancaInternet($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Depositos';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos_deposito', $this->res->datos);
        $this->objFunc = $this->create('MODDeposito');
        $this->res = $this->objFunc->reporteDepositoBancaInternetArchivo($this->objParam);
        $this->objParam->addParametro('datos_archivo', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteDepositoBancaInternet($this->objParam);
        $this->objReporteFormato->imprimeCabecera();
        $this->objReporteFormato->generarDatos();

        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function reporteDeposito(){

        $this->objFunc = $this->create('MODDeposito');
        $this->res = $this->objFunc->listarDepositoReporte($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Depositos';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteDepositoOgone($this->objParam);
        if ($this->objParam->getParametro('por') == 'boleto') {
            $this->objReporteFormato->generarDatos();
        } else {
            $this->objReporteFormato->generarDatosDeposito();
        }
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }
}

?>