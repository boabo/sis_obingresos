CREATE OR REPLACE FUNCTION obingresos.ft_detalle_debito_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_detalle_debito_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdetalle_debito'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        18-07-2018 16:54:10
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				18-07-2018 16:54:10								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdetalle_debito'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_detalle_debito_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DBR_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani
 	#FECHA:		18-07-2018 16:54:10
	***********************************/

	if(p_transaccion='OBING_DBR_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select dw.id_detalle_boletos_web,
                                mo.id_agencia,
                                mo.id_periodo_venta,
                                mo.id_movimiento_entidad,
                                dw.numero_autorizacion,
                                mo.pnr ||'' - '' ||dw.billete::text as nro_boleto,
                                dw.fecha,
                                dw.importe - dw.comision as monto,
                                dw.neto,
                                dw.comision,
                                dw.importe as total_monto
                                from obingresos.tmovimiento_entidad mo
                                inner join obingresos.tdetalle_boletos_web dw on dw.numero_autorizacion = mo.autorizacion__nro_deposito
                                where mo.tipo = ''debito'' and
                                mo.estado_reg = ''activo'' and
                                mo.cierre_periodo = ''no'' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DBR_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		18-07-2018 16:54:10
	***********************************/

	elsif(p_transaccion='OBING_DBR_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select  count(dw.id_detalle_boletos_web),
            					 sum(dw.importe - dw.comision) as monto_total,
                                 sum(dw.neto)as neto_total,
                                 sum(dw.comision)as comision_total,
                                 sum(dw.importe) as total_monto
                                from obingresos.tmovimiento_entidad mo
                                inner join obingresos.tdetalle_boletos_web dw on dw.numero_autorizacion = mo.autorizacion__nro_deposito
                                where mo.tipo = ''debito'' and
                                mo.estado_reg = ''activo'' and
                                mo.cierre_periodo = ''no'' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

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