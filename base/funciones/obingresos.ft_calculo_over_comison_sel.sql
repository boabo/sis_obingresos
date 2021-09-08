CREATE OR REPLACE FUNCTION obingresos.ft_calculo_over_comison_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_calculo_over_comison_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.ft_calculo_over_comison_sel'
 AUTOR: 		(franklin.espinoza)
 FECHA:	        31-07-2021 15:14:58
 COMENTARIOS:
 ***************************************************************************/

  DECLARE

	v_consulta    		varchar;
    v_parametros  		record;
    v_nombre_funcion   	text;
    v_resp				varchar;


  BEGIN

    v_nombre_funcion = 'obingresos.ft_calculo_over_comison_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'OBING_AGE_EXC_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		franklin.espinoza
     #FECHA:		31-07-2021 15:14:58
    ***********************************/

    if(p_transaccion='OBING_AGE_EXC_SEL')then

      begin
      	--Sentencia de la consulta
        v_consulta:='select
						tae.id_acm_key,
                    	tae.iata_code,
                        tae.office_id,
                        tae.fecha_desde,
                        tae.fecha_hasta,
                        tae.observacion
						from obingresos.tagencia_excluida tae
            			where  tae.fecha_desde='''||v_parametros.fecha_desde||'''::date and tae.fecha_hasta='''||v_parametros.fecha_hasta||'''::date';

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