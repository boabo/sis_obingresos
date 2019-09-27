<?php
/**
*@package pXP
*@file gen-ACTAcreditacionPorVoideo.php
*@author  (ismael.valdivia)
*@date 20-09-2019 08:45:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class ACTActivarContratos extends ACTbase{
	function actualizarContratos(){
      $this->objFunc=$this->create('MODActivarContratos');
      $this->res=$this->objFunc->actualizarContratos($this->objParam);
      $this->res->imprimirRespuesta($this->res->generarJson());
  }
}


?>
