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
    Phx.vista.MovimientoEntidadSinPeriodo = {
        bsave:false,
        bdel:false,
        require:'../../../sis_obingresos/vista/movimiento_entidad/MovimientoEntidad.php',
        requireclase:'Phx.vista.MovimientoEntidad',
        title:'Movimientos',
        nombreVista: 'MovimientoEntidadSinPeriodo',
        bnew:false,
        bedit:false,
        primeraCarga : true,
        constructor:function(config){
            this.maestro=config.maestro;
			this.initButtons=[this.fecha_ini,this.fecha_fin];


			delete this.Atributos[9].config.renderer;
			delete this.Atributos[10].config.renderer;
			delete this.Atributos[13].config.renderer;
			delete this.Atributos[14].config.renderer;


	    	//llama al constructor de la clase padre
			Phx.vista.MovimientoEntidad.superclass.constructor.call(this,config);

			this.addButton('archivo', {
	                grupo: [0,1],
	                argument: {imprimir: 'archivo'},
	                text: 'Respaldos',
	                iconCls:'blist' ,
	                disabled: false,
	                handler: this.archivo
	            });
			this.init();
			this.iniciarEventos();
			this.store.baseParams.id_entidad = this.maestro.id_agencia;


        },
        fecha_ini : new Ext.form.DateField({
	        format: 'd/m/Y',
	        fieldLabel: 'Fecha Ini',
	        width:125,
	        emptyText:'Fecha Ini...'
	    }),
	    fecha_fin : new Ext.form.DateField({
	        format: 'd/m/Y',
	        fieldLabel: 'Fecha Fin',
	        width:125,
	        emptyText:'Fecha Fin...'
	    }),

        preparaMenu:function()
        {
            //this.getBoton('archivo').enable();
            Phx.vista.MovimientoEntidadSinPeriodo.superclass.preparaMenu.call(this);
            //

        },
        liberaMenu:function()
        {
            //this.getBoton('archivo').disable();
            Phx.vista.MovimientoEntidadSinPeriodo.superclass.liberaMenu.call(this);
            //
        },
        iniciarEventos : function () {
        	this.fecha_ini.on('change',function (field, newValue, oldValue) {
        		this.store.baseParams.fecha_inicio = newValue.dateFormat('d/m/Y');
        		if (this.fecha_fin.getValue() && this.primeraCarga)  {
        			this.primeraCarga = false;
        			this.load({params:{start:0, limit:this.tam_pag}});
        		} else if (this.fecha_fin.getValue() && !this.primeraCarga) {
        			this.onButtonAct();
        		}

        	},this);
        	this.fecha_fin.on('change',function (field, newValue, oldValue) {
        		this.store.baseParams.fecha_fin = newValue.dateFormat('d/m/Y');
        		if (this.fecha_ini.getValue() && this.primeraCarga) {
        			this.primeraCarga = false;
        			this.load({params:{start:0, limit:this.tam_pag}})
        		} else if (this.fecha_ini.getValue() && !this.primeraCarga) {
        			this.onButtonAct();
        		}

        	},this);
        }

    };
</script>
