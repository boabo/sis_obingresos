CREATE OR REPLACE FUNCTION obingresos.ft_deposito_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_deposito_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tdeposito'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:28
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
    v_deposito			record;
	v_fecha_in				date;
    v_fecha_fi				date;
    v_id_deposito			integer;

BEGIN

	v_nombre_funcion = 'obingresos.ft_deposito_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_DEP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	if(p_transaccion='OBING_DEP_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						dep.id_deposito,
						dep.estado_reg,
						dep.nro_deposito,
						dep.monto_deposito,
						dep.id_moneda_deposito,
						dep.id_agencia,
						dep.fecha,
						dep.saldo,
						dep.id_usuario_reg,
						dep.fecha_reg,
						dep.id_usuario_ai,
						dep.usuario_ai,
						dep.id_usuario_mod,
						dep.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						mon.codigo_internacional as desc_moneda
						from obingresos.tdeposito dep
						inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

  	/*********************************
 	#TRANSACCION:  'OBING_DEREP_SEL'
 	#DESCRIPCION:	Reporte Deposito
 	#AUTOR:		mam
 	#FECHA:		23-11-2016 22:42:28
	***********************************/

    ELSIF(p_transaccion= 'OBING_DEREP_SEL')then
		begin

 CREATE TEMP TABLE tmp_deposito (
    							id_deposito INTEGER,
                                nro_deposito VARCHAR(70),
                                pnr VARCHAR(20),
                                monto_deposito NUMERIC(18,2),
                              	moneda VARCHAR(5),
                                total NUMERIC(18,2),
                               	nro_boleto VARCHAR(50),
                                CONSTRAINT tmp_deposito_pkey PRIMARY KEY(id_deposito)
               				 	)ON COMMIT DROP;
			RAISE NOTICE 'CICLO';
    					FOR v_deposito
           						 IN
                          			(SELECT d.nro_deposito,
                                    		d.monto_deposito,
                                            d.id_moneda_deposito,
					                        d.moneda,
                                            d.pnr,
                                            sum(b.total) as total_boletos,
                                            pxp.list(b.nro_boleto) as nro_boletos

                                    FROM obingresos.tdeposito d
                                    --INNER JOIN param.tmoneda m on m.id_moneda = d.id_moneda_deposito
                          	     	LEFT JOIN  obingresos.tboleto b on b.localizador = d.pnr
                                    WHERE  (v_parametros.fecha_ini - CAST('5 days' AS INTERVAL))>=b.fecha_emision and
                                    b.fecha_emision  <=(v_parametros.fecha_fin + CAST('5 days' AS INTERVAL))
				    				group by

                                 			d.nro_deposito,
                                    		d.monto_deposito,
                                            d.id_moneda_deposito,
					    					d.moneda,
                                            d.pnr)
                       			 	LOOP

                                  IF v_deposito.monto_deposito !=  v_deposito.total_boletos or v_deposito.nro_boletos is null THEN
                                  insert into tmp_deposito(	nro_deposito,
                                                            pnr,
                                                            monto_deposito,
                                                            moneda,
                                                            total,
                                                            nro_boleto
                                                            )values(
                                                            v_deposito.nro_deposito,
                                                            v_deposito.pnr,
                                                            v_deposito.monto_deposito,
                                                            v_deposito.moneda,
                                                            v_deposito.total_boletos,
                                                            v_deposito.nro_boleto
                                                            )RETURNING id_deposito into v_id_deposito;
                                  END IF;

                            END LOOP;

             --Sentencia de la consulta
				v_consulta:='	select
            					d.nro_deposito,
                                d.pnr,
                                d.monto_deposito,
                                d.moneda,
                                d.total_boletos,
                                d.nro_boleto,
                                d.nro_boleto
            					from tmp_deposito d
							 	where d.fecha_emision >= '''||v_parametros.fecha_ini||'''and d.fecha_emision <= '''||v_parametros.fecha_fin||'''  ';
		 --Devuelve la respuesta
			return v_consulta;

    end;

	/*********************************
 	#TRANSACCION:  'OBING_DEP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:28
	***********************************/

	elsif(p_transaccion='OBING_DEP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_deposito)
					    from obingresos.tdeposito dep
					    inner join segu.tusuario usu1 on usu1.id_usuario = dep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = dep.id_usuario_mod
						inner join param.tmoneda mon on mon.id_moneda = dep.id_moneda_deposito
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