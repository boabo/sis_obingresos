CREATE OR REPLACE FUNCTION obingresos.ft_reportes_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_reportes_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'vef.tpunto_venta'
 AUTOR: 		 (franklin.espinoza)
 FECHA:	        11-04-2020 15:14:58
 COMENTARIOS:
 ***************************************************************************/

  DECLARE

	v_consulta    		varchar;
    v_parametros  		record;
    v_nombre_funcion   	text;
    v_resp				varchar;

  BEGIN

    v_nombre_funcion = 'vef.ft_reportes_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
       #TRANSACCION:  'OBING_PUVE_CT_SEL'
     #DESCRIPCION:	Consulta de datos
     #AUTOR:		franklin.espinoza
     #FECHA:		11-04-2020 15:14:58
    ***********************************/

    if(p_transaccion='OBING_PUVE_CT_SEL')then

      begin
        --Sentencia de la consulta
        v_consulta:='select
						puve.id_punto_venta,
						puve.estado_reg,
						puve.id_sucursal,
						puve.nombre,
						puve.descripcion,
						puve.id_usuario_reg,
						puve.fecha_reg,
						puve.id_usuario_ai,
						puve.usuario_ai,
						puve.id_usuario_mod,
						puve.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
            puve.codigo,
            puve.habilitar_comisiones,
            suc.formato_comprobante,
            puve.tipo,
            tag.codigo_int as office_id,
            lug.nombre as lugar
						from vef.tpunto_venta puve
						inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
				    inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
            left join obingresos.tagencia tag on tag.codigo = puve.codigo
            left join param.tlugar lug on lug.id_lugar = tag.id_lugar
            where  ';

        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
        raise notice '%',v_consulta;
        --Devuelve la respuesta
        return v_consulta;
      end;
    /*********************************
     #TRANSACCION:  'OBING_PUVE_CT_CONT'
     #DESCRIPCION:	Conteo de registros
     #AUTOR:		franklin.espinoza
     #FECHA:		11-04-2020 15:14:58
    ***********************************/
    elsif(p_transaccion='OBING_PUVE_CT_CONT')then

      begin
        --Sentencia de la consulta de conteo de registros
        v_consulta:='select count(id_punto_venta)
					    from vef.tpunto_venta puve
					    inner join segu.tusuario usu1 on usu1.id_usuario = puve.id_usuario_reg
						  left join segu.tusuario usu2 on usu2.id_usuario = puve.id_usuario_mod
					    inner join vef.tsucursal suc on suc.id_sucursal = puve.id_sucursal
              left join obingresos.tagencia tag on tag.codigo = puve.codigo
              left join param.tlugar lug on lug.id_lugar = tag.id_lugar
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