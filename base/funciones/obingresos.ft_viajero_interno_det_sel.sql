CREATE OR REPLACE FUNCTION obingresos.ft_viajero_interno_det_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_viajero_interno_det_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tviajero_interno_det'
 AUTOR: 		 (rzabala)
 FECHA:	        21-12-2018 14:21:07
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				21-12-2018 14:21:07								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tviajero_interno_det'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_viajero_interno_det_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DVI_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:07
	***********************************/

	if(p_transaccion='OBING_DVI_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						dvi.id_viajero_interno_det,
						dvi.estado_reg,
						dvi.nombre,
						(UPPER(dvi.pnr))::varchar,
						dvi.num_boleto,
						dvi.id_usuario_reg,
						dvi.fecha_reg,
						dvi.id_usuario_ai,
						dvi.usuario_ai,
						dvi.id_usuario_mod,
						dvi.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        dvi.id_viajero_interno,
                        dvi.solicitud,
                        dvi.num_documento,
                        dvi.estado_voucher,
                        dvi.tarifa
						from obingresos.tviajero_interno_det dvi
						inner join segu.tusuario usu1 on usu1.id_usuario = dvi.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dvi.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DVI_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		rzabala
 	#FECHA:		21-12-2018 14:21:07
	***********************************/

	elsif(p_transaccion='OBING_DVI_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_viajero_interno_det)
					    from obingresos.tviajero_interno_det dvi
					    inner join segu.tusuario usu1 on usu1.id_usuario = dvi.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dvi.id_usuario_mod
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