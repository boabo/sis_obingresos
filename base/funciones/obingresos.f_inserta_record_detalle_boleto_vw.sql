CREATE OR REPLACE FUNCTION obingresos.f_inserta_record_detalle_boleto_vw (
  p_asunto text,
  p_body text,
  p_correos varchar,
  p_fecha date
)
RETURNS varchar AS
$body$
  /**************************************************************************
   FUNCION: obingresos.f_inserta_record_detalle_boleto_vw
   DESCRIPCIÓN: Inserta record en la tabla obingresos.tdetalle_diario_error_vw
   AUTOR:         BOA(franklin.espinoza)
   FECHA:
   COMENTARIOS:

  ***********************************************************************/

  DECLARE
    v_resp      record;
    v_res_cone  varchar;
    v_database  varchar;
    v_respuesta varchar;
    v_nombre_funcion   text;
    v_mensaje_error    text;
    v_usr_bd 		varchar;
    v_query			varchar;
  BEGIN
    v_nombre_funcion='obingresos.f_inserta_record_detalle_boleto_vw';
    v_database=current_database();
    v_usr_bd=v_database||'_conexion';

    v_res_cone=(select dblink_connect('user=' || v_usr_bd ||' dbname='||v_database));

    v_query = 'select * from obingresos.f_insert_rec_det_bol_vw(
                '''||p_asunto||''',
                '''||p_body||''',
                '''||p_correos||''',
               	'''||p_fecha||'''::date
    )';


    SELECT * FROM
      dblink(
          v_query,true)
        AS t1(id_detalle_diario_error_vw integer)
    into v_resp;

    v_res_cone=(select dblink_disconnect());

    return 'exito';

    EXCEPTION

    WHEN OTHERS THEN

      v_respuesta = '';
      v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje',SQLERRM);
      v_respuesta = pxp.f_agrega_clave(v_respuesta,'codigo_error',SQLSTATE);
      v_respuesta = pxp.f_agrega_clave(v_respuesta,'tipo_respuesta','ERROR'::varchar);
      v_respuesta = pxp.f_agrega_clave(v_respuesta,'procedimientos',v_nombre_funcion);

      --RCM 31/01/2012: Cuando la llamada a esta funcion devuelve error, el manejador de excepciones de esa función da el resultado,
      --por lo que se modifica para que devuelva un json direcamente
      raise exception '%',pxp.f_resp_to_json(v_respuesta);

  END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
RETURNS NULL ON NULL INPUT
SECURITY DEFINER
COST 100;