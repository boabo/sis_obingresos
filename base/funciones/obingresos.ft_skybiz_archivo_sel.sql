CREATE OR REPLACE FUNCTION obingresos.ft_skybiz_archivo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_skybiz_archivo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tskybiz_archivo'
 AUTOR: 		 (admin)
 FECHA:	        15-02-2017 15:18:39
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

	v_nombre_funcion = 'obingresos.ft_skybiz_archivo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_SKYBIZ_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 15:18:39
	***********************************/

	if(p_transaccion='OBING_SKYBIZ_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
  skybiz.id_skybiz_archivo,
  skybiz.fecha,
  skybiz.subido,
  skybiz.comentario,
  skybiz.estado_reg,
  skybiz.nombre_archivo,
  skybiz.id_usuario_ai,
  skybiz.usuario_ai,
  skybiz.fecha_reg,
  skybiz.id_usuario_reg,
  skybiz.id_usuario_mod,
  skybiz.fecha_mod,
  usu1.cuenta as usr_reg,
  usu2.cuenta as usr_mod,
  skybiz.moneda,
  skybiz.banco,
  sum(det.total_amount) as total
from obingresos.tskybiz_archivo skybiz
  inner join segu.tusuario usu1 on usu1.id_usuario = skybiz.id_usuario_reg
  left join segu.tusuario usu2 on usu2.id_usuario = skybiz.id_usuario_mod
INNER JOIN obingresos.tskybiz_archivo_detalle det on det.id_skybiz_archivo = skybiz.id_skybiz_archivo

				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

				v_consulta:= ' '|| v_consulta || ' GROUP BY skybiz.id_skybiz_archivo,
  skybiz.fecha,
  skybiz.subido,
  skybiz.comentario,
  skybiz.estado_reg,
  skybiz.nombre_archivo,
  skybiz.id_usuario_ai,
  skybiz.usuario_ai,
  skybiz.fecha_reg,
  skybiz.id_usuario_reg,
  skybiz.id_usuario_mod,
  skybiz.fecha_mod,
  usu1.cuenta ,
usu2.cuenta ,
skybiz.moneda,
skybiz.banco';

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;


			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_SKYBIZ_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		15-02-2017 15:18:39
	***********************************/

	elsif(p_transaccion='OBING_SKYBIZ_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_skybiz_archivo)
					    from obingresos.tskybiz_archivo skybiz
					    inner join segu.tusuario usu1 on usu1.id_usuario = skybiz.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = skybiz.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;
			raise notice '%',v_consulta;
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