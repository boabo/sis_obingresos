CREATE OR REPLACE FUNCTION obingresos.ft_mco_s_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_mco_s_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tmco_s'
 AUTOR: 		 (breydi.vasquez)
 FECHA:	        28-04-2020 15:25:04
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				28-04-2020 15:25:04								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tmco_s'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
  v_fil				varchar;

BEGIN

	v_nombre_funcion = 'obingresos.ft_mco_s_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_IMCOS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-04-2020 15:25:04
	***********************************/

	if(p_transaccion='OBING_IMCOS_SEL')then

    	begin
      if p_administrador !=1 then
               if exists ( select 1
                from segu.tusuario usu
                inner join orga.tfuncionario f on f.id_persona = usu.id_persona
                inner join orga.vfuncionario_ultimo_cargo fun on fun.id_funcionario = f.id_funcionario
                inner join vef.tpermiso_sucursales ps on ps.id_funcionario = fun.id_funcionario
                where usu.id_usuario = p_id_usuario) then
                  v_fil = ' 0 = 0 and ';
               else
                 v_fil = 'imcos.id_usuario_reg = '||p_id_usuario||' and ';
               end if;
        else
          v_fil = ' 0 = 0 and ';
        end if;

    		--Sentencia de la consulta
			v_consulta:=' select
            			  imcos.id_mco,
                          imcos.estado_reg,
                          imcos.estado,
                          imcos.fecha_emision,
                          imcos.id_moneda,
                          imcos.motivo,
                          imcos.valor_total,
                          imcos.id_gestion,
                          imcos.id_usuario_reg,
                          imcos.fecha_reg,
                          imcos.id_usuario_ai,
                          imcos.usuario_ai,
                          imcos.id_usuario_mod,
                          imcos.fecha_mod,
                          usu1.cuenta as usr_reg,
                          usu2.cuenta as usr_mod,
                          conig.codigo,
                          conig.desc_ingas,
                          mon.codigo_internacional,
                          ges.gestion,
                          imcos.id_boleto,
                          imcos.nro_tkt_mco as tkt,
                          imcos.fecha_doc_orig as fecha_doc_or,
                          imcos.valor_total_doc_orig as val_total_doc_or,
                          imcos.moneda_doc_orig as moneda_doc_or,
                          (case when imcos.moneda_doc_orig = ''USD'' then
                          round((imcos.valor_total_doc_orig * imcos.t_c_doc_orig),2)
                          else
                          0.00
                          end)::numeric as val_conv_doc_or,
                          imcos.t_c_doc_orig as t_c_doc_or,
                          imcos.estacion_doc_orig as estacion_doc_or,
                          imcos.pais_doc_orig as pais_doc_or,
                          imcos.id_punto_venta,
                          vpv.codigo as agt_tv_head,
                          vpv.nombre as city_head ,
                          vsu.codigo as suc_head,
                          vsu.nombre as nombre_suc_head,
                          pl.codigo as estacion_head,
                          plf.nombre as pais_head,
                          mon.codigo as desc_moneda,
                          imcos.id_concepto_ingas,
                          imcos.tipo_cambio,
                          imcos.nro_mco,
                          imcos.pax,
                          imcos.id_funcionario_emisor,
                          fun.desc_funcionario1
                          from obingresos.tmco_s imcos
                          inner join segu.tusuario usu1 on usu1.id_usuario = imcos.id_usuario_reg
                          left join segu.tusuario usu2 on usu2.id_usuario = imcos.id_usuario_mod
                          inner join param.tconcepto_ingas conig  on conig.id_concepto_ingas = imcos.id_concepto_ingas
                          inner join param.tmoneda mon on mon.id_moneda =  imcos.id_moneda
                          inner join param.tgestion ges on ges.id_gestion = imcos.id_gestion
                          inner join vef.tpunto_venta vpv on vpv.id_punto_venta = imcos.id_punto_venta
                          inner join vef.tsucursal vsu on vsu.id_sucursal = vpv.id_sucursal
                          inner join param.tlugar pl on pl.id_lugar = vsu.id_lugar
                          inner join param.tlugar plf on plf.id_lugar = pl.id_lugar_fk
                          inner join orga.vfuncionario_cargo  fun on fun.id_funcionario = imcos.id_funcionario_emisor
                          and imcos.fecha_emision between fun.fecha_asignacion and coalesce(fun.fecha_finalizacion, now())
				        where   '||v_fil||' ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_IMCOS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		28-04-2020 15:25:04
	***********************************/

	elsif(p_transaccion='OBING_IMCOS_CONT')then

		begin
    if p_administrador !=1 then
             if exists ( select 1
              from segu.tusuario usu
              inner join orga.tfuncionario f on f.id_persona = usu.id_persona
              inner join orga.vfuncionario_ultimo_cargo fun on fun.id_funcionario = f.id_funcionario
              inner join vef.tpermiso_sucursales ps on ps.id_funcionario = fun.id_funcionario
              where usu.id_usuario = p_id_usuario) then

                v_fil = ' 0 = 0 and ';
             else
               v_fil = 'imcos.id_usuario_reg = '||p_id_usuario||' and ';
             end if;
      else
        v_fil = ' 0 = 0 and ';
      end if;
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_mco)
					    from obingresos.tmco_s imcos
                        inner join segu.tusuario usu1 on usu1.id_usuario = imcos.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = imcos.id_usuario_mod
                        inner join param.tconcepto_ingas conig  on conig.id_concepto_ingas = imcos.id_concepto_ingas
                        inner join param.tmoneda mon on mon.id_moneda =  imcos.id_moneda
                        inner join param.tgestion ges on ges.id_gestion = imcos.id_gestion
                        inner join vef.tpunto_venta vpv on vpv.id_punto_venta = imcos.id_punto_venta
                        inner join vef.tsucursal vsu on vsu.id_sucursal = vpv.id_sucursal
                        inner join param.tlugar pl on pl.id_lugar = vsu.id_lugar
                        inner join param.tlugar plf on plf.id_lugar = pl.id_lugar_fk
                        inner join orga.vfuncionario_cargo  fun on fun.id_funcionario = imcos.id_funcionario_emisor
                        and imcos.fecha_emision between fun.fecha_asignacion and coalesce(fun.fecha_finalizacion, now())
					    where '||v_fil||' ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_GETKTS_SEL'
 	#DESCRIPCION:	Consulta de tkts
 	#AUTOR:		breydi vasquez
 	#FECHA:		06-05-2020
	***********************************/

	elsif(p_transaccion='OBING_GETKTS_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='
                        select
                             bole.id_boleto,
                             bole.id_agencia,
                             bole.id_moneda_boleto,
                             bole.nro_boleto as tkt,
                             bole.moneda,
                             bole.total,
                             bole.fecha_emision,
                             lug.codigo as tkt_estac,
                             lugp.codigo as tkt_pais,
                             (case when bole.moneda = ''USD'' then
                               round((bole.total * tc.oficial),2)
                             else
                             0.00
                             end)::numeric as val_conv,
                             tc.oficial as tipo_cambio
                        from obingresos.tboleto bole
                        inner join obingresos.tagencia age on age.id_agencia = bole.id_agencia
                        inner join param.tlugar lug on lug.id_lugar = age.id_lugar
                        inner join param.tlugar lugp on lugp.id_lugar = lug.id_lugar_fk
                        inner join param.ttipo_cambio tc on tc.id_moneda = bole.id_moneda_boleto
						and tc.fecha = bole.fecha_emision
                        where bole.nro_boleto like '''||COALESCE(v_parametros.tkt,'-')||'%'' ';

            v_consulta:=v_consulta||'  limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_GETKTS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		breydi vasquez
 	#FECHA:		06-05-2020
	***********************************/

	elsif(p_transaccion='OBING_GETKTS_CONT')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select count(bole.id_boleto)
                        from obingresos.tboleto bole
                        inner join obingresos.tagencia age on age.id_agencia = bole.id_agencia
                        inner join param.tlugar lug on lug.id_lugar = age.id_lugar
                        inner join param.tlugar lugp on lugp.id_lugar = lug.id_lugar_fk
                        where bole.nro_boleto like '''||COALESCE(v_parametros.tkt,'-')||'%'' ';

			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'OBING_GETKTFIL_SEL'
 	#DESCRIPCION:	Consulta de tkt MCOs
 	#AUTOR:		breydi vasquez
 	#FECHA:		06-05-2020
	***********************************/

	elsif(p_transaccion='OBING_GETKTFIL_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:=' select
                          imcos.id_mco,
                          imcos.id_boleto,
                          imcos.nro_mco,
                          imcos.moneda_doc_orig as moneda,
						  imcos.valor_total_doc_orig as total
                          from obingresos.tmco_s imcos
                          -- inner join obingresos.tboleto bole on bole.id_boleto = imcos.id_boleto
                          where imcos.nro_mco like '''||COALESCE(v_parametros.nro_mco,'-')||'%''
                          and ';
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||'  limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'OBING_GETKTFIL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		breydi vasquez
 	#FECHA:		06-05-2020
	***********************************/

	elsif(p_transaccion='OBING_GETKTFIL_CONT')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select count(imcos.id_mco)
                          from obingresos.tmco_s imcos
                          inner join obingresos.tboleto bole on bole.id_boleto = imcos.id_boleto
                          where imcos.nro_mco like '''||COALESCE(v_parametros.nro_mco,'-')||'%'' and ';

			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_REPMCOS_SEL'
 	#DESCRIPCION:	Reporte MCOs
 	#AUTOR:		breydi vasquez
 	#FECHA:		06-05-2020
	***********************************/

	elsif(p_transaccion='OBING_REPMCOS_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='   select
                            imcos.id_mco,
                            imcos.fecha_emision,
                            conig.codigo as t_concepto,
                            imcos.nro_mco,
                            imcos.pax,
                            imcos.nro_tkt_mco as tkt,
                            fun.desc_funcionario2,
                            imcos.motivo,
                            imcos.valor_total,
                            initcap(usu1.desc_persona) as cajero,
                            mon.codigo_internacional as codi_moneda
                            from obingresos.tmco_s imcos
                            inner join segu.vusuario usu1 on usu1.id_usuario = imcos.id_usuario_reg
                            inner join param.tmoneda mon on mon.id_moneda = imcos.id_moneda
                            inner join param.tconcepto_ingas conig  on conig.id_concepto_ingas = imcos.id_concepto_ingas
                            inner join param.tgestion ges on ges.id_gestion = imcos.id_gestion
                            -- inner join obingresos.tboleto bole on bole.id_boleto = imcos.id_boleto
                            inner join orga.vfuncionario_cargo  fun on fun.id_funcionario = imcos.id_funcionario_emisor
                            and imcos.fecha_emision between fun.fecha_asignacion and coalesce(fun.fecha_finalizacion, now())
                            where  ';
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by imcos.fecha_reg, nro_mco asc ';
			raise notice '%', v_consulta;
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

ALTER FUNCTION obingresos.ft_mco_s_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
