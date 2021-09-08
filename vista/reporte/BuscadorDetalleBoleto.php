<?php
/**
 * @package pxP
 * @file    BuscadorDetalleBoleto.php
 * @author  Franklin Espinoza Alvarez
 * @date    21-09-2012
 * @description Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
header("content-type:text/javascript; charset=UTF-8");
?>

<style type="text/css" rel="stylesheet">
    .x-selectable,
    .x-selectable * {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    .x-grid-row td,
    .x-grid-summary-row td,
    .x-grid-cell-text,
    .x-grid-hd-text,
    .x-grid-hd,
    .x-grid-row,

    .x-grid-row,
    .x-grid-cell,
    .x-unselectable
    {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    /** ***************************************** **************************************** **/

    .lista{
        width: '100%';
        font-family: Garamond,"Times New Roman", serif;
    }
    .item1{
        width: 1250px;
        border: solid 1px black;
    }
    .item2{
        width: 600px;
        /* border: solid 1px black; */
    }
    #detalle {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #detalle td, #detalle th {
        border: 1px solid #ddd;
        padding: 5px;
    }

    #detalle tr{background-color: #f2f2f2;}

    #detalle th {
        padding-top: 5px;
        padding-bottom: 5px;
        text-align: center;
        background-color: #239B56;
        color: white;
        font-size: 14px;
        font-weight: bold;
    }
    #con_pago {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #con_pago td, #con_pago th {
        border: 1px solid #ddd;
        padding: 5px;
    }

    #con_pago tr{background-color: #f2f2f2;}

    #con_pago th {
        padding-top: 5px;
        padding-bottom: 5px;
        text-align: center;
        background-color: rgba(31, 54, 86, 0.9);
        color: white;
        font-size: 14px;
        font-weight: bold;
    }
    #bol_asoc {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #bol_asoc td, #bol_asoc th {
        border: 1px solid #ddd;
        padding: 5px;
    }

    #bol_asoc tr{background-color: #f2f2f2;}

    #bol_asoc th {
        padding-top: 5px;
        padding-bottom: 5px;
        text-align: center;
        background-color: #ff4700;
        color: white;
        font-size: 14px;
        font-weight: bold;
    }
    #deposito {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #deposito td, #deposito th {
        border: 1px solid #ddd;
        padding: 5px;
    }

    #deposito tr{background-color: #f2f2f2;}

    #deposito th {
        padding-top: 5px;
        padding-bottom: 5px;
        text-align: center;
        background-color: #8E44AD;
        color: white;
        font-size: 14px;
        font-weight: bold;
    }
    .capti{
        text-align: center;
        font-weight: bold;
        letter-spacing: 10px;
        margin: 15px 0 5px 0;
        box-shadow: 0px 5px 5px -2px black;
    }
    #table-header b{
        font-size: 14px;
        font-family: initial;
    }
    .f_text{
        color: #1F3656;
        font-weight: bold;
    }
</style>
<script>
    if(screen.width<=1440){
        wdf=1250;
    }else if (screen.width<=1920) {
        wdf=1670;
    }
    Phx.vista.BuscadorDetalleBoleto = Ext.extend(Phx.gridInterfaz, {

        /*viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },*/
        constructor : function(config) {

            this.maestro = config.maestro;

            Phx.vista.BuscadorDetalleBoleto.superclass.constructor.call(this, config);

            this.txtSearch = new Ext.form.TextField();
            this.txtSearch.enableKeyEvents = true;
            this.txtSearch.maxLength = 13;
            this.txtSearch.maxLengthText = 'Ha exedido el numero de caracteres permitidos';
            this.txtSearch.msgTarget = 'under';

            this.txtSearch.on('specialkey', this.onTxtSearchSpecialkey, this);

            this.txtSearch.on('keyup', function (field, e) {

                if(this.txtSearch.getValue().length == 15) {
                    this.store.baseParams.nro_ticket = field.getValue();
                    this.load({params: {start: 0, limit: 7}});
                }

            }, this);

            this.tbar.add(this.txtSearch);

            this.addButton('btnBuscar', {
                text : 'Buscar',
                iconCls : 'bzoom',
                disabled : false,
                handler : this.onBtnBuscar
            });

            this.init();

        },
        preparaMenu:function(tb){
            Phx.vista.BuscadorDetalleBoleto.superclass.preparaMenu.call(this,tb);
            var data = this.getSelectedData();
        },

        liberaMenu:function(tb){
            Phx.vista.BuscadorDetalleBoleto.superclass.liberaMenu.call(this,tb);
        },

        Atributos:[
            {
                config:{
                    name: 'datajson',
                    gwidth:wdf,
                    renderer: function (value, p, record) { console.log('valores', value, p, record);
                        /*var obj = JSON.parse(record.json.jsondata).data;
                        console.log("data",obj);
                        var title = '';
                        var detalle = obj.detalle_venta;
                        var total_det = 0;
                        var total_ca = 0;
                        var total_uni = 0;
                        var form_pago = obj.formas_pago;
                        var total_fp = 0;
                        var bolasoc = obj.bolasoc;
                        var deposito = obj.deposito;
                        var total_depo = 0;
                        (obj.anticipo != null)?title_ant = 'ANTICIPO':title_ant='';
                        var info =`	<div class="lista">
                            <table width="100%">
                            <tr>
                                <td>
                                    <table width="100%" align="center" id="table-header">
                                        <tr>`;
                        if(obj.tipo_factura=='recibo' || obj.tipo_factura == 'recibo_manual'){
                            info += `
                                          <td width="2%" style="font-size:14px;">
                                              <b>N° ${obj.tit_fac}: </b><span class="f_text">${obj.nro_factura}</span>`;
                            if (obj.nit!='' || obj.nit!=null){
                                info +=  `<br><br><b>Nit: </b><span class="f_text">${obj.nit}</span>`;
                            }
                            info +=`<br><br>
                                          <b>Razon Social: </b><span class="f_text">${obj.nombre_factura}</span>
                                              <br><br>
                                              <b>Sucursal: </b><span class="f_text">${obj.sucursal}</span>
                                          </td>
                                          <td width="3%" style="font-size:14px;">
                                              <b>Fecha: </b><span class="f_text">${obj.fecha_factura.split("-").reverse().join("/")}</span>
                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp<b>Estado: </b> ${(obj.estado=='anulado')?'<span class="f_text" style="background:#E74C3C;color:white;">ANULADO</span>':'<span class="f_text" style="background:#28B463;color:white;">VALIDA</span>'}
                                              <br><br>
                                              <b>Usuario Emisor: </b><span class="f_text">${obj.desc_persona}</span>
                                              <br><br>
                                              <b>P-Venta/Agencia: </b><span class="f_text">${obj.punto_venta}</span></td>

                                          </td>
                                              <td width="3%" style="font-size:14px;">
                                              <b>Total: </b><span class="f_text">${Ext.util.Format.number(obj.total_venta,'0.000,00/i')}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Exento: </b><span class="f_text">${Ext.util.Format.number(obj.excento,'0.000,00/i')}</span>
                                              ${(obj.comision>0)?'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp<b>Comision: </b><span class="f_text">'+Ext.util.Format.number(obj.comision,'0.000,00/i')+'</span>':''}
                                              <br><br>
                                              <b>Importe Base Para Credito Fiscal: </b> <span class="f_text">${Ext.util.Format.number((obj.total_venta - obj.excento),'0.000,00/i')}</span>
                                              <br><br>
                                              <b>Observaciones: </b><span style="font-size:10pt;">${(obj.observaciones == null || obj.observaciones=='')?'':obj.observaciones.toLowerCase()}</span>
                                          </td>
                                          `;
                        }else{
                            info += `
                                          <td width="2%" style="font-size:14px;">
                                              <b>N° ${obj.tit_fac}: </b><span class="f_text">${obj.nro_factura}</span>
                                              <br><br>
                                              <b>Código de Control: </b><span class="f_text">${(obj.cod_control == null)?'':obj.cod_control}</span>
                                              <br><br>
                                              <b>N° Autorizacion: </b><span class="f_text">${(obj.nroaut == null || obj.nroaut==''||obj.nroaut==undefined)?'':obj.nroaut}</span>
                                              <br><br>
                                              <b>Sucursal: </b><span class="f_text">${obj.sucursal}</span>
                                          </td>
                                          <td width="3%" style="font-size:14px;">
                                              <b>Fecha: </b><span class="f_text">${obj.fecha_factura.split("-").reverse().join("/")}</span>
                                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp<b>Estado: </b> ${(obj.estado=='anulado')?'<span class="f_text" style="background:#E74C3C;color:white;">ANULADO</span>':'<span class="f_text" style="background:#28B463;color:white;">VALIDA</span>'}
                                              <br><br>
                                              <b>Nit: </b><span class="f_text">${obj.nit}</span>
                                              <br><br>
                                              <b>Razon Social: </b><span class="f_text">${obj.nombre_factura}</span>
                                              <br><br>
                                              <b>P-Venta/Agencia: </b><span class="f_text">${obj.punto_venta}</span></td>

                                          </td>
                                              <td width="3%" style="font-size:14px;">
                                              <b>Total: </b><span class="f_text">${Ext.util.Format.number(obj.total_venta,'0.000,00/i')}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Exento: </b><span class="f_text">${Ext.util.Format.number(obj.excento,'0.000,00/i')}</span>
                                              ${(obj.comision>0)?'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Comision: </b><span class="f_text">'+Ext.util.Format.number(obj.comision,'0.000,00/i')+'</span>':''}
                                              <br><br>
                                              <b>Importe Base Para Credito Fiscal: </b> <span class="f_text">${Ext.util.Format.number((obj.total_venta - obj.excento),'0.000,00/i')}</span>
                                              <br><br>
                                              <b>Usuario Emisor: </b><span class="f_text">${obj.desc_persona}</span>
                                              <br><br>
                                              <b>Observaciones: </b><span style="font-size:10pt;">${(obj.observaciones == null || obj.observaciones=='')?'':obj.observaciones.toLowerCase()}</span>
                                          </td>
                                          `;
                        }

                        info +=`  </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="background: beige;">
                                  <hr>
                                </td>
                            </tr>`;
                        if (detalle!=null){
                            info += `
                                    <tr>
                                        <td class="item2">
                                        <table id="detalle">
                                        <caption class="capti">CONCEPTOS VENTA</caption>
                                          <tr>
                                            <th>Tipo</th>
                                            <th>Concepto</th>
                                            <th>Moneda</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Total</th>
                                            <th>Descripcion</th>
                                          </tr>`;
                            detalle.forEach( e => {
                                total_det = total_det + (e.cantidad*e.precio);
                                total_ca = total_ca + e.cantidad;
                                total_uni = total_uni + e.precio;
                                info += `
                                                <tr>
                                                    <td>${e.tipo}</td>
                                                    <td>${e.desc_ingas}</td>
                                                    <td align="center">${e.moneda_det}</td>
                                                    <td align="center">${e.cantidad}</td>
                                                    <td align="center">${Ext.util.Format.number(e.precio,'0.000,00/i')}</td>
                                                    <td align="center">${Ext.util.Format.number((e.cantidad*e.precio),'0.000,00/i')}</td>
                                                    <td>${(e.descripcion==null || e.descripcion=='' )?'':e.descripcion}</td>
                                                </tr>
                                            `;
                            });

                            info += `<tr><td colspan="3" align="center"><b>TOTAL DETALLE</b></td><td align="center"><b>${total_ca}</b></td><td align="center"><b>${Ext.util.Format.number(total_uni,'0.000,00/i')}</b></td><td align="center"><b>${Ext.util.Format.number(total_det,'0.000,00/i')}</b></td><td></td></tr>
                                        </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                          <hr>
                                        </td>
                                    </tr>
                                    `;
                        }
                        if (form_pago!=null){
                            info += `
                                  <tr>
                                    <td class="item2">
                                      <table id="con_pago">
                                      <caption class="capti">FORMAS DE PAGO ${title_ant}</caption>
                                        <tr>
                                          <th>Medio de Pago</th>
                                          <th>Moneda</th>
                                          <th>Monto  Transaccion</th>
                                          <th>Tipo Tarjeta</th>
                                          <th>N° Tarjeta</th>
                                          <th>Codigo Tarjeta</th>
                                          <th>N° Cuenta</th>
                                        </tr>
                                      `;
                            form_pago.forEach( e => {
                                total_fp = total_fp + e.monto_transaccion;
                                info += `
                                          <tr>
                                              <td>${e.name}</td>
                                              <td align="center">${e.moneda_fp}</td>
                                              <td align="center">${Ext.util.Format.number(e.monto_transaccion,'0.000,00/i')}</td>
                                              <td align="center">${e.tipo_tarjeta}</td>
                                              <td align="center">${e.numero_tarjeta}</td>
                                              <td align="center">${e.codigo_tarjeta}</td>
                                              <td align="center">${(e.cod_cuenta==null)?'':e.cod_cuenta}</td>
                                          </tr>
                                          `;
                            });
                            info +=`<tr><td colspan="2" align="center"><b>TOTAL FORMA PAGO</b></td><td align="center"><b>${Ext.util.Format.number(total_fp,'0.000,00/i')}</b></td><td colspan="4"></td></tr>
                                      </table>
                                    </td>
                                  </tr>
                                  <tr>
                                      <td>
                                        <hr>
                                      </td>
                                  </tr>
                                  `;
                        }
                        if (deposito != null){
                            info += `
                                <tr>
                                    <td class="item2">
                                    <table id="deposito">
                                    <caption class="capti">DEPÓSITO</caption>
                                    <tr>
                                      <th>N° Deposito</th>
                                      <th>Moneda</th>
                                      <th>Monto Total</th>
                                      <th>Fecha</th>
                                    </tr>
                                    `;
                            deposito.forEach( e => {
                                total_depo = total_depo + e.monto_total;
                                info += `
                                      <tr>
                                          <td align="center">${e.nro_deposito}</td>
                                          <td align="center">${e.moneda_dep}</td>
                                          <td align="center">${Ext.util.Format.number(e.monto_total,'0.000,00/i')}</td>
                                          <td align="center">${(e.fecha==null)?'':e.fecha.split("-").reverse().join("/")}</td>
                                      </tr>
                                      `;
                            });
                            info += `<tr><td colspan="2" align="center"><b>TOTAL DEPÓSITO</b></td><td align="center"><b>${Ext.util.Format.number(total_depo,'0.000,00/i')}</b></td><td></td></tr>
                                    </table>
                                     </td>
                                   </tr>
                                   <tr>
                                       <td>
                                         <hr>
                                       </td>
                                   </tr>
                                   `;
                        }
                        if (bolasoc != null){
                            info += `
                                <tr>
                                    <td class="item2">
                                    <table id="bol_asoc">
                                    <caption class="capti">BOLETO ASOCIADO</caption>
                                    <tr>
                                      <th>N° Boleto</th>
                                      <th>Nit</th>
                                      <th>Pasajero</th>
                                      <th>Fecha Emision</th>
                                      <th>Razon</th>
                                      <th>Ruta</th>
                                      <th>Estado</th>
                                    </tr>
                                    `;
                            bolasoc.forEach( e => {
                                info += `
                                  <tr>
                                      <td align="center">${e.nro_boleto}</td>
                                      <td align="center">${e.nit == null && ''}</td>
                                      <td>${(e.pasajero == null)?'':e.pasajero}</td>
                                      <td align="center">${(e.fecha_emision==null)?'':e.fecha_emision.split("-").reverse().join("/")}</td>
                                      <td>${(e.razon == null)?'':e.razon}</td>
                                      <td>${(e.ruta == null)?'':e.ruta}</td>
                                      <td align="center">${e.estado_reg}</td>
                                  </tr>
                                  `;
                            });
                            info += `</table>
                                 </td>
                               </tr>`;
                        }

                        info +=`</table>
                  </div>
                  `;
                        return info;*/
                    }
                },
                type:'TextField',
                id_grupo:1,
                grid:true
            }
        ],
        ActList:'../../sis_obingresos/control/Reportes/getTicketInformationRecursive',
        fields: [
            {name:'datajson', type: 'text'}
        ],

        bdel:false,
        bsave:false,
        bnew:false,
        bedit:false,

        onBtnBuscar : function() {
            this.store.baseParams.nro_ticket = ((this.txtSearch.getValue()).trim()).toUpperCase();
            this.load({params: {start: 0, limit: 7}});
            //this.grid.getSelectionModel().selectFirstRow();
        },

        onTxtSearchSpecialkey : function(field, e) {

            if (e.getKey() == e.ENTER) {
                this.onBtnBuscar();
            }
        },

        onCombinacion: function(e) {

            if (e.getKey() == 17) {
                console.log('especial');
                this.onBtnBuscar();
            }
        }
    });
</script>
