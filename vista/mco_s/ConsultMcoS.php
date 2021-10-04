<?php
/**
*@package pXP
*@file ConsultMcoS.php
*@author  (breydi.vasquez)
*@date 28-04-2020 15:25:04
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
include_once('../../media/styles.php');
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ConsultMcoS=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    this.initButtons = ['-', '-',' <span style="font-size:12px;font-weight:bold;">Ingrese MCO &nbsp;&nbsp;&nbsp;&nbsp;</span>',this.cmbMco,'-','-'];
    Phx.vista.ConsultMcoS.superclass.constructor.call(this,config);
    // this.cmbMco.on('select',this.capturaFiltros,this);
    this.grid.getTopToolbar().items.items[7].hidden=true;    
    this.init();
    this.grid.body.dom.firstChild.childNodes[0].children[0].firstChild.style.textAlign='center';    
	},

	onButtonAct:function(){
		 //this.grid.getTopToolbar().items.items[2].getValue();
		 var nroMco = this.grid.getTopToolbar().items.items[3].getValue()
     if(nroMco!=''){
      this.store.baseParams.nroMco = nroMco;		  
     }else{
      this.store.baseParams.nroMco = '0000';
     }
     this.load();

	},

  	// capturaFiltros:function(combo, record, index){
		// 	console.log('delse',combo);
		// 	console.log('record',record);
    //   this.store.baseParams.id_mco=this.cmbMco.getValue();
  	// 	this.store.load({params:{start:0, limit:this.tam_pag}});
  	// },

	Atributos:[

        {
            config:{
                name: 'codigo',
                fieldLabel: '<span style="text-align: center; font-weight:bold;font-size:12px;">DETALLE MCO</span>',
                gwidth:1050,
                renderer: function (value, p, record) {

                  function format_fecha(fecha){
                    var day = fecha.getDate();
                    var month = fecha.getMonth() + 1;
                    var year = fecha.getFullYear();
                    var hour = fecha.getHours();
                    var min = fecha.getMinutes();
                    var sec = fecha.getSeconds();
                    var fdate = (month < 10)? day+'/'+0+month+'/'+year: day+'/'+month+'/'+year;
                    var ftime = (sec < 10)? hour+':'+min+':'+0+sec:hour+':'+min+':'+sec;
                    var obj = {'fecha': fdate, 'time': ftime};
                    return  obj;
                  }

                  var estado = (record.data['estado']==1)?'<b style="color:green;">VÁLIDO</b>':'<b style="color:red;">ANULADO</b>';
                  var fecha_emis = format_fecha(record.data['fecha_emision']);
                  var fecha_tkt = (record.data['fecha_doc_or']==null)?null:format_fecha(record.data['fecha_doc_or']);
                  var fecha_reg = format_fecha(record.data['fecha_reg']);


                    return `<div class="table_mco">
                    <table  class="t-interno-mco" cellpadding="5" cellspacing="5">
                        <tr>
                          <td width="350px"><b>Pais: </b><span>${record.data['pais_head']}</span></td>
                          <td width="280px"><b>Estacion: </b><span>${record.data['estacion_head']}</span></td>
                          <td width="350px"><b>Agt/PV: </b><span>${record.data['agt_tv_head']}</span></td>
                          <td width="900px"><b>Sucursal: </b><span>${record.data['nombre_suc_head']}</span></td>
                        </tr>
                    </table>
                    <table  class="t-interno-mco" cellpadding="5" cellspacing="5">
                        <tr>
                          <td width="184px"><b>T-Concepto: </b><span>${record.data['codigo']}</span></td>
                          <td width="154px"><b>Estado: </b>${estado}</td>
                          <td width="188px"><b>Fecha Emision: </b><span>${(fecha_emis==null)?'':fecha_emis.fecha}</span></td>
                          <td width="100px"><b>T-C: </b><span>${record.data['tipo_cambio']}</span></td>
                          <td width="100px"><b>Moneda: </b><span>${record.data['desc_moneda']}</span></td>
                        </tr>
                    </table>
                    <table  class="t-interno-mco" cellpadding="5" cellspacing="5">
                        <tr>
                          <td><b>Numero MCO:</b></td>
                          <td  width="260px"><b><span>${record.data['nro_mco']}</span></b></td>
                          <td  width="300px"><b>PAX: </b><span>${record.data['pax']}</span></td>
                        </tr>
                    </table>
                    <br>
                    <table class="t-interno-mco" cellpadding="5" cellspacing="5">
                        <tr>
                          <td width="900px" height="80px"><b>Motivo: </b><span>${record.data['motivo']}</span></td>
                        </tr>
                    </table>
                    <table  class="t-interno-mco" cellpadding="5" cellspacing="5">
                        <tr>
                          <td width="200px" ><b>Valor-Total: </b><span>${record.data['valor_total']}</span></td>
                          <td width="300px" ><b>Emitido por: </b><span>${record.data['desc_funcionario1']}</span></td>
                        </tr>
                    </table>
                    <table  class="t-interno-mco" cellpadding="5" cellspacing="5">
                        <tr>
                          <td width="200px"><b>Usr/Cajero: </b><span>${record.data['usr_reg']}</span></td>
                          <td width="200px"><b>Fecha-Reg: </b><span>${fecha_reg.fecha}</span></td>
                          <td width="150px"><b>Hora-Reg: </b><span>${fecha_reg.time}</span></td>
                        </tr>
                    </table>
                    <br>
                    <table  cellpadding="5" cellspacing="5">
                        <caption >Documentos Originales</caption>
                      <tr>
                        <th>TKT</th>
                        <th>Pais</th>
                        <th>Estacion</th>
                        <th>Fecha</th>
                        <th>T/C</th>
                        <th>Moneda</th>
                        <th>Valor-Total</th>
                        <th>Valor-Conv</th>
                      </tr>
                      <tr  class="tkt_footer">
                        <td width="200px">${record.data['tkt']}</td>
                        <td width="100px">${record.data['pais_doc_or']}</td>
                        <td width="100px">${record.data['estacion_doc_or']}</td>
                        <td width="100px">${(fecha_tkt==null)?'':fecha_tkt.fecha}</td>
                        <td width="100px">${(record.data['t_c_doc_or']==null)?'':record.data['t_c_doc_or']}</td>
                        <td width="100px">${record.data['moneda_doc_or']}</td>
                        <td width="150px">${(record.data['val_total_doc_or']==null)?0:record.data['val_total_doc_or']}</td>
                        <td width="150px">${record.data['val_conv_doc_or']}</td>
                      </tr>
                    </table>
                        </div>`
                  }
            },
            type:'TextField',
            id_grupo:1,
            grid:true
        },

	],
	tam_pag:50,
	title:'ConsultMCOs',
	ActList:'../../sis_obingresos/control/McoS/listarMcoS',
	id_store:'id_mco',
	fields: [
		{name:'id_mco', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'fecha_emision', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_moneda', type: 'numeric'},
		{name:'motivo', type: 'string'},
		{name:'valor_total', type: 'numeric'},
		{name:'id_documento_original', type: 'numeric'},
		{name:'id_gestion', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'id_boleto', type: 'numeric'},
		{name:'codigo', type: 'string'},
		{name:'desc_ingas', type: 'string'},
		{name:'codigo_internacional', type: 'string'},
		{name:'gestion', type: 'numeric'},
		{name:'tkt', type: 'string'},
		{name:'fecha_doc_or', type: 'date'},
		{name:'val_total_doc_or', type: 'numeric'},
		{name:'moneda_doc_or', type: 'string'},
		{name:'estacion_doc_or', type: 'string'},
		{name:'pais_doc_or', type: 'string'},
    {name:'id_punto_venta', type: 'numeric'},
    {name:'agt_tv_head', type: 'string'},
    {name:'estacion_head', type: 'string'},
    {name:'suc_head', type: 'string'},
    {name:'city_head', type: 'string'},
    {name:'pais_head', type: 'string'},
    {name:'desc_moneda', type: 'string'},
    {name:'concepto_codigo', type: 'string'},
    {name:'id_concepto_ingas', type: 'numeric'},
    {name:'tipo_cambio', type: 'numeric'},
    {name:'nro_mco', type: 'string'},
    {name:'pax', type: 'string'},
    {name:'id_funcionario_emisor', type: 'numeric'},
    {name:'desc_funcionario1', type: 'string'},
    {name:'t_c_doc_or', type: 'numeric'},
    {name:'val_conv_doc_or', type: 'numeric'},
    {name:'nombre_suc_head', type: 'numeric'},

	],
	sortInfo:{
		field: 'id_mco',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
  btest:false,
  bnew:false,
  bedit:false,

  cmbMco: new Ext.form.Field({
            fieldLabel: 'TKT-MCO',
            allowBlank: false,
            emptyText: 'Tkt-mco...',
						width: 200,
            // store: new Ext.data.JsonStore(
            //     {
            //         url: '../../sis_obingresos/control/McoS/listarTktFiltroConsul',
            //         id: 'id_mco',
            //         root: 'datos',
            //         sortInfo: {
            //             field: 'nro_mco',
            //             direction: 'ASC'
            //         },
            //         totalProperty: 'total',
            //         fields: ['id_mco','nro_mco', 'total', 'moneda'],
            //         remoteSort: true
            //     }),
            // valueField: 'id_mco',
            // hiddenValue: 'nro_mco',
            // displayField: 'nro_mco',
            // gdisplayField: 'nro_mco',
            // queryParam: 'nro_mco',
            // tpl:'<tpl for="."><div class="x-combo-list-item"><b>MCO:</b><p>{nro_mco}</p></div></tpl>',
            // listWidth: '180',
            // autoSelect: true,
            // typeAhead: false,
            // typeAheadDelay: 75,
            // hideTrigger: true,
            // triggerAction: 'query',
            // lazyRender: false,
            // mode: 'remote',
            // pageSize: 20,
            // queryDelay: 500,
            // anchor: '0%',
            // minChars: 4,
            // listWidth: '240',
            value:'930'
  })

})
</script>
