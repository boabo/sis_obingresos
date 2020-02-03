CREATE OR REPLACE FUNCTION obingresos.ft_reporte_banca_boletos_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_reporte_banca_boletos_sel
 DESCRIPCION:   Funcion que devuelve la lista de datos de la tabla tboletos_banca para obtener informacion
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        03-01-2020 10:54:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
 #ISSUE				FECHA				AUTOR				DESCRIPCION
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_reporte_banca_boletos_sel';
    v_parametros = pxp.f_get_record(p_tabla);


    /*********************************
     #TRANSACCION:  'OBING_BOLEBANC_SEL'
     #DESCRIPCION:	Lista de datos boletos banca electronica
     #AUTOR:		Ismael Valdivia
     #FECHA:		03-01-2020
    ***********************************/
    if(p_transaccion='OBING_BOLEBANC_SEL')then

        begin

        v_consulta ='select
                          banca.agencia_id,
                          ag.nombre,
                          ag.codigo_int,
                          ag.tipo_agencia,
                          lu.codigo as nombre_lugar,
                          mon.codigo_internacional,
                          (sum(banca.monto_total) - sum(banca.comision)) as monto_boa,
                          sum(banca.comision) as monto_agencia,
                    	  ((sum(banca.monto_total) - sum(banca.comision)) + sum(banca.comision)) as total_debito
                    from obingresos.tboletos_banca banca
                    inner join obingresos.tagencia ag on ag.id_agencia = banca.agencia_id
                    inner join param.tlugar lu on lu.id_lugar = ag.id_lugar
                    inner join param.tmoneda mon on mon.id_moneda = ag.id_moneda_control
                    where banca.fecha_pago_banco between '''||v_parametros.fecha_ini::date||''' and '''||v_parametros.fecha_fin::date||''' and '||v_parametros.filtro||' ';

        v_consulta := v_consulta || '
        GROUP BY
                              banca.agencia_id,
                              ag.nombre,
                              ag.codigo_int,
                              ag.tipo_agencia,
                              lu.codigo,
                              mon.codigo_internacional
                    order by ag.nombre asc';

        return v_consulta;
		end;
    /*********************************
     #TRANSACCION:  'OBING_BOLEBANC_CONT'
     #DESCRIPCION:	Devuelve los totales y el conteo de datos
     #AUTOR:		Ismael Valdivia
     #FECHA:		03-01-2020
    ***********************************/

    elsif(p_transaccion='OBING_BOLEBANC_CONT')then

      begin
      	v_consulta = 'select
                           count (distinct banca.agencia_id),
                           (sum(banca.monto_total) - sum(banca.comision)),
                           sum(banca.comision),
                           ((sum(banca.monto_total) - sum(banca.comision)) + sum(banca.comision))
                    from obingresos.tboletos_banca banca
                    inner join obingresos.tagencia ag on ag.id_agencia = banca.agencia_id
                    inner join param.tlugar lu on lu.id_lugar = ag.id_lugar
                    inner join param.tmoneda mon on mon.id_moneda = ag.id_moneda_control
                    where banca.fecha_pago_banco between '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin||''' and '||v_parametros.filtro||' ';

        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_BOLEDETBAN_SEL'
     #DESCRIPCION:	Lista de datos boletos banca electronica detallado
     #AUTOR:		Ismael Valdivia
     #FECHA:		06-01-2020
    ***********************************/
    elsif(p_transaccion='OBING_BOLEDETBAN_SEL')then

        begin

        v_consulta ='select
                        ag.id_agencia,
                        ag.nombre,
                        ag.codigo_int,
                        banca.transaccion_id,
                        banca.pnr,
                        banca.tkt,
                        banca.neto,
                        banca.tasas,
                        banca.monto_total,
                        banca.comision,
                        banca.moneda,
                        banca.fecha_emision,
                        banca.fecha_transaccion,
                        banca.fecha_pago_banco,
                        banca.forma_pago,
                        banca.entidad_pago,
                        banca.estado
                    from obingresos.tboletos_banca banca
                    inner join obingresos.tagencia ag on ag.id_agencia = banca.agencia_id
                    inner join param.tlugar lu on lu.id_lugar = ag.id_lugar
                    inner join param.tmoneda mon on mon.id_moneda = ag.id_moneda_control
                    where banca.agencia_id = '||v_parametros.id_agencia||' and banca.fecha_pago_banco between '''||v_parametros.fecha_ini::date||''' and '''||v_parametros.fecha_fin::date||'''
                    ORDER BY banca.transaccion_id ASC';


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

ALTER FUNCTION obingresos.ft_reporte_banca_boletos_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
