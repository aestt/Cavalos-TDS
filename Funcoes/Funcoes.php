<?php

    class Funcoes{

        public function base64 ($vlr , $tipo){

           switch($tipo){
                case 1: $rst = base64_encode($vlr);
                    break;
                case 2: $rst = base64_decode($vlr);
                    break;
           }

           return $rst;

        }


    }