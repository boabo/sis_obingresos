<?php
/**
*@package pXP
*@file gen-ACTConsultarVoucherLog.php
*@author  (ismael.valdivia)
*@date 19-12-2019 17:52:00
*@description Servicio para recuperar los datos del voucher
*/

class ACTConsultarVoucherLog extends ACTbase{
	function consultarVoucher(){
      $this->objFunc=$this->create('MODConsultarVoucherLog');
      $this->res=$this->objFunc->consultarVoucher($this->objParam);
      $this->res->imprimirRespuesta($this->res->generarJson());
  }
}


?>
