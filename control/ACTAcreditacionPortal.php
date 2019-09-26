<?php
/**
*@package pXP
*@file gen-ACTAcreditacionPortal.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class ACTAcreditacionPortal extends ACTbase{
	function insertarAcreditacion(){
		$this->objFunc=$this->create('MODAcreditacionPortal');
		if($this->objParam->insertar('id_movimiento_entidad')){
			$this->res=$this->objFunc->insertarAcreditacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
}

?>
