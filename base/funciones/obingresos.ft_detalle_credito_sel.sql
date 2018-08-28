CREATE OR REPLACE FUNCTION obingresos.ft_detalle_credito_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_detalle_credito_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdetalle_credito'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        18-07-2018 16:53:28
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				18-07-2018 16:53:28								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdetalle_credito'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_detalle_credito_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_RDC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani
 	#FECHA:		18-07-2018 16:53:28
	***********************************/

	if(p_transaccion='OBING_RDC_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='  select   mo.id_movimiento_entidad,
                                    mo.id_agencia,
                                    mo.id_periodo_venta,
                                   COALESCE(mo.autorizacion__nro_deposito,''Saldo del Peiodo Anterior'')as nro_deposito,
                                    mo.monto_total,
                                    mo.fecha
                                    from obingresos.tmovimiento_entidad mo
                                    where	mo.tipo = ''credito'' and
                                          mo.estado_reg = ''activo'' and
                                          mo.garantia = ''no'' and  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_RDC_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		18-07-2018 16:53:28
	***********************************/

	elsif(p_transaccion='OBING_RDC_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:=' select 	count(mo.id_movimiento_entidad),
            						sum(mo.monto_total) as suma_total
                                	from obingresos.tmovimiento_entidad mo
                                	where mo.tipo = ''credito'' and
                                      mo.estado_reg = ''activo'' and
                                      mo.garantia = ''no'' and ';

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