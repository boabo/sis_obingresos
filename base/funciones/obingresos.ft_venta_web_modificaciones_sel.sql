CREATE OR REPLACE FUNCTION obingresos.ft_venta_web_modificaciones_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
  RETURNS varchar AS
  $body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_venta_web_modificaciones_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tventa_web_modificaciones'
 AUTOR: 		 (jrivera)
 FECHA:	        11-01-2017 19:44:28
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
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_venta_web_modificaciones_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_VWEBMOD_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		11-01-2017 19:44:28
	***********************************/

	if(p_transaccion='OBING_VWEBMOD_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						vwebmod.id_venta_web_modificaciones,
						vwebmod.nro_boleto,
						vwebmod.tipo,
						vwebmod.motivo,
						vwebmod.nro_boleto_reemision,
						vwebmod.used,
						vwebmod.estado_reg,
						vwebmod.id_usuario_ai,
						vwebmod.usuario_ai,
						vwebmod.fecha_reg,
						vwebmod.id_usuario_reg,
						vwebmod.fecha_mod,
						vwebmod.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        (case when vwebmod.tipo = ''reemision'' then
                        	vwebmod.procesado
                        else
                        	''no aplica''
                        end) as procesado,
                        (case when b.voided is not null and b.voided = ''si'' then
                        	''si''
                        else
                        	''no''
                        end)::varchar as anulado
						from obingresos.tventa_web_modificaciones vwebmod
						inner join segu.tusuario usu1 on usu1.id_usuario = vwebmod.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = vwebmod.id_usuario_mod
                        left join obingresos.tboleto b on  b.nro_boleto = vwebmod.nro_boleto and b.estado_reg = ''activo''
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_VWEBMOD_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		11-01-2017 19:44:28
	***********************************/

	elsif(p_transaccion='OBING_VWEBMOD_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_venta_web_modificaciones)
					    from obingresos.tventa_web_modificaciones vwebmod
					    inner join segu.tusuario usu1 on usu1.id_usuario = vwebmod.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = vwebmod.id_usuario_mod
					    left join obingresos.tboleto b on  b.nro_boleto = vwebmod.nro_boleto and b.estado_reg = ''activo''
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