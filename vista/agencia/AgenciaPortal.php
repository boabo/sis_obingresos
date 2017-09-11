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
    Phx.vista.AgenciaPortal = {
        bsave:false,
        bdel:false,
        require:'../../../sis_obingresos/vista/agencia/Agencia.php',
        requireclase:'Phx.vista.Agencia',
        title:'Boleto',
        nombreVista: 'AgenciaPortal',
        bnew:false,
        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            config.vista = 'corporativo';
            Phx.vista.AgenciaPortal.superclass.constructor.call(this,config);


            this.addButton('btnMovimientos',
                {
                    text: 'Movimientos',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.onMovimientos,
                    tooltip: 'Movimientos de la agencia corporativa'
                }
            );

        },
        onMovimientos : function () {
            var rec = {maestro: this.sm.getSelected().data};

            Phx.CP.loadWindows('../../../sis_obingresos/vista/movimiento_entidad/MovimientoEntidad.php',
                'Movimientos de agencia corporativa',
                {
                    width:800,
                    height:'90%'
                },
                rec,
                this.idContenedor,
                'MovimientoEntidad');

        },

        preparaMenu:function()
        {	var rec = this.sm.getSelected();
            Phx.vista.AgenciaPortal.superclass.preparaMenu.call(this);
            this.getBoton('btnMovimientos').enable();

        },
        liberaMenu:function()
        {

            Phx.vista.AgenciaPortal.superclass.liberaMenu.call(this);
            this.getBoton('btnMovimientos').disable();
        },

    };
</script>
