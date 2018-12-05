CREATE OR REPLACE FUNCTION obingresos.ft_depositos_periodo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
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
 	#TRANSACCION:  'OB_DE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		28-09-2017 18:47:46
	***********************************/

	if (p_transaccion='OB_DE_SEL')then

        begin
		v_consulta = 'select  mo.id_movimiento_entidad,
                              mo.id_agencia,
                              mo.id_periodo_venta,
                              mo.gestion::varchar as gestion,
                              mo.mes,
                              mo.fecha_ini,
                              mo.fecha_fin,
                              mo.fecha,
                              mo.autorizacion__nro_deposito,
                              mo.monto_total,
                              dep.nro_deposito
                              from obingresos.vdepositos_periodo mo
                              left join obingresos.tdeposito dep on dep.nro_deposito_boa = mo.autorizacion__nro_deposito
                              where ';
       --Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
		end;
    /*********************************
     #TRANSACCION:  'OB_DE_CONT'
     #DESCRIPCION:	Reporte saldo vigente
     #AUTOR:		MMV
     #FECHA:		18-11-2018
    ***********************************/
    elsif(p_transaccion='OB_DE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros

			v_consulta:='select  count(mo.id_movimiento_entidad),
            					 sum(mo.monto_total) as suma_total
                                 from obingresos.vdepositos_periodo mo
                              	 left join obingresos.tdeposito dep on dep.nro_deposito_boa = mo.autorizacion__nro_deposito
                                 where ';
			v_consulta:=v_consulta||v_parametros.filtro;
            raise notice 'cos -> %',v_consulta;
			--Devuelve la respuest
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
