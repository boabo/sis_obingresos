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
		v_consulta = 'select
						dep.id_deposito,
                        age.nombre,
						dep.estado_reg,
						dep.nro_deposito,
                        dep.nro_deposito_boa,
						dep.monto_deposito,
						dep.id_agencia,
						dep.fecha,
                        dep.estado,
                        dep.id_apertura_cierre_caja
                        from obingresos.tdeposito dep
                        left join obingresos.tagencia age on age.id_agencia = dep.id_agencia
                        left join obingresos.tperiodo_venta pv on pv.id_periodo_venta = dep.id_periodo_venta
                        left join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
                        left join obingresos.tperiodo_venta per on per.id_tipo_periodo = tp.id_tipo_periodo
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

			v_consulta:='select  count(dep.id_deposito),
            					 sum(dep.monto_deposito) as suma_total
                                 from obingresos.tdeposito dep
                        left join obingresos.tagencia age on age.id_agencia = dep.id_agencia
                        left join obingresos.tperiodo_venta pv on pv.id_periodo_venta = dep.id_periodo_venta
                        left join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
                        left join obingresos.tperiodo_venta per on per.id_tipo_periodo = tp.id_tipo_periodo
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
