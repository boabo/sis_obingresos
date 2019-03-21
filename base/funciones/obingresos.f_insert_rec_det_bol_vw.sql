CREATE OR REPLACE FUNCTION obingresos.f_insert_rec_det_bol_vw (
  p_asunto text,
  p_body text,
  p_correos varchar,
  p_fecha date
)
RETURNS integer AS
$body$
  /**************************************************************************
   FUNCION: obingresos.f_insert_rec_det_bol_vw
   DESCRIPCIÓN: Inserta record en la tabla obingresos.tdetalle_diario_error_vw
   AUTOR:         BOA(franklin.espinoza)
   FECHA:
   COMENTARIOS:

  ***********************************************************************/

  DECLARE
    v_resp      record;
    v_respuesta varchar;
    v_nombre_funcion   text;
    v_mensaje_error    text;
	v_id_detalle_diario_error_vw integer;
  BEGIN
    v_nombre_funcion='obingresos.f_insert_rec_det_bol_vw';
   	insert into obingresos.tdetalle_diario_error_vw(
                asunto,
                desc_error,
                correos,
                fecha_venta
            )values(
                p_asunto,
                p_body,
                p_correos,
               	p_fecha
        )RETURNING id_detalle_diario_error_vw into v_id_detalle_diario_error_vw;

    return v_id_detalle_diario_error_vw;

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
RETURNS NULL ON NULL INPUT
SECURITY DEFINER
COST 100;