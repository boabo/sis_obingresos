CREATE OR REPLACE FUNCTION obingresos.ft_viajero_frecuente_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_viajero_frecuente_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tviajero_frecuente'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        12-12-2017 19:32:55
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				12-12-2017 19:32:55								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tviajero_frecuente'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_viajero_frecuente_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_VFB_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani
 	#FECHA:		12-12-2017 19:32:55
	***********************************/

	if(p_transaccion='OBING_VFB_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
						vfb.id_viajero_frecuente,
						vfb.nombre_completo,
						vfb.voucher_code,
						vfb.estado_reg,
						vfb.pnr,
						vfb.status,
						vfb.ffid,
						vfb.ticket_number,
						vfb.mensaje,
						vfb.id_pasajero_frecuente,
						vfb.id_boleto_amadeus,
						vfb.id_usuario_reg,
						vfb.fecha_reg,
						vfb.usuario_ai,
						vfb.id_usuario_ai,
						vfb.id_usuario_mod,
						vfb.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from obingresos.tviajero_frecuente vfb
						inner join segu.tusuario usu1 on usu1.id_usuario = vfb.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = vfb.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_VFB_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		12-12-2017 19:32:55
	***********************************/

	elsif(p_transaccion='OBING_VFB_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_viajero_frecuente)
					    from obingresos.tviajero_frecuente vfb
					    inner join segu.tusuario usu1 on usu1.id_usuario = vfb.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = vfb.id_usuario_mod
					    where ';

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