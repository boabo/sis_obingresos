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

BEGIN

	v_nombre_funcion = 'obingresos.ft_reporte_cuenta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ENT_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani
 	#FECHA:		11-06-2018 15:14:58
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
                                      contador integer )ON COMMIT DROP;

     if  pxp.f_existe_parametro(p_tabla,'id_periodo_venta') then


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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'debito'  and mo.estado_reg = 'activo' and mo.id_periodo_venta = v_parametros.id_periodo_venta
                     union all
                        select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                (case
                                when mo.cierre_periodo = 'no' then
                                mo.autorizacion__nro_deposito
                                else
                                'Saldo Cierre Peridos Anterior'
                                end::text) as autorizacion__nro_deposito,
                                ' '::varchar as billete,
                                0::numeric as comision,
                                mo.monto_total as importe,
                                mo.neto,
                                mo.monto_total  as saldo
                        from obingresos.tmovimiento_entidad mo
                        inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and  mo.id_periodo_venta = v_parametros.id_periodo_venta
                   		order by fecha ,billete asc)LOOP

                     v_contador = v_contador + 1;

                     IF EXISTS (select 1
                     				from temp) THEN

                     select COALESCE( saldo, 0 )
                     into
                     v_saldo
                     from temp
                     where contador = (select max(contador)
                     				  from temp);
                     ELSE
                     v_saldo = 0;

        			 END IF;

                    insert into	temp (  id_agencia,
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
                                        contador)
                                        values(
                                        v_record.id_agencia,
                                        v_record.nombre,
                                        v_record.tipo,
                                        v_record.pnr,
                                        v_record.fecha,
                                        v_record.autorizacion__nro_deposito,
                                        v_record.billete,
                                        v_record.comision,
                                        v_record.importe,
                                        v_record.neto,
                                         case
                                        	when v_saldo = 0 then
                                          		case
                                         			when  v_record.tipo = 'debito'  then
                                                 		v_saldo - v_record.saldo
                                           			when v_record.tipo = 'credito' and v_record.autorizacion__nro_deposito !='Saldo Cierre Peridos Anterior'  then
                                                        v_saldo + v_record.saldo
                                           			end
                                        else
                                        case
                                        	when v_record.tipo = 'debito' then
                                   					v_saldo -  v_record.saldo
    											when v_record.tipo = 'credito' and v_record.autorizacion__nro_deposito !='Saldo Cierre Peridos Anterior'  then
                                          			 v_record.saldo - (- v_saldo)
                                        		end
                                        end,
                                        v_contador);
    		v_contador = v_contador ;
    	END LOOP;
       else

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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'debito'
                        and mo.estado_reg = 'activo' and mo.ajuste = 'no'

                        union all

                        select mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
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

                        union all

                        select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                mo.autorizacion__nro_deposito,
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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and mo.cierre_periodo = 'no' and mo.garantia = 'no'
                        and mo.ajuste = 'no'
                     union all
                     select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                mo.autorizacion__nro_deposito,
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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and mo.cierre_periodo = 'no' and mo.garantia = 'no'
                        and mo.ajuste = 'si'
                        union all
                        select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                'Boleta Garantia'::varchar as autorizacion__nro_deposito,
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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and mo.cierre_periodo = 'no' and mo.garantia = 'si'
                        and mo.id_periodo_venta is null
                        and mo.ajuste = 'no'
                        order by fecha ,billete asc)LOOP

                     v_contador = v_contador + 1;

                     IF EXISTS (select 1
                     				from temp) THEN

                     select COALESCE( saldo, 0 )
                     into
                     v_saldo
                     from temp
                     where contador = (select max(contador)
                     				  from temp);
                     ELSE
                     v_saldo = 0;

        			 END IF;

                    insert into	temp (  id_agencia,
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
                                        contador)
                                        values(
                                        v_record.id_agencia,
                                        v_record.nombre,
                                        v_record.tipo,
                                        v_record.pnr,
                                        v_record.fecha,
                                        v_record.autorizacion__nro_deposito,
                                        v_record.billete,
                                        v_record.comision,
                                        v_record.importe,
                                        v_record.neto,
                                        case
                                        	when v_saldo = 0 then
                                          		case
                                         			when  v_record.tipo = 'debito' then
                                                 		v_saldo - v_record.saldo
                                           			when v_record.tipo = 'credito' then
                                                        v_saldo + v_record.saldo
                                           			end
                                        else
                                        case
                                        	when v_record.tipo = 'debito' then
                                   					v_saldo -  v_record.saldo
    											when v_record.tipo = 'credito' then
                                          			 v_record.saldo +  v_saldo
                                        		end
                                        end,
                                        v_contador);
    		v_contador = v_contador ;
    	END LOOP;
        END IF;
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
                                saldo
                                from temp
                                where fecha between '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin||'''';

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
                                      contador integer )ON COMMIT DROP;

     if  pxp.f_existe_parametro(p_tabla,'id_periodo_venta') then


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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'debito'  and mo.estado_reg = 'activo' and mo.id_periodo_venta = v_parametros.id_periodo_venta
                     union all
                        select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                (case
                                when mo.cierre_periodo = 'no' then
                                mo.autorizacion__nro_deposito
                                else
                                'Saldo Cierre Peridos Anterior'
                                end::text) as autorizacion__nro_deposito,
                                ' '::varchar as billete,
                                0::numeric as comision,
                                mo.monto_total as importe,
                                mo.neto,
                                mo.monto_total  as saldo
                        from obingresos.tmovimiento_entidad mo
                        inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and  mo.id_periodo_venta = v_parametros.id_periodo_venta
                   		order by fecha ,billete asc)LOOP

                     v_contador = v_contador + 1;

                     IF EXISTS (select 1
                     				from temp) THEN

                     select COALESCE( saldo, 0 )
                     into
                     v_saldo
                     from temp
                     where contador = (select max(contador)
                     				  from temp);
                     ELSE
                     v_saldo = 0;

        			 END IF;

                    insert into	temp (  id_agencia,
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
                                        contador)
                                        values(
                                        v_record.id_agencia,
                                        v_record.nombre,
                                        v_record.tipo,
                                        v_record.pnr,
                                        v_record.fecha,
                                        v_record.autorizacion__nro_deposito,
                                        v_record.billete,
                                        v_record.comision,
                                        v_record.importe,
                                        v_record.neto,
                                         case
                                        	when v_saldo = 0 then
                                          		case
                                         			when  v_record.tipo = 'debito'  then
                                                 		v_saldo - v_record.saldo
                                           			when v_record.tipo = 'credito' and v_record.autorizacion__nro_deposito !='Saldo Cierre Peridos Anterior'  then
                                                        v_saldo + v_record.saldo
                                           			end
                                        else
                                        case
                                        	when v_record.tipo = 'debito' then
                                   					v_saldo -  v_record.saldo
    											when v_record.tipo = 'credito' and v_record.autorizacion__nro_deposito !='Saldo Cierre Peridos Anterior'  then
                                          			 v_record.saldo - (- v_saldo)
                                        		end
                                        end,
                                        v_contador);
    		v_contador = v_contador ;
    	END LOOP;
       else

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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'debito'
                        and mo.estado_reg = 'activo' and mo.ajuste = 'no'

                        union all

                        select mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
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

                        union all

                        select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                mo.autorizacion__nro_deposito,
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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and mo.cierre_periodo = 'no' and mo.garantia = 'no'
                        and mo.ajuste = 'no'
                     union all
                     select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                mo.autorizacion__nro_deposito,
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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and mo.cierre_periodo = 'no' and mo.garantia = 'no'
                        and mo.ajuste = 'si'
                        union all
                        select 	mo.id_agencia,
                                ag.nombre,
                                mo.tipo,
                                ''::varchar as pnr,
                                mo.fecha ,
                                'Boleta Garantia'::varchar as autorizacion__nro_deposito,
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
                        where mo.id_agencia =  v_parametros.id_agencia and mo.tipo = 'credito'
                        and mo.estado_reg = 'activo' and mo.cierre_periodo = 'no' and mo.garantia = 'si'
                        and mo.id_periodo_venta is null
                        and mo.ajuste = 'no'
                        order by fecha ,billete asc)LOOP

                     v_contador = v_contador + 1;

                     IF EXISTS (select 1
                     				from temp) THEN

                     select COALESCE( saldo, 0 )
                     into
                     v_saldo
                     from temp
                     where contador = (select max(contador)
                     				  from temp);
                     ELSE
                     v_saldo = 0;

        			 END IF;

                    insert into	temp (  id_agencia,
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
                                        contador)
                                        values(
                                        v_record.id_agencia,
                                        v_record.nombre,
                                        v_record.tipo,
                                        v_record.pnr,
                                        v_record.fecha,
                                        v_record.autorizacion__nro_deposito,
                                        v_record.billete,
                                        v_record.comision,
                                        v_record.importe,
                                        v_record.neto,
                                        case
                                        	when v_saldo = 0 then
                                          		case
                                         			when  v_record.tipo = 'debito' then
                                                 		v_saldo - v_record.saldo
                                           			when v_record.tipo = 'credito' then
                                                        v_saldo + v_record.saldo
                                           			end
                                        else
                                        case
                                        	when v_record.tipo = 'debito' then
                                   					v_saldo -  v_record.saldo
    											when v_record.tipo = 'credito' then
                                          			 v_record.saldo +  v_saldo
                                        		end
                                        end,
                                        v_contador);
    		v_contador = v_contador ;
    	END LOOP;
        END IF;
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
                                saldo
                                from temp
                                where fecha < '''||v_parametros.fecha_ini||'''';

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
		where mo.fecha >= '''||v_parametros.fecha_ini||''' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
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
where mo.fecha >= '''||v_parametros.fecha_ini||''' and mo.fecha <='''||v_parametros.fecha_fin||''' and
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
                cr.monto_credito ,
                cr.garantia as codigo_ciudad,
                de.monto_debito,
               (cr.ajuste_credito - de.ajuste_debito) as monto_ajustes,
               ( cr.garantia+ cr.monto_credito - de.monto_debito )+(cr.ajuste_credito- de.ajuste_debito) as saldo_con_boleto,
               ( cr.monto_credito - de.monto_debito) + (cr.ajuste_credito - de.ajuste_debito) as saldo_sin_boleto
        from obingresos.tagencia ag
        inner join credito cr on cr.id_agencia = ag.id_agencia
        left join  debitos de on de.id_agencia = ag.id_agencia
        inner join contrato c on c.id_agencia = ag.id_agencia
		inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
		inner join param.tlugar l on l.id_lugar = ag.id_lugar
        where  '||v_parametros.filtro||')
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

      		v_consulta = '(select  ''boletos''::varchar as tipo,
                                  bp.id_agencia,
                                  bp.id_periodo_venta,
                                  ag.nombre,
                                  COALESCE(TO_CHAR(bp.fecha_ini,''DD'')||'' al ''|| TO_CHAR(bp.fecha_fin,''DD'')||'' ''||bp.mes||'' ''||EXTRACT(YEAR FROM bp.fecha_ini),''Periodo Vigente'')::text as periodo,
                                  bp.monto_debito,
                                   null::numeric as monto_deposito,
                                  (select pe.fecha_pago
                                   from obingresos.tperiodo_venta pe
                                   where pe.id_periodo_venta = bp.id_periodo_venta) as fecha_pago,
                                   null::date as fecha,
                                   null::varchar as nro_deposito,
                                   null::numeric as garante,
                                   (cr.monto_total - bp.monto_debito) as monto_sin_boleta,
                                   null::varchar nro_deposito_boa
                          from obingresos.vboletos_a_pagar bp
                          inner join obingresos.tagencia ag on ag.id_agencia = bp.id_agencia
                          left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                          where bp.id_agencia = '||v_parametros.id_agencia||' and bp.fecha_ini >= '''||v_parametros.fecha_ini||'''
                          and bp.fecha_ini <= '''||v_parametros.fecha_fin||'''
                          and EXTRACT(YEAR FROM bp.fecha_ini) = '''||v_parametros.año_ini||'''
     Union
/*RECUPERAMOS EL ULTIMO PERIODO AL QUE PERTENECE*/
	select  ''boletos''::varchar as tipo,
                                  bp.id_agencia,
                                  bp.id_periodo_venta,
                                  ag.nombre,
                                  COALESCE(TO_CHAR(bp.fecha_ini,''DD'')||'' al ''|| TO_CHAR(bp.fecha_fin,''DD'')||'' ''||bp.mes||'' ''||EXTRACT(YEAR FROM bp.fecha_ini),''Periodo Vigente'')::text as periodo,
                                  bp.monto_debito,
                                   null::numeric as monto_deposito,
                                  (select pe.fecha_pago
                                   from obingresos.tperiodo_venta pe
                                   where pe.id_periodo_venta = bp.id_periodo_venta) as fecha_pago,
                                   null::date as fecha,
                                   null::varchar as nro_deposito,
                                   null::numeric as garante,
                                   (cr.monto_total - bp.monto_debito) as monto_sin_boleta,
                                   null::varchar nro_deposito_boa
                          from obingresos.vboletos_a_pagar bp
                          inner join obingresos.tagencia ag on ag.id_agencia = bp.id_agencia
                          left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                          where bp.id_agencia = '||v_parametros.id_agencia||' and bp.id_periodo_venta = (select bp.id_periodo_venta
                                                                              from obingresos.vboletos_a_pagar bp
                                                                              where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_fin >= '''||v_parametros.fecha_fin||'''
                                                                              FETCH FIRST 1 ROW ONLY)
                          and EXTRACT(YEAR FROM bp.fecha_ini) = '''||v_parametros.año_ini||'''

   /*Inicio del Periodo*/
Union
	select  ''boletos''::varchar as tipo,
                                  bp.id_agencia,
                                  bp.id_periodo_venta,
                                  ag.nombre,
                                  COALESCE(TO_CHAR(bp.fecha_ini,''DD'')||'' al ''|| TO_CHAR(bp.fecha_fin,''DD'')||'' ''||bp.mes||'' ''||EXTRACT(YEAR FROM bp.fecha_ini),''Periodo Vigente'')::text as periodo,
                                  bp.monto_debito,
                                   null::numeric as monto_deposito,
                                  (select pe.fecha_pago
                                   from obingresos.tperiodo_venta pe
                                   where pe.id_periodo_venta = bp.id_periodo_venta) as fecha_pago,
                                   null::date as fecha,
                                   null::varchar as nro_deposito,
                                   null::numeric as garante,
                                   (cr.monto_total - bp.monto_debito) as monto_sin_boleta,
                                   null::varchar nro_deposito_boa
                          from obingresos.vboletos_a_pagar bp
                          inner join obingresos.tagencia ag on ag.id_agencia = bp.id_agencia
                          left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                          where bp.id_agencia = '||v_parametros.id_agencia||' and EXTRACT(YEAR FROM bp.fecha_ini) = '''||v_parametros.año_ini||''' and bp.id_periodo_venta = (select max( bp.id_periodo_venta)
                                                                              from obingresos.vboletos_a_pagar bp
                                                                              where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_ini <= '''||v_parametros.fecha_ini||'''
                                                                              ))


 /*********************************************************/



                          union

                  (    select  ''deposito''::varchar as tipo,
                              db.id_agencia,
                              db.id_periodo_venta,
                              null::varchar as nombre,
                              null::text as periodo,
                              null::numeric as monto_debito,
                              db.monto_deposito,
                              null::date as fecha_pago,
                              db.fecha,
                              db.nro_deposito,
                              (select  COALESCE(mo.monto,0)
                              from obingresos.tmovimiento_entidad mo
                              where mo.id_agencia =  '||v_parametros.id_agencia||' and
                              mo.garantia = ''si'' and mo.id_periodo_venta is null) as garante,
                              null::numeric as monto_sin_boleta,
                              db.nro_deposito_boa
                              from obingresos.vdepositos_imp db
                              left join obingresos.vboletos_a_pagar bp on bp.id_agencia = db.id_agencia and bp.id_periodo_venta = db.id_periodo_venta
                      		  where db.id_agencia = '||v_parametros.id_agencia||' and bp.fecha_ini >= '''||v_parametros.fecha_ini||''' and bp.fecha_fin <= '''||v_parametros.fecha_fin||'''

                        union

                              /*Inicio Periodo*/

                              select  ''deposito''::varchar as tipo,
                              db.id_agencia,
                              db.id_periodo_venta,
                              null::varchar as nombre,
                              null::text as periodo,
                              null::numeric as monto_debito,
                              db.monto_deposito,
                              null::date as fecha_pago,
                              db.fecha,
                              db.nro_deposito,
                              (select  COALESCE(mo.monto,0)
                              from obingresos.tmovimiento_entidad mo
                              where mo.id_agencia =  '||v_parametros.id_agencia||' and
                              mo.garantia = ''si'' and mo.id_periodo_venta is null) as garante,
                              null::numeric as monto_sin_boleta,
                              db.nro_deposito_boa
                              from obingresos.vdepositos_imp db
                              --left join obingresos.vboletos_a_pagar bp on bp.id_agencia = db.id_agencia and bp.id_periodo_venta = db.id_periodo_venta
                      		  where db.id_agencia = '||v_parametros.id_agencia||'  and  db.id_periodo_venta >= (select max(bp.id_periodo_venta)
                                                                            from obingresos.vboletos_a_pagar bp
                                                                            where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_ini <= '''||v_parametros.fecha_ini||'''
                                                                           )
                              and db.id_periodo_venta <= (select max(bp.id_periodo_venta)
                                                                            from obingresos.vboletos_a_pagar bp
                                                                            where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_fin <= '''||v_parametros.fecha_fin||'''
                                                                           )

                               union
                              /*Fin Periodo*/
                              select  ''deposito''::varchar as tipo,
                              db.id_agencia,
                              db.id_periodo_venta,
                              null::varchar as nombre,
                              null::text as periodo,
                              null::numeric as monto_debito,
                              db.monto_deposito,
                              null::date as fecha_pago,
                              db.fecha,
                              db.nro_deposito,
                              (select  COALESCE(mo.monto,0)
                              from obingresos.tmovimiento_entidad mo
                              where mo.id_agencia =  '||v_parametros.id_agencia||' and
                              mo.garantia = ''si'' and mo.id_periodo_venta is null) as garante,
                              null::numeric as monto_sin_boleta,
                              db.nro_deposito_boa
                              from obingresos.vdepositos_imp db
                              left join obingresos.vboletos_a_pagar bp on bp.id_agencia = db.id_agencia and bp.id_periodo_venta = db.id_periodo_venta
                      		  where db.id_agencia = '||v_parametros.id_agencia||'  and  db.id_periodo_venta = (select bp.id_periodo_venta
                                                                            from obingresos.vboletos_a_pagar bp
                                                                            where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_fin >= '''||v_parametros.fecha_fin||'''
                                                                            FETCH FIRST 1 ROW ONLY
                                                                           )  )


                   order by id_periodo_venta ,fecha';

       --Devuelve la respuesta
    return v_consulta;
	end;
    /*********************************
     #TRANSACCION:  'OBING_PERANT_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		30-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_PERANT_SEL')then

    begin

      		v_consulta = 'select  ''boletos''::varchar as tipo,
                                  bp.id_agencia,
                                  bp.id_periodo_venta,
                                  ag.nombre,
                                  COALESCE(TO_CHAR(bp.fecha_ini,''DD'')||'' al ''|| TO_CHAR(bp.fecha_fin,''DD'')||'' ''||bp.mes||'' ''||EXTRACT(YEAR FROM bp.fecha_ini),''Periodo Vigente'')::text as periodo,
                                  bp.monto_debito,
                                   null::numeric as monto_deposito,
                                  (select pe.fecha_pago
                                   from obingresos.tperiodo_venta pe
                                   where pe.id_periodo_venta = bp.id_periodo_venta) as fecha_pago,
                                   null::date as fecha,
                                   null::varchar as nro_deposito,
                                   null::numeric as garante,
                                   (COALESCE( cr.monto_total,0) - COALESCE( bp.monto_debito,0)) as monto_anterior
                          from obingresos.vboletos_a_pagar bp
                          inner join obingresos.tagencia ag on ag.id_agencia = bp.id_agencia
                          left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                          where bp.id_agencia = '||v_parametros.id_agencia||' and bp.id_periodo_venta = (select max(bp.id_periodo_venta)
                                                                              from obingresos.vboletos_a_pagar bp
                                                                              left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                                                                              where bp.id_agencia = '||v_parametros.id_agencia||' and bp.fecha_fin <= '''||v_parametros.fecha_ini||''')


    					UNION



(select  ''SinBoleta''::varchar as tipo,
                                  bp.id_agencia,
                                  bp.id_periodo_venta,
                                  ag.nombre,
                                  COALESCE(TO_CHAR(bp.fecha_ini,''DD'')||'' al ''|| TO_CHAR(bp.fecha_fin,''DD'')||'' ''||bp.mes||'' ''||EXTRACT(YEAR FROM bp.fecha_ini),''Periodo Vigente'')::text as periodo,
                                  bp.monto_debito,
                                   null::numeric as monto_deposito,
                                  (select pe.fecha_pago
                                   from obingresos.tperiodo_venta pe
                                   where pe.id_periodo_venta = bp.id_periodo_venta) as fecha_pago,
                                   null::date as fecha,
                                   null::varchar as nro_deposito,
                                   null::numeric as garante,
                                   (COALESCE( cr.monto_total,0) - COALESCE( bp.monto_debito,0)) as monto_anterior
                          from obingresos.vboletos_a_pagar bp
                          inner join obingresos.tagencia ag on ag.id_agencia = bp.id_agencia
                          left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                          where bp.id_agencia = '||v_parametros.id_agencia||' and bp.id_periodo_venta = (select max(bp.id_periodo_venta)
                                                                              from obingresos.vboletos_a_pagar bp
                                                                              left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                                                                              where bp.id_agencia = '||v_parametros.id_agencia||' and bp.fecha_ini <= '''||v_parametros.fecha_fin||''')
UNION

select  ''SinBoleta''::varchar as tipo,
                                  bp.id_agencia,
                                  bp.id_periodo_venta,
                                  ag.nombre,
                                   COALESCE(TO_CHAR(bp.fecha_ini,''DD'')||'' al ''|| TO_CHAR(bp.fecha_fin,''DD'')||'' ''||bp.mes||'' ''||EXTRACT(YEAR FROM bp.fecha_ini),''Periodo Vigente'')::text as periodo,
                                  bp.monto_debito,
                                   null::numeric as monto_deposito,
                                  (select pe.fecha_pago
                                   from obingresos.tperiodo_venta pe
                                   where pe.id_periodo_venta = bp.id_periodo_venta) as fecha_pago,
                                   null::date as fecha,
                                   null::varchar as nro_deposito,
                                   null::numeric as garante,
                                   (COALESCE( cr.monto_total,0) - COALESCE( bp.monto_debito,0)) as monto_anterior
                          from obingresos.vboletos_a_pagar bp
                          inner join obingresos.tagencia ag on ag.id_agencia = bp.id_agencia
                          left join obingresos.vcredito_ag cr on cr.id_agencia = bp.id_agencia and cr.id_periodo_venta = bp.id_periodo_venta
                          where bp.id_agencia = '||v_parametros.id_agencia||' and bp.id_periodo_venta = (select bp.id_periodo_venta
                                                                              from obingresos.vboletos_a_pagar bp
                                                                              where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_fin >= '''||v_parametros.fecha_fin||'''
                                                                              FETCH FIRST 1 ROW ONLY)
                          and EXTRACT(YEAR FROM bp.fecha_fin)='''||v_parametros.año_ini||''')';

       --Devuelve la respuesta
    return v_consulta;
	end;

          /*********************************
     #TRANSACCION:  'OBING_MOV_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_MOV_SEL')then

      begin

    v_consulta = '(with aux as (select mo.tipo,
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
                                       mo.monto_total,
                                       COALESCE(au.monto_total,0) as  monto_total_debito,
                                        mo.monto_total - COALESCE(au.monto_total,0) as saldo,
                                        mcb.monto_total - COALESCE(au.monto_total,0) as saldo2
                                       from obingresos.vcredito_ag mo
                                       inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                                       inner join obingresos.vcredito_ag_boleta mcb on mcb.id_agencia = mo.id_agencia and mo.id_periodo_venta = mcb.id_periodo_venta
                                       left join aux au on  COALESCE (au.id_periodo_venta,0) = COALESCE(mo.id_periodo_venta,0)
                                       where mo.id_agencia = '||v_parametros.id_agencia||'
                                       and mo.fecha_ini >= '''||v_parametros.fecha_ini||'''
                                       and mo.fecha_ini <= '''||v_parametros.fecha_fin||''' )

UNION

/*-------------------------------------------INICIO DEL PERIODO----------------------------------------------------*/
(
(with aux as (select mo.tipo,
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
                                       mo.monto_total,
                                       COALESCE(au.monto_total,0) as  monto_total_debito,
                                        mo.monto_total - COALESCE(au.monto_total,0) as saldo,
                                        mcb.monto_total - COALESCE(au.monto_total,0) as saldo2
                                       from obingresos.vcredito_ag mo
                                       inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                                       inner join obingresos.vcredito_ag_boleta mcb on mcb.id_agencia = mo.id_agencia and mo.id_periodo_venta = mcb.id_periodo_venta
                                       left join aux au on  COALESCE (au.id_periodo_venta,0) = COALESCE(mo.id_periodo_venta,0)
                                       where mo.id_agencia = '||v_parametros.id_agencia||'
                                       and mo.id_periodo_venta >= (select max(bp.id_periodo_venta)
                                                                            from obingresos.vcredito_ag bp
                                                                            where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_ini <= '''||v_parametros.fecha_ini||'''
                                                                           )
                                       and mo.id_periodo_venta <= (select max(bp.id_periodo_venta)
                                                                            from obingresos.vcredito_ag bp
                                                                            where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_fin <= '''||v_parametros.fecha_fin||'''
                                                                           ))

UNION

/*-----------------------------------------------FIN DEL PERIODO-----------------------------------------------------------------*/

(with aux as (select mo.tipo,
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
                                       mo.monto_total,
                                       COALESCE(au.monto_total,0) as  monto_total_debito,
                                        mo.monto_total - COALESCE(au.monto_total,0) as saldo,
                                        mcb.monto_total - COALESCE(au.monto_total,0) as saldo2
                                       from obingresos.vcredito_ag mo
                                       inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                                       inner join obingresos.vcredito_ag_boleta mcb on mcb.id_agencia = mo.id_agencia and mo.id_periodo_venta = mcb.id_periodo_venta
                                       left join aux au on  COALESCE (au.id_periodo_venta,0) = COALESCE(mo.id_periodo_venta,0)
                                       where mo.id_agencia = '||v_parametros.id_agencia||'
                                       and mo.id_periodo_venta = (select bp.id_periodo_venta
                                                                            from obingresos.vcredito_ag bp
                                                                            where bp.id_agencia='||v_parametros.id_agencia||' and bp.fecha_fin >= '''||v_parametros.fecha_fin||'''
                                                                            FETCH FIRST 1 ROW ONLY
                                                                           ))
                               )
                                order by id_periodo_venta';

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
