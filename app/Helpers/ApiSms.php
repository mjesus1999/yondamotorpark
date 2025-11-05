<?php
//app/Helpers/ApiSms.php

class ApiSms
{
    private $token;
    private $url;
    private $autorization;
    private $smstype;
    private $shorturl;


    public function __construct()
    {
        $this->token = "ODc2NTc4NTYyNjpTWUMxOFBGOTVQTDI=";
        $this->autorization = "Authorization: Bearer " . $this->token;
        $this->smstype = "1";
        $this->shorturl = "0";
        $this->url = "https://api3.gamanet.pe/token/smssend";
    }

    public function sendMessage(string $phone, string $message): bool
    {


        $fields = [
            'smsnumber' => $phone,
            'smstext' => $message,
            'smstype' => $this->smstype,
            'shorturl' => $this->shorturl
        ];
        $fields_string = http_build_query($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            $this->autorization
        ]);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($result === false) {

            return false;
        }

        $array = json_decode($result, true);
        if (!is_array($array)) {

            return false;
        }


        if (isset($array['message']) && $array['message'] === "0") {
            return true;
        }

        return false;
    }


}



// $objApiSms = new ApiSms();
// if ($objApiSms->sendMessage('919482381', 'Hola Josue, ¿Cómo estás?')) {
//     echo "SMS enviado correctamente\n";
// } else {
//     echo "Error al enviar SMS\n";
// }

