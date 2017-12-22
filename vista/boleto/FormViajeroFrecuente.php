<?php
/**
 *@package pXP
 *@file    FormViajeroFrecuente.php
 *@author  MMV
 *@date    11-12-2017
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormViajeroFrecuente=Ext.extend(Phx.frmInterfaz,{
        ActSave:'../../sis_obingresos/control/Boleto/viajeroFrecuente',
        constructor:function(config)
        {
            this.maestro = config;
            Phx.vista.FormViajeroFrecuente.superclass.constructor.call(this,config);
            this.init();
            console.log('maestro',this.maestro);
        },

        loadValoresIniciales:function() {
            Phx.vista.FormViajeroFrecuente.superclass.loadValoresIniciales.call(this);
            this.getComponente('id_boleto_amadeus').setValue(this.maestro.id_boleto_amadeus);
            this.getComponente('pnr').setValue(this.maestro.localizador);
            this.getComponente('ticketNumber').setValue(this.maestro.nro_boleto);
        },

        successSave:function(resp)
        {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        },


        Atributos:[


            {
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_boleto_amadeus'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_pasajero_frecuente'
                },
                type:'Field',
                form:true
            },

            {
                config:{
                    name: 'ffid',
                    fieldLabel: 'FFID',
                    allowBlank: true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength: 100,
                    style: 'background-color:#9BF592 ; background-image: none;'
                },
                type:'TextField',
                id_grupo:1,
                form:true
            },


            {
                config:{
                    name: 'voucherCode',
                    fieldLabel: 'Voucher Code',
                    allowBlank: true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength: 100,
                    style: 'background-color: #9BF592; background-image: none;'
                },
                type:'TextField',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name: 'ticketNumber',
                    fieldLabel: 'Ticket Number',
                    allowBlank: true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength: 100,
                    style: 'background-color: #E1F590; background-image: none;',
                    readOnly :true
                },
                type:'TextField',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name: 'pnr',
                    fieldLabel: 'PNR',
                    allowBlank: true,
                    anchor: '90%',
                    gwidth: 100,
                    maxLength: 100,
                    style: 'background-color: #E1F590; background-image: none;',
                    readOnly :true
                },
                type:'TextField',
                id_grupo:1,
                form:true
            }


        ],
        title:'Viajero Frecuente',
        fields: [
            {name:'id_boleto_amadeus', type: 'numeric'},
            {name:'ffid', type: 'varchar'},
            {name:'pnr', type: 'varchar'},
            {name:'ticketNumber', type: 'varchar'},
            {name:'voucherCode', type: 'varchar'}
        ],
        onSubmit: function(o) {
            Phx.vista.FormViajeroFrecuente.superclass.onSubmit.call(this,o,undefined, true);
            this.cambiarRevision();
        },
        cambiarRevision: function(){
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_obingresos/control/Boleto/cambiarRevisionBoleto',
                params:{ id_boleto_amadeus: this.maestro.id_boleto_amadeus},
                success: this.succeEstadoSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        succeEstadoSinc:function(){
            Phx.CP.loadingHide();
        }
    })
</script>
