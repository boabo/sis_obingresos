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
        //bnew:true,
        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            config.vista = 'corporativo';
            Phx.vista.AgenciaPortal.superclass.constructor.call(this,config);


            this.addButton('btnMovimientos',
                {
                    grupo:[1,2],
                    text: 'Movimientos Per. Vigente',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.onMovimientos,
                    tooltip: 'Movimientos de la agencia corporativa para el periodo vigente'
                }
            );

            this.addButton('btnMovimientosSP',
                {
                    grupo:[1,2],
                    text: 'Rep Movimientos',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.onMovimientosSP,
                    tooltip: 'Movimientos de la agencia corporativa para un rango de fechas'
                }
            );

            this.addButton('btnTkt',
                {
                    grupo:[1,2],
                    text: 'Tkts',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.onTkts,
                    tooltip: 'Billetes emitidos de la agencia corporativa'
                }
            );
            this.addButton('Estado',
                {
                    grupo:[1,2],
                    text: 'Estado de Cuenta',
                    iconCls: 'bprint',
                    disabled: true,
                    handler: this.estado,
                    tooltip: 'Billetes emitidos de la agencia corporativa'
                }
            );
            this.addButton('Acm',
                {
                    grupo:[1,2],
                    text: 'Acm',
                    iconCls: 'bfolder',
                    disabled: true,
                    handler: this.onButtonAcm,
                    tooltip: '<b>Archivo Acm</b></br>Muestra el Detalle de ACMs validados.'
                }
            );

        },
        onButtonAcm: function(){

            //Phx.vista.ArchivoAcmDet.superclass.onButtonAcm.call(this);
            var rec = {maestro: this.sm.getSelected().data}
            rec.acm='funcional';
            rec.mov_ent='SI';
            console.log('VALOR', rec.acm);

            Phx.CP.loadWindows('../../../sis_obingresos/vista/acm/Acm.php',
                'Detalle de los ACMs Validados',
                {
                    width:1200,
                    height:600
                },
                rec,
                this.idContenedor,
                'Acm');

        },
        onMovimientosSP : function () {
            var rec = {maestro: this.sm.getSelected().data};

            Phx.CP.loadWindows('../../../sis_obingresos/vista/movimiento_entidad/MovimientoEntidadSinPeriodo.php',
                'Movimientos de agencia corporativa',
                {
                    width:'90%',
                    height:'90%'
                },
                rec,
                this.idContenedor,
                'MovimientoEntidadSinPeriodo');

        },

        onMovimientos : function () {
            var rec = {maestro: this.sm.getSelected().data};

            Phx.CP.loadWindows('../../../sis_obingresos/vista/movimiento_entidad/MovimientoEntidad.php',
                'Movimientos de agencia corporativa',
                {
                    width:'90%',
                    height:'90%'
                },
                rec,
                this.idContenedor,
                'MovimientoEntidad');

        },

        onTkts : function () {
            var rec = {maestro: this.sm.getSelected().data};

            Phx.CP.loadWindows('../../../sis_obingresos/vista/detalle_boletos_web/DetalleBoletosWeb.php',
                'Boletos',
                {
                    width:800,
                    height:'90%'
                },
                rec,
                this.idContenedor,
                'DetalleBoletosWeb');

        },

        preparaMenu:function()
        {	var rec = this.sm.getSelected();
            Phx.vista.AgenciaPortal.superclass.preparaMenu.call(this);
            this.getBoton('btnMovimientos').enable();
            this.getBoton('btnMovimientosSP').enable();

            this.getBoton('btnTkt').enable();
            this.getBoton('Estado').enable();

            this.getBoton('Acm').enable();

        },
        liberaMenu:function()
        {

            Phx.vista.AgenciaPortal.superclass.liberaMenu.call(this);
            this.getBoton('btnMovimientos').disable();
            this.getBoton('btnMovimientosSP').disable();
            this.getBoton('btnTkt').disable();
            this.getBoton('Estado').disable();
            this.getBoton('Acm').disable();
        },
        estado:function(){
            Phx.CP.loadingShow();
            var rec=this.sm.getSelected();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/PeriodoVenta/EstadoCuenta',
                params:{'id_agencia':rec.data.id_agencia},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }

    };
</script>
