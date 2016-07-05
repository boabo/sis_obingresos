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
Phx.vista.BoletoCaja = {    
    bsave:false,  
    bdel:false,     
    require:'../../../sis_obingresos/vista/boleto/Boleto.php',
    requireclase:'Phx.vista.Boleto',
    title:'Boleto',
    nombreVista: 'BoletoCaja',
    bnew:false,        
    successGetVariables : function (response,request) {
		Phx.vista.BoletoCaja.superclass.successGetVariables.call(this,response,request);  
		this.store.baseParams.estado = 'caja';
		this.addButton('btnPagarGrupo',
            {
                text: 'Pagar Grupo',
                iconCls: 'bmoney',
                disabled: true,
                handler: this.onGrupo,
                tooltip: 'Paga todos los boletos seleccionados'
            }
        );
				
	},
	preparaMenu:function()
    {	var rec = this.sm.getSelected();
          
        Phx.vista.Boleto.superclass.preparaMenu.call(this);         
        this.getBoton('btnPagarGrupo').enable(); 	        
        
    },
    liberaMenu:function()
    {	
                
        Phx.vista.Boleto.superclass.liberaMenu.call(this);        
        this.getBoton('btnPagarGrupo').disable(); 
    },    
    
};
</script>
