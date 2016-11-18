/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_detalle_boletos_web_sel
 DESCRIPCION:
 AUTOR: 		 (admin)
 FECHA:	        18-11-2016
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

	v_nombre_funcion = 'obingresos.ft_detalle_boletos_web_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DETBOWEB_SEL'
 	#DESCRIPCION:	Reporte nit razon
 	#AUTOR:		MAM
 	#FECHA:		18-11-2016
	***********************************/

	if(p_transaccion='OBING_DETBOWEB_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='SELECT
            				b.fecha_emision,
							d.billete,
        					d.entidad_pago,
       					 	d.nit,
        					d.razon_social,
                            d.importe
        					FROM obingresos.tboleto b
    						INNER JOIN  obingresos.tdetalle_boletos_web d on d.billete = b.nro_boleto
                            where b.fecha_emision >= '''||v_parametros.fecha_ini||'''and b.fecha_emision <= '''||v_parametros.fecha_fin||''' ';
			--Definicion de la respuesta
			--v_consulta:=v_consulta||v_parametros.filtro;
			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

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