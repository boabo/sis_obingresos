CREATE OR REPLACE FUNCTION "obingresos"."ft_consulta_viajero_frecuente_sel"(
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_consulta_viajero_frecuente_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tconsulta_viajero_frecuente'
 AUTOR: 		 (miguel.mamani)
 FECHA:	        15-12-2017 14:59:25
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-12-2017 14:59:25								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tconsulta_viajero_frecuente'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_consulta_viajero_frecuente_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_VIF_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		miguel.mamani
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	if(p_transaccion='OBING_VIF_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						vif.id_consulta_viajero_frecuente,
						vif.ffid,
						vif.estado_reg,
						vif.message,
						vif.voucher_code,
						vif.status,
            substring(vif.nro_boleto
          from 4)::varchar,
						vif.id_usuario_reg,
						vif.fecha_reg,
						vif.usuario_ai,
						vif.id_usuario_ai,
						vif.fecha_mod,
						vif.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from obingresos.tconsulta_viajero_frecuente vif
						inner join segu.tusuario usu1 on usu1.id_usuario = vif.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = vif.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_VIF_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		miguel.mamani
 	#FECHA:		15-12-2017 14:59:25
	***********************************/

	elsif(p_transaccion='OBING_VIF_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_consulta_viajero_frecuente)
					    from obingresos.tconsulta_viajero_frecuente vif
					    inner join segu.tusuario usu1 on usu1.id_usuario = vif.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = vif.id_usuario_mod
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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "obingresos"."ft_consulta_viajero_frecuente_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
