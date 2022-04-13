<?php
/**
 *@package pXP
 *@file    SolModPresupuesto.php
 *@author  Rensi Arteaga Copari
 *@date    30-01-2014
 *@description permites subir archivos a la tabla de documento_sol
 */
header("content-type: text/css; charset=UTF-8");
?>

<style type="text/css" rel="stylesheet">

    .prioridad_importanteA{

        background-color: #EAA8A8;//#ffe2e2
    color: #900;
    }

    .prioridad_importanteA:hover{
      background-color : #FFC57F;
    }

    .cero{
        background-color: #FFC57F;
        color: #FFC57F;
        font-weight: bold;
    }

    .table_mco {
        background: #fff;
        width: 1000px;
        height: auto;
        display: block;
        float: left;
        position: relative;
        left: 1%;
        margin: 5px;
        border: 4px solid green;
        border-radius: 8px;
        overflow: hidden;
        -moz-box-shadow: 0px 0px 0px rgba(0, 0, 0, 0);
        -webkit-box-shadow: 0px 0px 0px rgba(0, 0, 0, 0);
        box-shadow: 0px 0px 0px rgba(0, 0, 0, 0);
        -webkit-transform: translateY(0%);
        transform: translateY(0%);
        -webkit-transition: .3s;
        transition: .3s;
      }

      .t-interno-mco tr td{
      font-size: 12px;
      }
      .t-interno-mco tr td span{
        color:#274d80;
        font-weight: bold;
      }
      th {
        font-weight: bold;
        text-align: center;
      }
      caption {
        letter-spacing: 10px;
        font-size: 25px;
        text-align: center;
        color: green;
      }
      .tkt_footer{
        text-align: center;
        color:#274d80;
      }
      .tkt_footer td{
        font-size: 12px;
        font-weight: bold;
      }
</style>
