CREATE OR REPLACE FUNCTION obingresos.ft_reporte_cuenta_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_reporte_cuenta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.treporte_cuenta'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        11-06-2018 15:14:58
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				11-06-2018 15:14:58								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.treporte_cuenta'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_id_moneda_mb		integer;
    v_id_moneda_base	integer;
    v_id_moneda_usd		integer;
    v_record 			record;
    v_contador 			integer = 0;
	v_saldo 			numeric;
    v_periodo			varchar;
    v_aux				numeric;
    v_monto_str			varchar;
    v_where				varchar;
    v_where_tipo 		varchar;
    v_where_forma		varchar;
    v_records			record;
    v_calculo 			numeric;
    v_mes				integer;
    v_dia				integer;
    v_id_periodo_veta	integer;

    v_creditos			record;
    v_acomodar			record;
    v_debitos			record;
    v_credito_total		record;
    v_debito_total 		record;
    v_debitos_fecha		record;
    v_crevigente		record;
    v_fecha_ultima		record;
    v_creditos_fuera	record;
    v_monto_anterior	record;
    v_garantia			record;
    v_ajustes			record;
    v_fecha_maxima		date;
    v_id_periodo_venta_ini integer;
    v_id_periodo_venta_fin	integer;
    v_fechas_periodos	record;

BEGIN

	v_nombre_funcion = 'obingresos.ft_reporte_cuenta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ENT_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		IVALDIVIA
 	#FECHA:		02-01-2018 20:57:30
	***********************************/

	if(p_transaccion='OBING_ENT_SEL')then

    	begin

        CREATE TEMPORARY TABLE temp ( id_agencia  int4,
                                      nombre varchar,
                                      tipo varchar,
                                      pnr varchar,
          							  fecha date,
                                      autorizacion__nro_deposito text,
                                      billete varchar,
                                      comision numeric,
                                      importe numeric,
                                      neto numeric,
                                      saldo numeric,
                                      contador integer,
                                      tipo_credito varchar,
                                      tipo_ajustes varchar,
                                      garantia numeric )ON COMMIT DROP;




        FOR v_record in (select mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                mo.pnr,
                                de.fecha,
                                mo.pnr ||' - '||de.billete  as autorizacion__nro_deposito,
                                de.billete,
                                de.comision,
                                de.importe,
                                de.neto,
                                de.importe -  de.comision as saldo
                        from obingresos.tmovimiento_entidad mo
                        inner join obingresos.tdetalle_boletos_web de on de.numero_autorizacion = mo.autorizacion__nro_deposito and de.void = 'no'
                        inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'debito'  and mo.estado_reg = 'activo'
                     	and mo.fecha between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date

                        union

                        select mo.id_agencia,
                                ag.nombre,
                                'ajustes':: varchar as tipo,
                                mo.pnr,
                                --null::date as fecha,
                                mo.fecha,
                                mo.autorizacion__nro_deposito as autorizacion__nro_deposito,
                                ' '::varchar as billete,
                                0::numeric as comision,
                                 (case when mo.id_moneda = 1 then
                            	mo.monto
                           		 else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)
                                end)as importe,
                                (case when mo.id_moneda = 1 then
                            	mo.neto
                           		 else
                            	param.f_convertir_moneda(2,1,mo.neto,mo.fecha,'O',2)
                                end)as neto,
                                (case when mo.id_moneda = 1 then
                            	mo.monto_total
                           		 else
                            	param.f_convertir_moneda(2,1,mo.monto_total,mo.fecha,'O',2)
                                end) as saldo
                        from obingresos.tmovimiento_entidad mo
                        inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'debito'
                        and mo.estado_reg = 'activo' and mo.ajuste = 'si'
						and mo.fecha between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date

                        )

                      LOOP


                    insert into	temp (  id_agencia,
                                        nombre,
                                        tipo,
                                        pnr,
                                        fecha,
                                        autorizacion__nro_deposito,
                                        billete,
                                        comision,
                                        importe,
                                        neto

                                        )
                                        values(
                                        v_record.id_agencia,
                                        v_record.nombre,
                                        v_record.tipo,
                                        v_record.pnr,
                                        v_record.fecha,
                                        v_record.autorizacion__nro_deposito,
                                        v_record.billete,
                                        v_record.comision,
                                        (v_record.saldo + v_record.comision),
                                        v_record.neto
                                      );

    	END LOOP;


        for v_creditos in (
        	select  mo.id_agencia,
                    mo.pnr,
                    mo.tipo,
                    mo.fecha,
                      sum(case when mo.ajuste = 'no' and  mo.garantia = 'no'   then
                              (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                              end)
                      else
                          0
                      end) as monto_credito,

                       sum(case when  mo.garantia = 'si' and mo.id_periodo_venta is null then
                          (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                              end)
                      else
                          0
                      end) as garantia,

                      sum(case when mo.ajuste = 'si'  then
                              (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                              end)
                      else
                          0
                      end) as ajuste_credito
                      from obingresos.tmovimiento_entidad mo
                      where mo.fecha between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date and
                       mo.cierre_periodo = 'no' and
                       mo.estado_reg = 'activo' and
                       mo.tipo = 'credito'
                       and mo.id_agencia = v_parametros.id_agencia
                      group by mo.id_agencia, mo.fecha, mo.pnr, mo.tipo

                      union


                      select 	mo.id_agencia,
                      			''::varchar as pnr,
                               	mo.tipo,
                                mo.fecha,
                                (case when mo.id_moneda = 1 then
                            	mo.monto
                           		 else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)
                                end)as monto_credito,
                                null::numeric as garantia,
                                null::numeric as ajuste_credito
                        from obingresos.tmovimiento_entidad mo
                        inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and mo.cierre_periodo = 'no' and mo.garantia = 'no'
                        and mo.ajuste = 'si'
                        and mo.fecha between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date




        ) loop
                 insert into	temp (
                                        tipo_credito,
                                        fecha,
                                        importe
                                        --garantia
                                        )
                                        values(
                                        v_creditos.tipo,
                                        v_creditos.fecha,
                                        v_creditos.monto_credito
                 						--v_creditos.garantia
                                        );

        end loop;

        for v_garantia in (
        	select  mo.id_agencia,
        mo.tipo ,
        sum(case when  mo.garantia = 'si' and mo.id_periodo_venta is null then
        (case when mo.id_moneda = 1 then
                            mo.monto
                        else
                            param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                            end)
        else
            0
        end) as garantia

        from obingresos.tmovimiento_entidad mo
        where mo.fecha between v_parametros.fecha_ini::date and v_parametros.fecha_fin::date and
         mo.cierre_periodo = 'no' and
         mo.estado_reg = 'activo' and
         mo.tipo = 'credito'
         and mo.id_agencia = v_parametros.id_agencia
        group by mo.id_agencia,mo.tipo ) LOOP
        	insert into	temp (
                                        tipo_credito,
                                        garantia
                                        )
                                        values(
                                        v_garantia.tipo,
                                        v_garantia.garantia
                                        );
        end loop;

    		--Sentencia de la consulta


			v_consulta:='select id_agencia,
                                nombre,
                                tipo,
                                pnr,
                                fecha,
                                autorizacion__nro_deposito,
                                billete,
                                comision,
                                importe,
                                neto,
                                saldo,
                                tipo_credito,
                                tipo_ajustes,
                                garantia
                                from temp
                                order by fecha';

			--Devuelve la respuesta
			return v_consulta;

		end;

     /*********************************
     #TRANSACCION:  'OBING_ANTERIOR_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		04-12-2018
    ***********************************/

    elsif(p_transaccion='OBING_ANTERIOR_SEL')then
	begin

        v_consulta = '
              with credito as (	select  mo.id_agencia,
                      sum(case when mo.ajuste = ''no'' and  mo.garantia = ''no''   then
                              (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as monto_credito,

                       sum(case when  mo.garantia = ''si'' and mo.id_periodo_venta is null then
                          (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as garantia,

                      sum(case when mo.ajuste = ''si''  then
                              (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as ajuste_credito
                      from obingresos.tmovimiento_entidad mo
                      where mo.fecha >= ''8/1/2017'' and mo.fecha < '''||v_parametros.fecha_ini||''' and
                       mo.cierre_periodo = ''no'' and
                       mo.estado_reg = ''activo'' and
                       mo.tipo = ''credito''
                      group by mo.id_agencia),
              debitos as (select  mo.id_agencia,
              sum(case when mo.ajuste = ''no'' then
                          (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as monto_debito,
                      sum(case when mo.ajuste = ''si'' then
                          (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as ajuste_debito
              from obingresos.tmovimiento_entidad mo
              where mo.fecha >= ''8/1/2017'' and mo.fecha < '''||v_parametros.fecha_ini||''' and
                    mo.tipo = ''debito'' and
                    mo.cierre_periodo = ''no'' and
                    mo.estado_reg = ''activo''
              group by mo.id_agencia)

                      select  ag.id_agencia,
                              ag.nombre,
                              --cr.id_periodo_venta,
                              --ag.codigo_int,
                              ag.tipo_agencia,

                              COALESCE ( cr.monto_credito, 0 )as monto_creditos,
                              cr.garantia as codigo_ciudad,
                              COALESCE( de.monto_debito, 0 ) as monto_debitos,
                              COALESCE(cr.ajuste_credito,0) as ajuste_credito,
                              COALESCE(de.ajuste_debito,0) as ajuste_debito,
                             ( COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as monto_ajustes,
                             --( COALESCE(cr.garantia,0)+ COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0) )+(COALESCE(cr.ajuste_credito,0)- COALESCE(de.ajuste_debito,0)) as saldo_con_boleto,
                             ( COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0)) + (COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as saldo_sin_boleto_ant
                      from obingresos.tagencia ag
                      inner join credito cr on cr.id_agencia = ag.id_agencia
                      left join  debitos de on de.id_agencia = ag.id_agencia
                      where ag.id_agencia = '||v_parametros.id_agencia||'
                      order by nombre';


       --Devuelve la respuesta
    return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_ENT_RE'
 	#DESCRIPCION:	Resumen Estado Cuenta Corriente
 	#AUTOR:		jrivera
 	#FECHA:		08-04-2016 22:44:37
	***********************************/
    elsif(p_transaccion='OBING_ENT_RE')then

    	begin

          CREATE TEMPORARY TABLE temp ( id_agencia  int4,
                                  	 	tipo varchar,
                                      	moneda varchar,
                                      	monto numeric,
                                      	monto_mb numeric,
                                        aux varchar
                                       )ON COMMIT DROP;


        select param.f_get_moneda_base() into v_id_moneda_mb;
        select m.id_moneda into v_id_moneda_usd
        from param.tmoneda m
        where m.codigo_internacional = 'USD';
        FOR v_records in (
select 	me.id_agencia,
		'boleta_garantia'::varchar as tipo,
		mon.codigo_internacional,
        COALESCE(me.monto,0)as monto,
        COALESCE (param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd ),0) as monto_mb,
        'a'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
        me.garantia = 'si' and me.id_moneda = v_id_moneda_mb
        union all
select 	me.id_agencia,
        'boleta_garantia'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(me.monto,0)as monto,
        COALESCE(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd ),0) as monto_mb,
        'a'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
        me.garantia = 'si' and me.id_moneda = v_id_moneda_usd
        union all
select  me.id_agencia,
		'saldo_anterior'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(me.monto,0),
        COALESCE(me.monto, param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd ),0) as monto_mb,
        'b'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
        me.garantia = 'no' and me.ajuste = 'no' and me.cierre_periodo = 'si' and
        me.id_moneda = v_id_moneda_mb
        union all
select 	me.id_agencia,
		'saldo_anterior'::varchar as tipo,
        mon.codigo_internacional,
         COALESCE(me.monto,0),
        COALESCE(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd ),0) as monto_mb,
        'b'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
        me.garantia = 'no' and me.ajuste = 'no' and me.cierre_periodo = 'si' and
        me.id_moneda = v_id_moneda_usd
        union all
select 	me.id_agencia,
		'deposito'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'c'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
        me.garantia = 'no' and me.ajuste = 'no' and me.cierre_periodo = 'no' and
        me.id_moneda = v_id_moneda_mb
        group by me.id_agencia,mon.codigo_internacional
        union all
select 	me.id_agencia,
		'deposito'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'c'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
        me.garantia = 'no' and me.ajuste = 'no' and me.cierre_periodo = 'no' and
        me.id_moneda = v_id_moneda_usd
        group by me.id_agencia,mon.codigo_internacional
        union all
select  me.id_agencia,
		'comision'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto_total-me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto_total-me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'd'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'debito' and
        me.ajuste = 'no' and me.pnr is not null and
        me.id_moneda = v_id_moneda_mb
        group by me.id_agencia,mon.codigo_internacional
        union all
select 	me.id_agencia,
		'comision'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto_total-me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto_total-me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'd'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'debito' and
        me.ajuste = 'no' and me.pnr is not null and
        me.id_moneda = v_id_moneda_usd
        group by me.id_agencia,mon.codigo_internacional
        union all
select 	me.id_agencia,
		'otro_credito'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'e'::varchar as aux
from obingresos.tmovimiento_entidad me
inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
where me.estado_reg = 'activo' and me.id_periodo_venta is null
and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
me.garantia = 'no' and me.ajuste = 'si' and me.cierre_periodo = 'no' and
me.id_moneda = v_id_moneda_mb
group by me.id_agencia,mon.codigo_internacional
union all
select 	me.id_agencia,
		'otro_credito'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'e'::varchar as aux
from obingresos.tmovimiento_entidad me
inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
where me.estado_reg = 'activo' and me.id_periodo_venta is null
and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'credito' and
me.garantia = 'no' and me.ajuste = 'si' and me.cierre_periodo = 'no' and
me.id_moneda = v_id_moneda_usd
group by me.id_agencia,mon.codigo_internacional
 union all

select 	me.id_agencia,
		'boleto'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto_total),0) as monto,
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto_total,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'f'::varchar as aux
        from obingresos.tmovimiento_entidad me
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'debito' and
        me.ajuste = 'no' and me.pnr is not null and
        me.id_moneda = v_id_moneda_mb
        group by me.id_agencia,mon.codigo_internacional
union all
select 	me.id_agencia,
		'boleto'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto_total),0) as monto,
       COALESCE( sum(param.f_convertir_moneda(me.id_moneda,v_id_moneda_mb ,me.monto_total,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
       'f'::varchar as aux
from obingresos.tmovimiento_entidad me
inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
where me.estado_reg = 'activo' and me.id_periodo_venta is null
and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'debito' and
me.ajuste = 'no' and me.pnr is not null and
me.id_moneda = v_id_moneda_usd
group by me.id_agencia,mon.codigo_internacional
union all
select 	pva.id_agencia,
		'periodo_adeudado'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(pva.monto_mb * -v_id_moneda_mb ),0) as monto,
        COALESCE(sum(pva.monto_mb * -v_id_moneda_mb ),0) as monto_mb,
        'g'::varchar as aux
        from obingresos.tperiodo_venta_agencia pva
        inner join param.tmoneda mon on mon.id_moneda = v_id_moneda_mb
        where pva.estado_reg = 'activo' and pva.monto_mb < 0
        and pva.id_agencia = v_parametros.id_agencia  and pva.estado = 'abierto'
        group by pva.id_agencia,mon.codigo_internacional
union all
select 	pva.id_agencia,
		'periodo_adeudado'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(pva.monto_usd * -v_id_moneda_mb ),0) as monto,
        COALESCE(sum(param.f_convertir_moneda( v_id_moneda_usd , v_id_moneda_mb ,pva.monto_usd * -v_id_moneda_mb ,now()::date,'O',v_id_moneda_usd )),0) as monto_mb,
        'g'::varchar as aux
from obingresos.tperiodo_venta_agencia pva
inner join param.tmoneda mon on mon.id_moneda = v_id_moneda_usd
where pva.estado_reg = 'activo' and pva.monto_usd < 0
and pva.id_agencia = v_parametros.id_agencia  and pva.estado = 'abierto'
group by pva.id_agencia,mon.codigo_internacional
union all
select 	me.id_agencia,
		'otro_debito'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda, v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'h'::varchar as aux
from obingresos.tmovimiento_entidad me
inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
where me.estado_reg = 'activo' and me.id_periodo_venta is null
and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'debito' and
me.garantia = 'no' and me.ajuste = 'si' and me.cierre_periodo = 'no' and
me.id_moneda =  v_id_moneda_mb
group by me.id_agencia,mon.codigo_internacional
union all
select me.id_agencia,
		'otro_debito'::varchar as tipo,
        mon.codigo_internacional,
        COALESCE(sum(me.monto),0),
        COALESCE(sum(param.f_convertir_moneda(me.id_moneda, v_id_moneda_mb ,me.monto,me.fecha,'O',v_id_moneda_usd )),0) as monto_mb,
        'h'::varchar as aux
from obingresos.tmovimiento_entidad me
inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
where me.estado_reg = 'activo' and me.id_periodo_venta is null
and me.id_agencia = v_parametros.id_agencia  and me.tipo = 'debito' and
me.garantia = 'no' and me.ajuste = 'si' and me.cierre_periodo = 'no' and
me.id_moneda =  v_id_moneda_usd
group by me.id_agencia,mon.codigo_internacional
        )LOOP
         v_contador = v_contador + 1;
 IF ( not exists( select 1
        		from obingresos.tmovimiento_entidad me
        		where me.estado_reg = 'activo' and
                me.id_periodo_venta is null and
                me.id_agencia = v_parametros.id_agencia and
                me.tipo = 'credito' and
        		me.garantia = 'si'
                 ) and not EXISTS(select 1
        							from temp
                                    where tipo = 'boleta_garantia') ) THEN


        insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_parametros.id_agencia,
                            'boleta_garantia',
                             null,
                             0,
                             0,
                             'a'
                            );
 	END IF;

 IF ( not exists( select  1
                  from obingresos.tmovimiento_entidad me
                  where me.estado_reg = 'activo' and
                  me.id_periodo_venta is null and
                  me.id_agencia = v_parametros.id_agencia and
                  me.tipo = 'credito' and
                  me.garantia = 'no' and me.ajuste = 'no' and
                  me.cierre_periodo = 'si'
                 ) and not EXISTS(select 1
        							from temp
                                    where tipo = 'saldo_anterior') ) THEN
 insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_parametros.id_agencia,
                            'saldo_anterior',
                             null,
                             0,
                             0,
                             'b'
                            );
 	END IF;
  IF ( not exists( select 1
                  from obingresos.tmovimiento_entidad me
                  where me.estado_reg = 'activo' and
                  me.id_periodo_venta is null and
                  me.id_agencia = v_parametros.id_agencia and
                  me.tipo = 'credito' and
                  me.garantia = 'no' and
                  me.ajuste = 'no' and
                  me.cierre_periodo = 'no'
        group by me.id_agencia
                 ) and not EXISTS(select 1
        							from temp
                                    where tipo = 'deposito') ) THEN

        insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_parametros.id_agencia,
                            'deposito',
                             null,
                             0,
                             0,
                             'c'
                            );
 	END IF;
    IF ( not exists( select 1
                    from obingresos.tmovimiento_entidad me
                    where me.estado_reg = 'activo' and me.id_periodo_venta is null
                    and me.id_agencia = v_parametros.id_agencia and me.tipo = 'credito' and
                    me.garantia = 'no' and me.ajuste = 'si' and me.cierre_periodo = 'no' and
                    me.id_moneda = 1
                    group by me.id_agencia
                 ) and not EXISTS(select 1
        							from temp
                                    where tipo = 'otro_credito') ) THEN

        insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_parametros.id_agencia,
                            'otro_credito',
                             null,
                             0,
                             0,
                             'e'
                            );
 	END IF;
    IF ( not exists( select 1
        from obingresos.tmovimiento_entidad me
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia and me.tipo = 'debito' and
        me.ajuste = 'no' and me.pnr is not null and
        me.id_moneda = 1
        group by me.id_agencia
                 ) and not EXISTS(select 1
        							from temp
                                    where tipo = 'boleto') ) THEN

        insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_parametros.id_agencia,
                            'boleto',
                             null,
                             0,
                             0,
                             'f'
                            );
 	END IF;
    IF (not exists(select 	1
        from obingresos.tperiodo_venta_agencia pva
        where pva.estado_reg = 'activo' and pva.monto_mb < 0
        and pva.id_agencia = v_parametros.id_agencia and pva.estado = 'abierto'
        group by pva.id_agencia
                 ) and not EXISTS(select 1
        							from temp
                                    where tipo = 'periodo_adeudado') ) THEN

        insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_parametros.id_agencia,
                            'periodo_adeudado',
                             null,
                             0,
                             0,
                             'g'
                            );
 	END IF;

    IF(not exists (select 	1
        from obingresos.tmovimiento_entidad me
        where me.estado_reg = 'activo' and me.id_periodo_venta is null
        and me.id_agencia = v_parametros.id_agencia and me.tipo = 'debito' and
        me.garantia = 'no' and me.ajuste = 'si' and me.cierre_periodo = 'no'
        group by me.id_agencia
                 ) and not EXISTS(select 1
        							from temp
                                    where tipo = 'otro_debito')) THEN

        insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_parametros.id_agencia,
                            'otro_debito',
                             null,
                             0,
                             0,
                             'h'
                            );
 	END IF;


         insert into temp ( id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux)
                            values(
                            v_records.id_agencia,
                            v_records.tipo,
                            v_records.codigo_internacional,
                            v_records.monto,
                            v_records.monto_mb,
                            v_records.aux
                            );
         v_contador = v_contador ;
        END LOOP;

		v_consulta = 'select id_agencia,
                            tipo,
                            moneda,
                            monto,
                            monto_mb,
                            aux
        				from temp
                        order by aux ';
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
     #TRANSACCION:  'OBING_SALVIG_SEL'
     #DESCRIPCION:	Reporte saldo vigente
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/
    elsif(p_transaccion='OBING_SALVIG_SEL')then

        begin

        v_consulta ='with credito as (	select  mo.id_agencia,
		sum(case when mo.ajuste = ''no'' and  mo.garantia = ''no''   then
            	(case when mo.id_moneda = 1 then

                                mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = ''si'' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = ''si''  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)


                                end)
        else
            0
        end) as ajuste_credito
        from obingresos.tmovimiento_entidad mo
		where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
         mo.cierre_periodo = ''no'' and
      	 mo.estado_reg = ''activo'' and
         mo.tipo = ''credito''
		group by mo.id_agencia),
debitos as (select  mo.id_agencia,
sum(case when mo.ajuste = ''no'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_debito,
        sum(case when mo.ajuste = ''si'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_debito
from obingresos.tmovimiento_entidad mo
where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
      mo.tipo = ''debito'' and
      mo.cierre_periodo = ''no'' and
      mo.estado_reg = ''activo''
group by mo.id_agencia),
contrato as( select max(id_contrato) as ultimo_contrato,
                  id_agencia
                  from leg.tcontrato c
                  where id_agencia is not null and c.estado = ''finalizado''
                  group by id_agencia)
        select  ag.id_agencia,
        		ag.nombre,
                ag.codigo_int,
       			ag.tipo_agencia,
                array_to_string(con.formas_pago, '','')::varchar as formas_pago,
    			l.codigo,
                COALESCE( cr.monto_credito,0),
                cr.garantia as codigo_ciudad,
                COALESCE( de.monto_debito,0),
               (COALESCE( cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as monto_ajustes,
               (COALESCE( cr.garantia,0) + COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0) )+( COALESCE(cr.ajuste_credito,0)- COALESCE(de.ajuste_debito,0)) as saldo_con_boleto,
               (COALESCE( cr.monto_credito,0) - COALESCE(de.monto_debito,0)) + (COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as saldo_sin_boleto
        from obingresos.tagencia ag
        inner join credito cr on cr.id_agencia = ag.id_agencia
        left join  debitos de on de.id_agencia = ag.id_agencia
        inner join contrato c on c.id_agencia = ag.id_agencia
		inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
		inner join param.tlugar l on l.id_lugar = ag.id_lugar
        where  '||v_parametros.filtro||'
        order by nombre';
        return v_consulta;
		end;
    /*********************************
     #TRANSACCION:  'OBING_SALVIG_CONT'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_SALVIG_CONT')then

      begin
      	v_consulta = 'with credito as (	select  mo.id_agencia,
		sum(case when mo.ajuste = ''no'' and  mo.garantia = ''no''   then
            	(case when mo.id_moneda = 1 then
                            	mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)


                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = ''si'' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = ''si''  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_credito
        from obingresos.tmovimiento_entidad mo
		where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
         mo.cierre_periodo = ''no'' and
      	 mo.estado_reg = ''activo'' and
         mo.tipo = ''credito''
		group by mo.id_agencia),

debitos as (select  mo.id_agencia,
sum(case when mo.ajuste = ''no'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_debito,
        sum(case when mo.ajuste = ''si'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)


                                end)
        else
            0
        end) as ajuste_debito
from obingresos.tmovimiento_entidad mo
where mo.fecha >= ''8/1/2017'' and mo.fecha <='''||v_parametros.fecha_fin||''' and
      mo.tipo = ''debito'' and
      mo.cierre_periodo = ''no'' and
      mo.estado_reg = ''activo''
group by mo.id_agencia),
contrato as( select max(id_contrato) as ultimo_contrato,
                  id_agencia
                  from leg.tcontrato c
                  where id_agencia is not null and c.estado = ''finalizado''
                  group by id_agencia),
 detalle as  (select  ag.id_agencia,
        		ag.nombre,
                ag.codigo_int,
       			ag.tipo_agencia,
                array_to_string(con.formas_pago, '','')::varchar as formas_pago,
    			l.codigo,
                COALESCE( cr.monto_credito,0)as monto_credito,
                cr.garantia as codigo_ciudad,
                COALESCE( de.monto_debito,0) as monto_debito,
               (COALESCE( cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as monto_ajustes,
               (COALESCE( cr.garantia,0) + COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0) )+( COALESCE(cr.ajuste_credito,0)- COALESCE(de.ajuste_debito,0)) as saldo_con_boleto,
               (COALESCE( cr.monto_credito,0) - COALESCE(de.monto_debito,0)) + (COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as saldo_sin_boleto
        from obingresos.tagencia ag
        inner join credito cr on cr.id_agencia = ag.id_agencia
        left join  debitos de on de.id_agencia = ag.id_agencia
        inner join contrato c on c.id_agencia = ag.id_agencia
		inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
		inner join param.tlugar l on l.id_lugar = ag.id_lugar
        where  '||v_parametros.filtro||'
        order by nombre)
select count(id_agencia),
 sum(monto_credito) as total_creditos,
 sum(monto_debito)as total_debitos,
 sum(monto_ajustes) as total_ajuste,
 sum(saldo_con_boleto) as total_saldo_con_boleto,
 sum(saldo_sin_boleto) as total_saldo_sin_boleto
from detalle';
        --Devuelve la respuesta
        return v_consulta;

      end;
/*********************************
     #TRANSACCION:  'OBING_CUT_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_CUT_SEL')then

    begin
     CREATE TEMPORARY TABLE temp ( tipo_credito varchar,
          								tipo_debito varchar,
                                        total numeric,
          								id_agencia int4,
                                        monto_credito numeric,
                                        monto_debito numeric,
                                        garantia NUMERIC,
                                        autorizacion__nro_deposito varchar,
                                        fecha date,
                                        nro_deposito_boa varchar,
                                        ajuste_credito NUMERIC,
                                        ajuste_debito numeric,
                                        id_periodo_venta int4,
                                        fecha_ini date,
                                        fecha_fin date,
                                        periodo text,
                                        tipo_agencia varchar

                                       )ON COMMIT DROP;
/*Aumentando la condicion*/
        select
        pe.id_periodo_venta
        into v_id_periodo_venta_ini
        from obingresos.tperiodo_venta pe
        where  v_parametros.fecha_ini::date between pe.fecha_ini and pe.fecha_fin;

        if (v_id_periodo_venta_ini is null) then
            select pe.id_periodo_venta
            into v_id_periodo_venta_ini
            from obingresos.tperiodo_venta pe
            where pe.fecha_ini >= v_parametros.fecha_ini::date
            order by pe.id_periodo_venta ASC
            limit 1;
        end if;

        select
        pe.id_periodo_venta
        into v_id_periodo_venta_fin
        from obingresos.tperiodo_venta pe
        where v_parametros.fecha_fin::date between pe.fecha_ini and pe.fecha_fin;

        if (v_id_periodo_venta_fin is null) then
          select
          max(pe.id_periodo_venta)
          into v_id_periodo_venta_fin
          from obingresos.tperiodo_venta pe;
        end if;


    for v_fechas_periodos in   ( select
                                pe.fecha_ini,
                                pe.fecha_fin,
                                COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo
                                from obingresos.tperiodo_venta pe
                              	where pe.id_periodo_venta between v_id_periodo_venta_ini and v_id_periodo_venta_fin)

   /**********************************************/



    /*for v_debitos_fecha in (select
                  'debitos':: varchar as tipo,
                  mo.id_agencia,
                  pe.fecha_ini,
                  pe.fecha_fin

      from obingresos.tmovimiento_entidad mo
      LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
      where mo.fecha >= v_parametros.fecha_ini::date and mo.fecha <= v_parametros.fecha_fin::date and

            mo.id_agencia = v_parametros.id_agencia


      group by mo.id_agencia,
      pe.fecha_ini,
      pe.fecha_fin,
      pe.mes



      )*/


      loop

   for v_creditos in (  (with credito as (select  mo.id_agencia,
        pe.fecha_ini,
        pe.fecha_fin,
		sum(case when mo.ajuste = 'no' and  mo.garantia = 'no'   then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = 'si' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = 'si'  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                end)
        else
            0
        end) as ajuste_credito,
        mo.autorizacion__nro_deposito,
        mo.fecha,
        dep.nro_deposito_boa,

        COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo

        from obingresos.tmovimiento_entidad mo
        left join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
        left JOIN obingresos.tdeposito dep ON dep.id_agencia = mo.id_agencia and dep.nro_deposito::text = mo.autorizacion__nro_deposito::text AND dep.estado::text = 'validado'::text

		where mo.fecha >= '1/1/2017' and
         mo.cierre_periodo = 'no' and
      	 mo.estado_reg = 'activo' and
         mo.tipo = 'credito'
         and  mo.id_agencia = v_parametros.id_agencia
		group by mo.id_agencia, mo.autorizacion__nro_deposito, mo.fecha, dep.nro_deposito_boa, pe.fecha_ini,
        pe.fecha_fin, pe.mes)

select 			'creditos' :: varchar as tipo,
				ag.id_agencia,

                COALESCE ( cr.monto_credito, 0 )as monto_credito,
                cr.garantia as boleta_garantia,
                cr.autorizacion__nro_deposito,
                cr.fecha,
                cr.nro_deposito_boa,
                cr.ajuste_credito as ajuste,
                cr.fecha_ini,
                cr.fecha_fin,
                cr.periodo

                from obingresos.tagencia ag
                left join credito cr on cr.id_agencia = ag.id_agencia
                where ag.id_agencia = v_parametros.id_agencia
                and cr.fecha >= v_parametros.fecha_ini::date
                and cr.fecha <= v_parametros.fecha_fin::date

                )




                 )


        LOOP

		--if (v_creditos.fecha between v_debitos_fecha.fecha_ini and v_debitos_fecha.fecha_fin)  then
        if (v_creditos.fecha between v_fechas_periodos.fecha_ini and v_fechas_periodos.fecha_fin)  then
        insert into temp (
                                        tipo_credito,
                                        fecha_ini,
                                        fecha_fin,
                                        fecha,
                                        monto_credito,
                                        ajuste_credito,
                                        autorizacion__nro_deposito,
                                        nro_deposito_boa,
                                        periodo/*Aumentando*/

        				  )
                                  values (
                                  		v_creditos.tipo,
                                        v_fechas_periodos.fecha_ini,--v_debitos_fecha.fecha_ini,
                                        v_fechas_periodos.fecha_fin,--v_debitos_fecha.fecha_fin,
                                        v_creditos.fecha,
                                        v_creditos.monto_credito,
                                        v_creditos.ajuste,
                                        v_creditos.autorizacion__nro_deposito,
                                        v_creditos.nro_deposito_boa,
                                        v_fechas_periodos.periodo/*Aumentando*/
                                  );
                          end if;






  end loop;
end loop;




 for v_debitos in (select
                  'debitos':: varchar as tipo,
                  mo.id_agencia,
                  pe.fecha_ini,
                  pe.fecha_fin,

                  sum(case when mo.ajuste = 'no' then
                      (case when mo.id_moneda = 1 then
                                      mo.monto
                                  else
                                      param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                      end)
              else
                  0
              end) as monto_debito,
              sum(case when mo.ajuste = 'si' then
                  (case when mo.id_moneda = 1 then
                                      mo.monto
                                  else
                                      param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                      end)
              else
                  0
              end) as ajuste_debito,

                  COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo

      from obingresos.tmovimiento_entidad mo
      LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
      where mo.fecha >= v_parametros.fecha_ini::date and mo.fecha <= v_parametros.fecha_fin::date and
            mo.tipo = 'debito' and
            mo.id_agencia = v_parametros.id_agencia and
            mo.cierre_periodo = 'no' and
            mo.estado_reg = 'activo'

      group by mo.id_agencia,
      pe.fecha_ini,
      pe.fecha_fin,
      pe.mes



      )


      loop


     insert into temp (
                                        tipo_debito,
                                        fecha_ini,
                                        fecha_fin,
                                        periodo,
                                        monto_debito,
                                        ajuste_debito



        				  )
                                  values (
                                  		v_debitos.tipo,
                                        v_debitos.fecha_ini,
                                        v_debitos.fecha_fin,
                                        v_debitos.periodo,
                                        v_debitos.monto_debito,
                                        v_debitos.ajuste_debito
                                  );

end loop;

		/*******Obtenemos la fecha maxima del periodo*******/
		select
        max(pe.fecha_fin)
        into v_fecha_maxima
        from obingresos.tmovimiento_entidad mo
        LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
        where mo.fecha >= v_parametros.fecha_ini::date and mo.fecha <= v_parametros.fecha_fin::date and
              mo.id_agencia = v_parametros.id_agencia;
        /***********************************************************/


		/*Obtenemos los creditos qeu no estan dentro de ese periodo*/
		for v_creditos_fuera in (  (with credito as (select  mo.id_agencia,
        pe.fecha_ini,
        pe.fecha_fin,
		sum(case when mo.ajuste = 'no' and  mo.garantia = 'no'   then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = 'si' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = 'si'  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,'O',2)

                                end)
        else
            0
        end) as ajuste_credito,
        mo.autorizacion__nro_deposito,
        mo.fecha,
        dep.nro_deposito_boa,

        COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo

        from obingresos.tmovimiento_entidad mo
        left join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
        left JOIN obingresos.tdeposito dep ON dep.id_agencia = mo.id_agencia and dep.nro_deposito::text = mo.autorizacion__nro_deposito::text AND dep.estado::text = 'validado'::text

		where mo.fecha >= '1/1/2017' and
         mo.cierre_periodo = 'no' and
      	 mo.estado_reg = 'activo' and
         mo.tipo = 'credito'
         and  mo.id_agencia = v_parametros.id_agencia
		group by mo.id_agencia, mo.autorizacion__nro_deposito, mo.fecha, dep.nro_deposito_boa, pe.fecha_ini,
        pe.fecha_fin, pe.mes)

select 			'creditos' :: varchar as tipo,
				ag.id_agencia,

                COALESCE ( cr.monto_credito, 0 )as monto_credito,
                cr.garantia as boleta_garantia,
                cr.autorizacion__nro_deposito,
                cr.fecha,
                cr.nro_deposito_boa,
                cr.ajuste_credito as ajuste,
                cr.fecha_ini,
                cr.fecha_fin,
                cr.periodo

                from obingresos.tagencia ag
                left join credito cr on cr.id_agencia = ag.id_agencia
                where ag.id_agencia = v_parametros.id_agencia
                and cr.fecha > v_fecha_maxima
                and cr.fecha <= v_parametros.fecha_fin::date


                )



                 )


        LOOP

		/*Verificamos si ya se encuentra en la tabla temporal*/

        if (NOT exists(
        		select 1
                from temp
                where tipo_credito = v_creditos_fuera.tipo
                and fecha = v_creditos_fuera.fecha
                and monto_credito = v_creditos_fuera.monto_credito
                and autorizacion__nro_deposito = v_creditos_fuera.autorizacion__nro_deposito)
        	) then
        /*****************************************************/

        insert into temp (
                                        tipo_credito,
                                        fecha,
                                        monto_credito,
                                        garantia,
                                        ajuste_credito,
                                        autorizacion__nro_deposito,
                                        nro_deposito_boa


        				  )
                                  values (
                                  		v_creditos_fuera.tipo,
                                        v_creditos_fuera.fecha,
                                        v_creditos_fuera.monto_credito,
                                        v_creditos_fuera.boleta_garantia,
                                        v_creditos_fuera.ajuste,
                                        v_creditos_fuera.autorizacion__nro_deposito,
                                        v_creditos_fuera.nro_deposito_boa

                                  );







	end if;

  end loop;

if (v_parametros.formas_pago = 'prepago' ) then

		v_consulta = 'select
        				 				tipo_credito ,
                                        tipo_debito,
                                        total,
          								id_agencia ,
                                        COALESCE (monto_credito,0) as monto_credito ,
                                        COALESCE (monto_debito,0) as monto_debito ,
                                        COALESCE (garantia , 0) as garantia,
                                        autorizacion__nro_deposito ,
                                        fecha ,
                                        nro_deposito_boa ,
                                        COALESCE (ajuste_credito,0) as ajuste_credito ,
                                        COALESCE (ajuste_debito,0) as ajuste_debito,
                                        id_periodo_venta ,
                                        fecha_ini ,
                                        fecha_fin ,
                                        periodo,
                                        tipo_agencia

                                        from temp order by fecha_ini,fecha ';


			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;
elsif  (v_parametros.formas_pago = 'postpago' ) then

v_consulta = 'select
        				 				tipo_credito ,
                                        tipo_debito,
                                        total,
          								id_agencia ,
                                        COALESCE (monto_credito,0) as monto_credito ,
                                        COALESCE (monto_debito,0) as monto_debito ,
                                        COALESCE (garantia , 0) as garantia,
                                        autorizacion__nro_deposito ,
                                        fecha ,
                                        nro_deposito_boa ,
                                        COALESCE (ajuste_credito,0) as ajuste_credito ,
                                        COALESCE (ajuste_debito,0) as ajuste_debito,
                                        id_periodo_venta ,
                                        fecha_ini ,
                                        fecha_fin ,
                                        periodo,
                                        tipo_agencia

                                        from temp order by fecha ';


			raise notice '%',v_consulta;
			--Devuelve la respuesta
		return v_consulta;
end if;

		end;

      /*********************************
     #TRANSACCION:  'OBING_PERANT_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		30-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_PERANT_SEL')then

    begin
      		v_consulta = '
              with credito as (	select  mo.id_agencia,
                      sum(case when mo.ajuste = ''no'' and  mo.garantia = ''no''   then
                              (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as monto_credito,

                       sum(case when  mo.garantia = ''si'' and mo.id_periodo_venta is null then
                          (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as garantia,

                      sum(case when mo.ajuste = ''si''  then
                              (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as ajuste_credito
                      from obingresos.tmovimiento_entidad mo
                      where mo.fecha >= ''8/1/2017'' and mo.fecha < '''||v_parametros.fecha_ini||''' and
                       mo.cierre_periodo = ''no'' and
                       mo.estado_reg = ''activo'' and
                       mo.tipo = ''credito''
                      group by mo.id_agencia),
              debitos as (select  mo.id_agencia,
              sum(case when mo.ajuste = ''no'' then
                          (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as monto_debito,
                      sum(case when mo.ajuste = ''si'' then
                          (case when mo.id_moneda = 1 then
                                              mo.monto
                                          else
                                              param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                              end)
                      else
                          0
                      end) as ajuste_debito
              from obingresos.tmovimiento_entidad mo
              where mo.fecha >= ''8/1/2017'' and mo.fecha < '''||v_parametros.fecha_ini||''' and
                    mo.tipo = ''debito'' and
                    mo.cierre_periodo = ''no'' and
                    mo.estado_reg = ''activo''
              group by mo.id_agencia)

                      select  ag.id_agencia,
                              ag.nombre,
                              --cr.id_periodo_venta,
                              ag.codigo_int,
                              ag.tipo_agencia,

                              COALESCE ( cr.monto_credito, 0 )as monto_creditos,
                              cr.garantia as codigo_ciudad,
                              COALESCE( de.monto_debito, 0 ) as monto_debitos,
                              COALESCE(cr.ajuste_credito,0) as ajuste_credito,
                              COALESCE(de.ajuste_debito,0) as ajuste_debito,
                             ( COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as monto_ajustes,
                             --( COALESCE(cr.garantia,0)+ COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0) )+(COALESCE(cr.ajuste_credito,0)- COALESCE(de.ajuste_debito,0)) as saldo_con_boleto,
                             ( COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0)) + (COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as saldo_sin_boleto_ant
                      from obingresos.tagencia ag
                      inner join credito cr on cr.id_agencia = ag.id_agencia
                      left join  debitos de on de.id_agencia = ag.id_agencia
                      where ag.id_agencia = '||v_parametros.id_agencia||'
                      order by nombre';


       --Devuelve la respuesta
    return v_consulta;
	end;

     /*********************************
     #TRANSACCION:  'OBING_MOVANT_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		30-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_MOVANT_SEL')then

    begin
      		v_consulta = 'with aux as (select mo.tipo,
                                       mo.id_agencia,
                                       COALESCE(TO_CHAR(mo.fecha_ini,''DD'')||'' al ''|| TO_CHAR(mo.fecha_fin,''DD'')||'' ''||mo.mes||'' ''||EXTRACT(YEAR FROM mo.fecha_ini),''actual'')::text as periodo,
                                       mo.id_periodo_venta,
                                       mo.monto_total
                                       from obingresos.vdebito_ag mo
                                       inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                                       where mo.id_agencia = '||v_parametros.id_agencia||'
                                       order by id_periodo_venta)

                                select mo.tipo as credito,
                                       COALESCE(au.tipo,''debito'') as debito,
                                       ag.nombre,
                                       ag.codigo_int,
                                       mo.id_agencia,
                                       mo.id_periodo_venta,
                                       COALESCE(TO_CHAR(mo.fecha_ini,''DD'')||'' al ''|| TO_CHAR(mo.fecha_fin,''DD'')||'' ''||mo.mes||'' ''||EXTRACT(YEAR FROM mo.fecha_ini),''Periodo Vigente'')::text as periodo,
                                       mo.monto_total as credito_anterior,
                                       COALESCE(au.monto_total,0) as  debito_anterior,

                                        mo.monto_total - COALESCE(au.monto_total,0) as saldo_sin_boleta,
                                        mcb.monto_total - COALESCE(au.monto_total,0) as saldo_boleta
                                       from obingresos.vcredito_ag mo
                                       inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                                       inner join obingresos.vcredito_ag_boleta mcb on mcb.id_agencia = mo.id_agencia and mo.id_periodo_venta = mcb.id_periodo_venta
                                       left join aux au on  COALESCE (au.id_periodo_venta,0) = COALESCE(mo.id_periodo_venta,0)
                                       where mo.id_agencia = '||v_parametros.id_agencia||'
                                       and mo.id_periodo_venta = (select max(bp.id_periodo_venta)
                                                                              from obingresos.vcredito_ag bp
                                                                              where bp.id_agencia = '||v_parametros.id_agencia||' and bp.fecha_fin <= '''||v_parametros.fecha_ini||''')

                                order by id_periodo_venta';


       --Devuelve la respuesta
    return v_consulta;
	end;



     /*********************************
     #TRANSACCION:  'OBING_PER_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		MMV
     #FECHA:		18-11-2016
    ***********************************/

    elsif(p_transaccion='OBING_PER_SEL')then

      begin
      		v_consulta = 'select  pv.id_periodo_venta,
                                  pv.id_gestion,
                                  TO_CHAR(pv.fecha_ini,''DD'')||'' al ''|| TO_CHAR(pv.fecha_fin,''DD'')||'' ''||pv.mes||'' ''||EXTRACT(YEAR FROM pv.fecha_ini)::text as periodo,
                                  pv.mes,
                                  pv.fecha_ini,
                                  pv.fecha_fin
                          		  from obingresos.tperiodo_venta pv
                                  where ';
    	--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
	end;
	    /*********************************
     #TRANSACCION:  'OBING_RERE_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_RERE_SEL')then

      begin



      		v_consulta = 'with credito as (	select  mo.id_agencia,
		sum(case when mo.ajuste = ''no'' and  mo.garantia = ''no''   then
            	(case when mo.id_moneda = 1 then

                                mo.monto


                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = ''si'' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then

                                mo.monto

                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = ''si''  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_credito
        from obingresos.tmovimiento_entidad mo
		where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
         mo.cierre_periodo = ''no'' and
      	 mo.estado_reg = ''activo'' and
         mo.tipo = ''credito''
		group by mo.id_agencia),
debitos as (select  mo.id_agencia,
sum(case when mo.ajuste = ''no'' then
          	(case when mo.id_moneda = 1 then
            					mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_debito,
        sum(case when mo.ajuste = ''si'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_debito
from obingresos.tmovimiento_entidad mo
where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
      mo.tipo = ''debito'' and
      mo.cierre_periodo = ''no'' and
      mo.estado_reg = ''activo''
group by mo.id_agencia),
contrato as( select max(id_contrato) as ultimo_contrato,
                  id_agencia
                  from leg.tcontrato c
                  where id_agencia is not null and c.estado = ''finalizado''
                  group by id_agencia)
        select  ag.id_agencia,
        		ag.nombre,
                ag.codigo_int,
       			ag.tipo_agencia,
                array_to_string(con.formas_pago, '','')::varchar as formas_pago,
    			l.codigo,
                COALESCE ( cr.monto_credito, 0 )as monto_creditos,
                cr.garantia as codigo_ciudad,
                COALESCE( de.monto_debito, 0 )as monto_debitos,
               ( COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as monto_ajustes,
               ( COALESCE(cr.garantia,0)+ COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0) )+(COALESCE(cr.ajuste_credito,0)- COALESCE(de.ajuste_debito,0)) as saldo_con_boleto,
               ( COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0)) + (COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as saldo_sin_boleto
        from obingresos.tagencia ag
        inner join credito cr on cr.id_agencia = ag.id_agencia
        left join  debitos de on de.id_agencia = ag.id_agencia
        inner join contrato c on c.id_agencia = ag.id_agencia
		inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
		inner join param.tlugar l on l.id_lugar = ag.id_lugar
        where '||v_parametros.filtro||'
        order by nombre';

       --Devuelve la respuesta
       raise notice '%', v_consulta;
    return v_consulta;
	end;

	else

		raise exception 'Transaccion inexistente';

	end if;

EXCEPTION

	WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION obingresos.ft_reporte_cuenta_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
