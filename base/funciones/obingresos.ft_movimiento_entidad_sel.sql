CREATE OR REPLACE FUNCTION obingresos.ft_movimiento_entidad_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_movimiento_entidad_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tmovimiento_entidad'
 AUTOR: 		 (jrivera)
 FECHA:	        17-05-2017 15:53:35
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_id_moneda_base	integer;
    v_id_moneda_usd		integer;

BEGIN

	v_nombre_funcion = 'obingresos.ft_movimiento_entidad_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_MOE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		17-05-2017 15:53:35
	***********************************/

	if(p_transaccion='OBING_MOE_SEL')then

    	begin
        	v_id_moneda_base = (select param.f_get_moneda_base());
            select m.id_moneda into v_id_moneda_usd
            from param.tmoneda m
            where m.codigo_internacional = 'USD';

    		--Sentencia de la consulta
			v_consulta:='select
						moe.id_movimiento_entidad,
						moe.id_moneda,
						moe.id_periodo_venta,
						moe.id_agencia,
						moe.garantia,
						moe.monto_total,
						moe.tipo,
						moe.autorizacion__nro_deposito,
						moe.estado_reg,
						(case when moe.tipo = ''credito'' then
                        	moe.monto
                        else
                        	0
                        end) as credito,
                        (case when moe.tipo = ''debito'' then
                        	moe.monto
                        else
                        	0
                        end) as debito,
						moe.ajuste,
						moe.fecha,
						moe.pnr,
						moe.apellido,
						moe.id_usuario_reg,
						moe.fecha_reg,
						moe.usuario_ai,
						moe.id_usuario_ai,
						moe.fecha_mod,
						moe.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        mon.codigo_internacional as moneda,
                        (case when moe.tipo = ''credito'' then
                        	(case when moe.id_moneda = ' || v_id_moneda_base || ' then
                            	moe.monto
                            else
                            	param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',moe.monto,moe.fecha,''O'',2)
                            end)
                        else
                        	0
                        end)::numeric as credito_mb,
                        (case when moe.tipo = ''debito'' then
                        	(case when moe.id_moneda = ' || v_id_moneda_base || ' then
                            	moe.monto
                            else
                            	param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',moe.monto,moe.fecha,''O'',2)
                            end)
                        else
                        	0
                        end)::numeric as debito_mb,
                        (case when moe.id_moneda = ' || v_id_moneda_base || ' then
                            1
                        else
                            param.f_get_tipo_cambio(' || v_id_moneda_usd ||  ',moe.fecha,''O'')
                        end)::numeric as tipo_cambio,
                        moe.monto,
                        depo.nro_deposito,
                        depo.id_deposito
						from obingresos.tmovimiento_entidad moe
						inner join segu.tusuario usu1 on usu1.id_usuario = moe.id_usuario_reg
						inner join param.tmoneda mon on mon.id_moneda = moe.id_moneda
                        left join segu.tusuario usu2 on usu2.id_usuario = moe.id_usuario_mod
                        left join obingresos.tdeposito depo on depo.nro_deposito = moe.autorizacion__nro_deposito
				        where  moe.estado_reg = ''activo'' and
                        (moe.cierre_periodo = ''no'' or (moe.cierre_periodo = ''si'' and moe.tipo = ''credito'')) and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_MOE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		17-05-2017 15:53:35
	***********************************/

	elsif(p_transaccion='OBING_MOE_CONT')then

		begin
        	v_id_moneda_base = (select param.f_get_moneda_base());
            select m.id_moneda into v_id_moneda_usd
            from param.tmoneda m
            where m.codigo_internacional = 'USD';

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_movimiento_entidad) as total,
            			sum(case when moe.tipo = ''credito'' then
                        	(case when moe.id_moneda = ' || v_id_moneda_base || ' then
                            	moe.monto
                            else
                            	param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',moe.monto,moe.fecha,''O'',2)
                            end)
                        else
                        	0
                        end)::numeric as credito,
                        sum(case when moe.tipo = ''debito'' then
                        	(case when moe.id_moneda = ' || v_id_moneda_base || ' then
                            	moe.monto
                            else
                            	param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',moe.monto,moe.fecha,''O'',2)
                            end)
                        else
                        	0
                        end)::numeric as debito,
                        sum(case when moe.tipo = ''credito'' then
                        	moe.monto
                        else
                        	0
                        end) as credito,
                        sum(case when moe.tipo = ''debito'' then
                        	moe.monto
                        else
                        	0
                        end) as debito,
                        sum(moe.monto_total),
                        (case
                        when (select rt.tipo_pago
                              from obingresos.tagencia rt
                              where rt.id_agencia ='|| v_parametros.id_agencia ||') = ''postpago'' then
                         (select  sum( mo.monto)
from obingresos.tmovimiento_entidad mo
where mo.id_agencia = '|| v_parametros.id_agencia ||' and mo.garantia = ''no'' and mo.tipo =''credito'' and mo.id_periodo_venta is null)-( sum(case when moe.tipo = ''debito'' then
                        	moe.monto
                        else
                        	0
                        end))
                        else
                        (select  mo.monto
from obingresos.tmovimiento_entidad mo
where mo.id_agencia = ' || v_parametros.id_agencia || '  and mo.garantia = ''no'' and mo.id_periodo_venta is null and
mo.estado_reg = ''activo'' and mo.cierre_periodo = ''si'' and mo.tipo = ''credito'')
-
(select  sum(mo.monto)
from obingresos.tmovimiento_entidad mo
where mo.id_agencia = ' || v_parametros.id_agencia || ' and mo.garantia = ''no'' and mo.tipo =''debito'' and mo.id_periodo_venta is null and
mo.cierre_periodo = ''no'' and mo.estado_reg = ''activo'')
                        end)::numeric as saldo_actual,
                        ( case
						when
(select  mo.monto
from obingresos.tmovimiento_entidad mo
where mo.id_agencia = ' || v_parametros.id_agencia || '  and mo.garantia = ''no'' and mo.id_periodo_venta is null and
mo.estado_reg = ''activo'' and mo.cierre_periodo = ''si'' and mo.tipo = ''credito'')
-
(select  sum(mo.monto)
from obingresos.tmovimiento_entidad mo
where mo.id_agencia = ' || v_parametros.id_agencia || ' and mo.garantia = ''no'' and mo.tipo =''debito'' and mo.id_periodo_venta is null and
mo.cierre_periodo = ''no'' and mo.estado_reg = ''activo'') > 0 then
''Saldo a Favor''
when
(select  mo.monto
from obingresos.tmovimiento_entidad mo
where mo.id_agencia = ' || v_parametros.id_agencia || ' and mo.garantia = ''no'' and mo.id_periodo_venta is null and
mo.estado_reg = ''activo'' and mo.cierre_periodo = ''si'' and mo.tipo = ''credito'')
-
(select  sum(mo.monto)
from obingresos.tmovimiento_entidad mo
where mo.id_agencia = ' || v_parametros.id_agencia || ' and mo.garantia = ''no'' and mo.tipo =''debito'' and mo.id_periodo_venta is null and
mo.cierre_periodo = ''no'' and mo.estado_reg = ''activo'') = 0 then
''Deuda Actual''
else
''Deuda Actual''
end::varchar)as tipo,
                        coalesce((select sum(coalesce (pva.monto_mb,0) +
                                    param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',COALESCE(pva.monto_usd,0),now()::date,''O'',2))
                        from obingresos.tperiodo_venta_agencia pva
                        where pva.id_agencia = ' || v_parametros.id_agencia || ' and pva.estado= ''abierto''),0) as deudas
					    from obingresos.tmovimiento_entidad moe
              inner join param.tmoneda mon on mon.id_moneda = moe.id_moneda
					    inner join segu.tusuario usu1 on usu1.id_usuario = moe.id_usuario_reg
						  left join segu.tusuario usu2 on usu2.id_usuario = moe.id_usuario_mod
              left join obingresos.tdeposito depo on depo.nro_deposito = moe.autorizacion__nro_deposito
					    where moe.estado_reg = ''activo'' and
              (moe.cierre_periodo = ''no'' or (moe.cierre_periodo = ''si'' and moe.tipo = ''credito'')) and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			raise notice '-> %',v_consulta;
			--Devuelve la respuesta
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