CREATE OR REPLACE FUNCTION obingresos.ft_instancia_pago_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_instancia_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tinstancia_pago'
 AUTOR: 		 (admin)
 FECHA:	        04-06-2019 19:31:28
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				04-06-2019 19:31:28								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tinstancia_pago'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_instancia_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_INSP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:31:28
	***********************************/

	if(p_transaccion='OBING_INSP_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
						insp.id_instancia_pago,
						insp.estado_reg,
						insp.id_medio_pago,
                        --insp.instancia_pago_id,
						insp.nombre,
						--insp.codigo_medio_pago,
						insp.id_usuario_reg,
						insp.fecha_reg,
						insp.id_usuario_ai,
						insp.usuario_ai,
						insp.id_usuario_mod,
						insp.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        insp.fp_code,
                        insp.ins_code,

                        /*(Ismael Valdivia 16/10/2020)*/


                        mp.name,
                        insp.codigo,
                        fp.fop_code as codigo_fp,
                        mp.mop_code as codigo_mp
                        /************************************************************************/

						from obingresos.tinstancia_pago insp
						inner join segu.tusuario usu1 on usu1.id_usuario = insp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = insp.id_usuario_mod
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = insp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'Aqui llega consulta %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_INSP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		04-06-2019 19:31:28
	***********************************/

	elsif(p_transaccion='OBING_INSP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_instancia_pago)
					    from obingresos.tinstancia_pago insp
						inner join segu.tusuario usu1 on usu1.id_usuario = insp.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = insp.id_usuario_mod
                        inner join obingresos.tmedio_pago_pw mp on mp.id_medio_pago_pw = insp.id_medio_pago
                        inner join obingresos.tforma_pago_pw fp on fp.id_forma_pago_pw = mp.forma_pago_id
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

ALTER FUNCTION obingresos.ft_instancia_pago_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
