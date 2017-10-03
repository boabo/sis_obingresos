<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  (rarteaga)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.BoletoVendido = {    
    bsave:false,  
    bdel:false,     
    require:'../../../sis_obingresos/vista/boleto/Boleto.php',
    requireclase:'Phx.vista.Boleto',
    title:'Boleto',
    nombreVista: 'BoletoVendido',
    bnew:false,        
    successGetVariables : function (response,request) {
        this.tipo_usuario = 'todos';
		Phx.vista.BoletoVendido.superclass.successGetVariables.call(this,response,request);

		this.store.baseParams.estado = 'pagado';				
	},
    preparaMenu:function() {

    },
    liberaMenu:function() {

    },

};
</script>
