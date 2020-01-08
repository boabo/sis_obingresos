<?php
/**
 *@package pXP
 *@file gen-ACTArchivoAcm.php
 *@author  rzabala
 *@date 10-09-2018 09:10:17
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
include_once(dirname(__FILE__).'/../../lib/lib_general/funciones.inc.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/ReportWriter.php');
//require_once(dirname(__FILE__).'/../../sis_tesoreria/reportes/RLibroBancos.php');
//require_once(dirname(__FILE__).'/../reportes/RMemoCajaChica.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.phpmailer.php');
include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.smtp.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/cls_correo_externo.php');
//include_once(dirname(__FILE__).'/../../sis_obingresos/control/ACTArchivoAcmDet.php');

include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');
require_once(dirname(__FILE__).'/../reportes/RReporteArchivoAcm.php');

class ACTArchivoAcm extends ACTbase{

	function listarArchivoAcm(){
		$this->objParam->defecto('ordenacion','id_archivo_acm');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODArchivoAcm','listarArchivoAcm');
		} else{
			$this->objFunc=$this->create('MODArchivoAcm');

			$this->res=$this->objFunc->listarArchivoAcm($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function listarPlantillaArchivoExcel(){
        $this->objParam->defecto('ordenacion','id_plantilla_archivo_excel');

        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('archivoAcm') == 'EXTACM'){
            $this->objParam->addFiltro(" arxls.codigo in(''EXTACM'') ");
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('sis_parametros/MODPlantillaArchivoExcel','listarPlantillaArchivoExcel');
        } else{
            $this->objFunc=$this->create('sis_parametros/MODPlantillaArchivoExcel');

            $this->res=$this->objFunc->listarPlantillaArchivoExcel($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

	function insertarArchivoAcm(){
		$this->objFunc=$this->create('MODArchivoAcm');
		if($this->objParam->insertar('id_archivo_acm')){
			$this->res=$this->objFunc->insertarArchivoAcm($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarArchivoAcm($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarArchivoAcm(){
			$this->objFunc=$this->create('MODArchivoAcm');
		$this->res=$this->objFunc->eliminarArchivoAcm($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function cargarArchivoACMExcel(){
        //validar extnsion del archivo
        $id_archivo_acm = $this->objParam->getParametro('id_archivo_acm');

        $codigoArchivo = $this->objParam->getParametro('codigo');
				//echo ($id_archivo_acm);exit;

//        echo "que es: $id_archivo_acm";

        $arregloFiles = $this->objParam->getArregloFiles();
        $ext = pathinfo($arregloFiles['archivo']['name']);
        $extension = $ext['extension'];

        $error = 'no';
        $mensaje_completo = '';
        //validar errores unicos del archivo: existencia, copia y extension
        if(isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])){
            /*
                        if (!in_array($extension, array('xls','xlsx','XLS','XLSX'))){
                            $mensaje_completo = "La extensión del archivo debe ser XLS o XLSX";
                            $error = 'error_fatal';
                        }else {*/
            //procesa Archivo
            $archivoExcel = new ExcelInput($arregloFiles['archivo']['tmp_name'], $codigoArchivo);
            $archivoExcel->recuperarColumnasExcel();

            $arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();

            foreach ($arrayArchivo as $fila) {
								/*Aumentando control para verificar si el office id existe en la base de datos (Ismael Valdivia 08/01/2020)*/

								/*Recuperamos todos los datos del arreglo para encontrar repetidos*/
								/*Eliminamos datos repetidos*/
								$officeId_general[] = str_replace(" ", "", $fila['officce_id']);
							//	$datos_comunes =



								if ($this->existenciaOfficeId(str_replace(" ", "", $fila['officce_id'])) == 0) {
									/*Si no existe el officeId en la base de datos eliminamos el archivo generado*/
									$valor.= str_replace(" ", "", $fila['officce_id']).",";
									/*****************************************************************************/
								}
            }

						$officeId_elimar_repetidos = array_unique($officeId_general);
						$obtenemos_officeId_comunes = array_diff_assoc($officeId_general, $officeId_elimar_repetidos);
						$eliminar_repetidos_officeId_comunes = array_unique($obtenemos_officeId_comunes);
						$obtenemos_repetidos = implode(',',$eliminar_repetidos_officeId_comunes);

						if ($obtenemos_repetidos != '') {
							throw new Exception('El o los siguientes OfficeId: '.$obtenemos_repetidos.' se encuentran mas de una vez en el archivo Excel por favor verifique.');
						}elseif ($valor != '') {
							$valor = trim($valor, ',');
							throw new Exception('El o los siguientes OfficeId: '.$valor.' no se encuentran registrados en el sistema por favor revise el archivo excel y verifique que el OfficeId sea correcto.');
						} else {
								foreach ($arrayArchivo as $fila) {
								 $this->objParam->addParametro('id_archivo_acm', $id_archivo_acm);
								 $this->objParam->addParametro('importe_total_mt', '');
								 $this->objParam->addParametro('estado_reg', '');
								 $this->objParam->addParametro('porcentaje', $fila['porcentaje'] == NULL ? '' : $fila['porcentaje']);
								 $this->objParam->addParametro('importe_total_mb', '');
								 $this->objParam->addParametro('id_agencia', '');
								 $this->objParam->addParametro('officce_id', str_replace(" ", "", $fila['officce_id']) == NULL ? '' : str_replace(" ", "", $fila['officce_id']));
								 $this->objFunc = $this->create('sis_obingresos/MODArchivoAcmDet');
								 $this->res = $this->objFunc->insertarArchivoAcmDet($this->objParam);

								 if($this->res->getTipo()=='ERROR'){
										 $error = 'error';
										 $mensaje_completo = "Error al guardar el fila en tabla ". $this->res->getMensajeTec();
								 }

							 }
					 }

            //upload directory
            $upload_dir = "/tmp/";
            //create file name
            $file_path = $upload_dir . $arregloFiles['archivo']['name'];

            //move uploaded file to upload dir
            if (!move_uploaded_file($arregloFiles['archivo']['tmp_name'], $file_path)) {
                //error moving upload file
                $mensaje_completo = "Error al guardar el archivo ACM en disco";
                $error = 'error_fatal';
            }
            // }
        } else {
            $mensaje_completo = "No se subio el archivo";
            $error = 'error_fatal';
        }
        //armar respuesta en error fatal
        if ($error == 'error_fatal') {

            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTColumnaCalor.php',$mensaje_completo,
                $mensaje_completo,'control');
            //si no es error fatal proceso el archivo
        } else {
            $lines = file($file_path);
            /*
                        foreach ($lines as $line_num => $line) {
                            $arr_temp = explode('|', $line);

                            if (count($arr_temp) != 2) {
                                $error = 'error';
                                $mensaje_completo .= "No se proceso la linea: $line_num, por un error en el formato \n";

                            } else {
                                $this->objParam->addParametro('numero',$arr_temp[0]);
                                $this->objParam->addParametro('monto',$arr_temp[1]);
                                $this->objFunc=$this->create('MODConsumo');
                                $this->res=$this->objFunc->modificarConsumoCsv($this->objParam);

                                if ($this->res->getTipo() == 'ERROR') {
                                    $error = 'error';
                                    $mensaje_completo .= $this->res->getMensaje() . " \n";
                                }
                            }
                        }*/
        }
        //armar respuesta en caso de exito o error en algunas tuplas
        if ($error == 'error') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTArchivoAcmDet.php','Ocurrieron los siguientes errores : ' . $mensaje_completo,
                $mensaje_completo,'control');
            /*
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje($this->res);
            $this->mensajeRes->setMensaje($mensaje_completo,$this->nombre_archivo,$this->res->getMensaje(),$this->res->getMensajeTecnico(),'base',$this->res->getProcedimiento(),$this->res->getTransaccion(),$this->res->getTipoProcedimiento,$respuesta['consulta']);
            $this->mensajeRes->setDatos($respuesta);
            $this->res->imprimirRespuesta($this->respuesta->generarJson());
            */
        } else if ($error == 'no') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('EXITO','ACTArchivoAcmDet.php','El archivo fue ejecutado con éxito',
                'El archivo fue ejecutado con éxito','control');
        }

        //devolver respuesta
        $this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
        //return $this->respuesta;
    }
		/*Aumentando para verificar si existe el office id en la base de datos*/
		function existenciaOfficeId($officeId){

      $cone = new conexion();
      $link = $cone->conectarpdo();
      $copiado = false;
      $consulta ="select count (*) as existencia
												from obingresos.tagencia ag
									where ag.codigo_int= '".$officeId."'
									and ag.estado_reg='activo'";
      $res = $link->prepare($consulta);
      $res->execute();
      $result = $res->fetchAll(PDO::FETCH_ASSOC);
      return $result[0]['existencia'];

    }
		/***************************************************************************/

    function eliminarArchivoACMExcel(){
	    //var_dump($this->objParam->getParametro('id_archivo_acm'));
        $this->objFunc=$this->create('sis_obingresos/MODArchivoAcmDet');
        $this->res=$this->objFunc->eliminarArchivoAcm($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function validarAcm(){
        $id_archivo_acm = $this->objParam->getParametro('id_archivo_acm');
        $this->objParam->addParametro('id_archivo_acm', $id_archivo_acm);
        $this->objFunc=$this->create('sis_obingresos/MODMovimientoEntidad');
        if($this->objParam->insertar('id_acm')){
            $this->res=$this->objFunc->validarAcm($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
       /* $this->objFunc=$this->create('sis_obingresos/MODMovimientoEntidad');
        $this->res=$this->objFunc->validarAcm($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }*/
    function reporteArchivoACM(){
        if($this->objParam->getParametro('id_archivo_acm') != ''){
            $this->objParam->addFiltro("taa.id_archivo_acm = ". $this->objParam->getParametro('id_archivo_acm'));
//            var_dump($this->objParam->getParametro('id_archivo_acm'));exit;
        }


        /*if($this->objParam->getParametro('id_agencia') != ''){
            $this->objParam->addFiltro(" aad.id_agencia = ''".$this->objParam->getParametro('id_agencia')."''");
        }
        if($this->objParam->getParametro('officce_id') != ''){
            $this->objParam->addFiltro("aad.officce_id" . $this->objParam->getParametro('officce_id'));
        }*/
        $this->objFunc=$this->create('MODArchivoAcm');
        $this->res=$this->objFunc->reporteGenArchivoACM($this->objParam);
        //obtener titulo de reporte
         //var_dump($this->res);exit;
        $titulo ='ARCHIVO ACM DOMESTICO';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('datos',$this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato=new RReporteArchivoAcm($this->objParam);
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }
    function habilitarValidacion(){
        $this->objFunc=$this->create('MODArchivoAcm');
        $this->res=$this->objFunc->habilitarValidacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
