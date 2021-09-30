<?php
/**
*@package pXP
*@file gen-ACTMcoS.php
*@author  (breydi.vasquez)
*@date 28-04-2020 15:25:04
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/REMcos.php');
class ACTMcoS extends ACTbase{

	function listarMcoS(){
			  $this->objParam->defecto('ordenacion','id_mco');
        $this->objParam->defecto('dir_ordenacion','asc');
        //adicion a filtros

        $this->objParam->getParametro('id_punto_venta') != '' && $this->objParam->addFiltro(" imcos.id_punto_venta= ". $this->objParam->getParametro('id_punto_venta'));
				// $this->objParam->getParametro('campo_fecha') != '' && $this->objParam->addFiltro(" imcos.fecha_reg = ''".$this->objParam->getParametro('campo_fecha')."''::date");
				$this->objParam->getParametro('nroMco') != '' && $this->objParam->addFiltro(" imcos.nro_mco = ''".$this->objParam->getParametro('nroMco')."''");
				// $this->objParam->getParametro('usuario_filtro') != '' && $this->objParam->addFiltro(" imcos.id_usuario_reg = ".$_SESSION["_ID_USUARIO_OFUS"]);
				$this->objParam->getParametro('fecha') != '' && $this->objParam->addFiltro(" imcos.fecha_emision = ''".$this->objParam->getParametro('fecha')."''::date");

				 // $this->objParam->getParametro('tkt') != '' && $this->objParam->addFiltro(" bole.nro_boleto like ''''||COALESCE(''".$this->objParam->getParametro('tkt')."'',''-'')||''%''||''''");
        // fin filtro

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMcoS','listarMcoS');
		} else{
			$this->objFunc=$this->create('MODMcoS');

			$this->res=$this->objFunc->listarMcoS($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarMcoS(){
		$this->objFunc=$this->create('MODMcoS');

		if($this->objParam->insertar('id_mco')){
			$this->res=$this->objFunc->insertarMcoS($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarMcoS($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarMcoS() {
		$this->objFunc=$this->create('MODMcoS');
		$this->res=$this->objFunc->eliminarMcoS($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getDatatoFormRegMcoS() {
        $this->objFunc=$this->create('MODMcoS');
        $this->res=$this->objFunc->getDatatoFormRegMcoS($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarTkts() {

				($this->objParam->getParametro('concepto_codigo')!='MCOSAL')?$codigo_concepto= 'EL TICKET NO EXISTE': $codigo_concepto= 'EL MCO NO EXISTE';
        $this->objParam->defecto('ordenacion', 'billete');
        $this->objParam->defecto('dir_ordenacion', 'asc');
        $this->objFunc = $this->create('MODMcoS');
        $this->res = $this->objFunc->listarTkts($this->objParam);
        $datos = Array();


        if (count($this->res->getDatos())== 0) {
            $datos['tkt'] = $codigo_concepto;
            $this->res->total++;
            $this->res->addLastRecDatos($datos);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

		function listarTktFiltro(){

			$this->objParam->getParametro('id_punto_venta') != '' && $this->objParam->addFiltro(" imcos.id_punto_venta= ". $this->objParam->getParametro('id_punto_venta'));

			$this->objFunc = $this->create('MODMcoS');
			$this->res = $this->objFunc->listarTktFiltro($this->objParam);
			if($this->objParam->getParametro('_adicionar')!=''){

		 	$respuesta = $this->res->getDatos();

			 array_unshift ( $respuesta, array(  'id_mco'=>'0', 'nro_mco'=>'Todos'));
			 $this->res->setDatos($respuesta);
		 }
			$this->res->imprimirRespuesta($this->res->generarJson());
		}

		function listarRepoMcoS(){

			$this->objParam->defecto('ordenacion','nro_mco');
			$this->objParam->defecto('dir_ordenacion','asc');


			// filtros
			$this->objParam->getParametro('id_gestion') != '' && $this->objParam->addFiltro(" imcos.id_gestion= ". $this->objParam->getParametro('id_gestion'));
			// $this->objParam->getParametro('fecha_ini') != '' && $this->objParam->addFiltro(" imcos.fecha_emision= ". $this->objParam->getParametro('fecha_emision'));
			$this->objParam->getParametro('id_punto_venta') != '' && $this->objParam->addFiltro(" imcos.id_punto_venta= ". $this->objParam->getParametro('id_punto_venta'));
			if($this->objParam->getParametro('id_mco') == 0){
					$this->objParam->addFiltro(" 0 = 0 ");
			}else{
				$this->objParam->getParametro('id_mco') != '' && $this->objParam->addFiltro(" imcos.id_mco= ". $this->objParam->getParametro('id_mco'));
			}


			if($this->objParam->getParametro('filtro_mes')=='true'){
				if ($this->objParam->getParametro('fecha_ini')!='' && $this->objParam->getParametro('fecha_fin') != ''){
						$this->objParam->addFiltro(" imcos.fecha_reg::date between ''". $this->objParam->getParametro('fecha_ini')."''::date and ''".$this->objParam->getParametro('fecha_fin')."''::date");
				}
			}else{
						$this->objParam->getParametro('fecha_ini')!='' && $this->objParam->addFiltro(" imcos.fecha_reg::date = ''".$this->objParam->getParametro('fecha_ini')."''::date");
			}

      $this->objFunc=$this->create('MODMcoS');
      $dataTramite=$this->objFunc->listarRepoMcoS($this->objParam);
      if ($dataTramite->getTipo() == 'EXITO') {
              return $dataTramite;
      } else {
              $dataTramite->imprimirRespuesta($dataTramite->generarJson());
              exit;
      }
    }

		function reporteMcos() {
			$dataSource = $this->listarRepoMcoS();

			$nombreArchivo = 'MCOS'.uniqid(md5(session_id())).'.pdf';
			//parametros basicos
			$tamano = 'LETTER';
			$orientacion = 'L';
			$titulo = 'MCOS';


			$this->objParam->addParametro('orientacion',$orientacion);
			$this->objParam->addParametro('tamano',$tamano);
			$this->objParam->addParametro('titulo_archivo',$titulo);
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);

			//Instancia la clase de pdf
			$reporte = new REMcos($this->objParam);
			$reporte->setDatos($dataSource->getDatos());
			$reporte->generarReporte();
			$reporte->output($reporte->url_archivo,'F');

			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}
		function listarTktFiltroConsul(){


			$this->objFunc = $this->create('MODMcoS');
			$this->res = $this->objFunc->listarTktFiltro($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
		}
}

?>
