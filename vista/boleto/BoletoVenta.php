<?php
/**
 *@package pXP
 *@file gen-SistemaDist.php
 *@author  (rarteaga)
 *@date 20-09-2011 10:22:05
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.BoletoVenta = {
        require:'../../../sis_obingresos/vista/boleto/Boleto.php',
        requireclase:'Phx.vista.Boleto',

        tabsouth:[
            {
                url:'../../../sis_obingresos/vista/boleto_forma_pago/BoletoFormaPago.php',
                title:'Formas de Pago',
                height:'40%',
                cls:'BoletoFormaPago'
            },
            {
                url:'../../../sis_obingresos/vista/boleto_impuesto/BoletoImpuesto.php',
                title:'Impuestos - Tasas',
                height:'40%',
                cls:'BoletoImpuesto'
            }

        ]

    };
</script>
