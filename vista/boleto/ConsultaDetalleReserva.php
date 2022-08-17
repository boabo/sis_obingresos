
<style type="text/css" rel="stylesheet">
.lista{
  width: '100%';
  font-family: Garamond,"Times New Roman", serif;
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

#detalle tr{background-color: #f2f2f2; text-align: center;}

#detalle th {
  padding-top: 5px;
  padding-bottom: 5px;
  text-align: center;
  background-color: #239B56;
  color: white;
  font-size: 14px;
  font-weight: bold;
}

#vuelo {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#vuelo td, #vuelo th {
  border: 1px solid #ddd;
  padding: 5px;
}

#vuelo tr{background-color: #f2f2f2; text-align: center;}

#vuelo th {
  padding-top: 5px;
  padding-bottom: 5px;
  text-align: center;
  background-color: rgba(31, 54, 86, 0.9);
  color: white;
  font-size: 14px;
  font-weight: bold;
}

#table-header tr td{
  font-size: 14px;
  font-family: initial;
  font-weight: bold;  
}
.spacio_letra {
  letter-spacing: 4px;
  padding-left: 5px;
}
.f_text{
  color: #1F3656;
  font-weight: bold;
}
.capti{
  text-align: center;
  font-weight: bold;
  letter-spacing: 10px;
  margin: 15px 0 5px 0;
  box-shadow: 0px 5px 5px -2px black;
}
</style>
<script>

var me = null
if(screen.width<=1440)
{
  wdf=1250;
}
else if (screen.width<=1920) 
{
  wdf=1670;
}

Phx.vista.ConsultaDetalleReserva=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config)
  {
		this.maestro=config.maestro;
    Phx.vista.ConsultaDetalleReserva.superclass.constructor.call(this,config);
    this.init();
    this.load({params:{start:0,limit:2, pnr: this.pnr}});
    this.tbar.el.dom.parentNode.style.height=0;
    this.bbar.container.dom.firstChild.style.height=0;
    this.grid.view.innerHd.style.height=0;
    me = this
	},

	Atributos:[

        {
            config:{
                name: 'detalle',
                gwidth:wdf,
                renderer: function (value, p, record) 
                { console.log('vuelos',value);                                   
                  const pasajero = detalleReserva(value, 'pasajero');
                  const vuelo = detalleReserva(value, 'vuelo');
                  const fecha_creacion = cambioFecha(value.fecha_creacion, '/');                  
                  const hora_creacion = cambioFecha(value.hora_creacion, ':');
                  const fecha_limite = value.tl.fecha_limite.split(' ');
                  
                  var info =`	<div class="lista">
                                <table width="100%">
                                <tr>
                                  <td colspan="2">
                                      <table width="100%" id="table-header">
                                        <tr>
                                          <td>NRO DE RESERVA: <span class="spacio_letra">${value.localizador_resiber}</span></td>
                                          <td>FECHA CREACIÓN: <span class="spacio_letra">${fecha_creacion}</span></td>
                                          <td>FECHA LÍMITE: <span class="spacio_letra">${moment(fecha_limite[0]).format("DD/MM/YYYY")} ${fecha_limite[1]}</span></td>
                                        </tr>
                                      </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td style="background: beige;" colspan="2">
                                    <hr>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <table id="detalle">
                                    <caption class="capti">PASAJERO/S</caption>
                                      <tr>
                                        <th>Nombre</th>
                                        <th>Moneda</th>
                                        <th>Importe</th>                                        
                                      </tr>
                                      ${pasajero}   
                                    </table>
                                  </td>
                                  <td>
                                    <table id="vuelo">
                                    <caption class="capti">VUELO/S</caption>
                                      <tr>
                                        <th>Fecha Salida</th>
                                        <th>Nº Vuelo</th>
                                        <th>Origen</th>
                                        <th>Hora Salida</th>
                                        <th>Destino</th>
                                        <th>Hora Llegada</th>                                        
                                      </tr>
                                      ${vuelo}   
                                    </table>
                                  </td>                                  
                                </tr>                                                                 
                            </div>`;
                  return info;
                }
            },
            type:'TextField',
            id_grupo:1,
            grid:true
        },

	],
	title:'Detalle Reserva',
	ActList:'../../sis_obingresos/control/Boleto/consultaDetalleReserva',
	fields: [ {name:'detalle', type: 'text'} ],

	bdel:false,
	bsave:false,
  btest:false,
  bnew:false,
  bedit:false,
  bact:false,
  bexport:false

});

function plantilla (arrayData) 
{
  let row = '';
  
  if (arrayData.length > 1)
  {
    row = `<tr>`;
    arrayData.forEach( value => {
      row +=`<td>${value}</td>`;                 
    });
    row += `</tr>`;
  }

  return row;
}

function detalleReserva (data, valor) 
{  
  const ps = data.pasajeros.pasajeroDR;
  const vu = data.vuelos.vuelo;  
  let response = '';
  const arrayValores = [];

  if (valor == 'pasajero')
  {
    if (Array.isArray(ps))
    {
      ps.forEach(e => {
        response += `<tr>
                    <td>${e.apdos_nombre}</td>
                    <td>${e.pago.moneda}</td>
                    <td>${Ext.util.Format.number(e.pago.importe,'0.000,00/i')}</td>
                    </tr>`;
      })
    }            
    else
    {
      arrayValores.push(ps.apdos_nombre, ps.pago.moneda, ps.pago.importe);
      response = plantilla(arrayValores);
    }                    
  }

  if (valor == 'vuelo')
  {
    if (Array.isArray(vu))
    {
      vu.forEach(e => {
        response += `<tr>
                    <td>${e.fecha_salida}</td>
                    <td>${e.num_vuelo}</td>
                    <td>${e.origen}</td>
                    <td>${cambioFecha(e.hora_salida, ':')}</td>
                    <td>${e.destino}</td>
                    <td>${cambioFecha(e.hora_llegada, ':')}</td>
                    </tr>`;
      })       
    }            
    else
    {
      arrayValores.push(vu.fecha_salida, vu.num_vuelo, vu.origen, cambioFecha(vu.hora_salida, ':'), vu.destino, cambioFecha(vu.hora_llegada, ':'));
      response = plantilla(arrayValores);
    }         
  }
  
  return response;
}

function cambioFecha(f, separador)
{  
  return f.split('').map((v, i) => (i % 2 == 0)? v = i == 0? v: separador + v : v).join('');  
}


</script>
