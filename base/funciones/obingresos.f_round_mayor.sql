CREATE OR REPLACE FUNCTION obingresos.f_round_mayor (
  p_monto numeric
)
RETURNS numeric AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_round_mayor
 DESCRIPCION:   redondea al mayor 
 AUTOR: 	    RCM
 FECHA:			06/09/2013
 COMENTARIOS:	
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:		
 FECHA:		
 ***************************************************************************/
DECLARE

    v_resp                      varchar;
    v_nombre_funcion            text;
    v_mensaje_error             text;
   

BEGIN
    v_nombre_funcion:='obingresos.f_round_mayor';
    return ceil(p_monto*100)/100;

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