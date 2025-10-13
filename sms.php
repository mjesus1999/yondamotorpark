<?php
// $token = "ODc2NTc4NTYyNjpTWUMxOFBGOTVQTDI=";
// $autorization = "Authorization: Bearer ".$token;
// $fields_string = "";
// $smsnumber = "919482381";
// $smstext = "Hola mundo";
// $smstype = "1"; // 0: remitente largo, 1: remitente corto
// $shorturl = "0"; // acortador URL

// //Preparamos las variables que queremos enviar
// $url = 'https://api3.gamanet.pe/token/smssend';

// $fields = array(
//                         'smsnumber'=>urlencode($smsnumber),
//                         'smstext'=>urlencode($smstext),
//                         'smstype'=>urlencode($smstype),
//                         'shorturl'=>urlencode($shorturl)
//                 );

// //Preparamos el string para hacer POST (formato querystring)
// foreach($fields as $key=>$value) { 
//        $fields_string .= $key.'='.$value.'&'; 
// }
// $fields_string = rtrim($fields_string,'&');


// //abrimos la conexion
// $ch = curl_init();

// //configuramos la URL, numero de variables POST y los datos POST
// curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded', $autorization));
// curl_setopt($ch,CURLOPT_URL,$url);
// curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); //Descomentarlo si usa HTTPS
// curl_setopt($ch,CURLOPT_POST,count($fields));
// curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
// curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

// //ejecutamos POST
// $result = curl_exec($ch);

// //cerramos la conexion
// curl_close($ch);

// //Resultado
// $array = json_decode($result,true);

// echo "error:".$array["message"];
// echo "uniqueid:".$array["uniqueid"];          


          
          
          