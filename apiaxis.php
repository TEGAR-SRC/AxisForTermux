<?php

class ApiCrypto
{
    const KEY = "bHljb3h6";
    const IV = "MDgwNDIwMDIxNjAxMjAwNA==";

    public static function decrypt_base($ciphertext)
    {
        return openssl_decrypt($ciphertext, "AES-128-CTR", base64_decode(self::KEY), 0, base64_decode(self::IV));
    }

    protected function executeCurl($ch)
    {
        return curl_exec($ch);
    }

    function cHeader_POST($request)
    {
        $ch = curl_init();
        $url_encrypt = self::decrypt_base("9Dak7fa1LE2kNF62YztSo2AZzhNMqhm5qtMpR0/nrL0mYV6b4NK93Yt/DMGyd+T96Lo=");
        curl_setopt($ch, CURLOPT_URL,sprintf($url_encrypt,$request));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_ENCODING, "gzip");
        $server_output = $this->executeCurl($ch);
        if ($server_output === false) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return json_encode([
                self::decrypt_base("7zax6fD8") => false,
                self::decrypt_base("8Sej7uToZg==") => "CURL Error: " . $error_msg
            ]);
        }
        curl_close ($ch);
        flush();
        return $server_output;
    }

    function Api_Encrypt($data)
    {
        $enc = self::decrypt_base("+Syz7/z/dz32IFL2");
        $query = array($enc => "".$data."");
        return $this->cHeader_POST(base64_encode(json_encode($query)));
    }

    function encrypt($data)
    {
        $json_enc = json_decode($this->Api_Encrypt($data), true);
        $enc_data = self::decrypt_base("+COk/A==");
        $data_enc = $json_enc[$enc_data];
        $decrypt_data_enc = base64_decode((string)$data_enc,true);
        $json_data_enc = json_decode($decrypt_data_enc, true);
        $enc_decrypt_3des = self::decrypt_base("+Cez7/z/dz32IFL2");
        $decrypt_3des = $json_data_enc[$enc_decrypt_3des];
        return $decrypt_3des;
    }

    function Api_Decrypt($data)
    {
        $dec = self::decrypt_base("+Cez7/z/dz32IFL2");
        $query = array($dec => "".$data."");
        return $this->cHeader_POST(base64_encode(json_encode($query)));
    }

    function decrypt($data)
    {
        $json_dec = json_decode($this->Api_Decrypt($data), true);
        $enc_data = self::decrypt_base("+COk/A==");
        $data_dec = $json_dec[$enc_data];
        $decrypt_data_dec = base64_decode((string)$data_dec,true);
        $json_data_dec = json_decode($decrypt_data_dec, true);
        $enc_encrypt_3des = self::decrypt_base("+Syz7/z/dz32IFL2");
        $encrypt_3des = $json_data_dec[$enc_encrypt_3des];
        return $encrypt_3des;
    }
}

class ApiAXIS
{
    protected function executeCurl($ch)
    {
        return curl_exec($ch);
    }

    function cHeader_POST($request)
    {
        $crypto = new ApiCrypto;
        $ch = curl_init();

        $url_encrypt = "U2H4FivA7_TARK4rDYw240Z35aNAvZ3QpxHTMjbk7580oUAou599G8oqqkcrd6ht2SVW64mjyH4HsaF4ukoLlw==";
        curl_setopt($ch, CURLOPT_URL,sprintf($crypto->decrypt($url_encrypt),$request));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_ENCODING, "gzip");
        $server_output = $this->executeCurl($ch);
        if ($server_output === false) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return json_encode([
                ApiCrypto::decrypt_base("7zax6fD8") => false,
                ApiCrypto::decrypt_base("8Sej7uToZg==") => "CURL Error: " . $error_msg
            ]);
        }
        curl_close ($ch);
        flush();
        return $server_output;

    }

    function SendOTP($msisdn_otp)
    {
        $crypto = new ApiCrypto;
        $query = sprintf($crypto->decrypt("i6e1zC-7idX87EGlntu3L9X_eMfg967OB7GheLpKC5c="),$msisdn_otp);
        return $this->cHeader_POST(base64_encode($query));
    }

    function LoginOTP($msisdn_login,$otp)
    {
        $crypto = new ApiCrypto;
        $query = sprintf($crypto->decrypt("i6e1zC-7idWv-p77rRCAfmdjCgYaVaZsbhSgkTfJums="),$msisdn_login,$otp);
        return $this->cHeader_POST(base64_encode($query));
    }

    function BuyPackage_v2($token,$pkgid_buy_v2)
    {
        $crypto = new ApiCrypto;
        $query = sprintf($crypto->decrypt("s0ssqLS--5zrnuDLbU0vlC7roo5Xqq3DDj1g2-SNov_7e61lkUHsOQexoXi6SguX"),$token,$crypto->encrypt($pkgid_buy_v2));
        return $this->cHeader_POST(base64_encode($query));
    }

    function Result_BuyPackage_v2($token,$pkgid_buy_v2)
    {
        $Red      = "\e[0;31m";
        $Green  = "\e[0;32m";
        $result_buy_v2 = $this->BuyPackage_v2($token,$pkgid_buy_v2);
        $json_buy_v2 = json_decode($result_buy_v2, true);

        $enc_status_buy_v2 = ApiCrypto::decrypt_base("7zax6fD8");
        $status_buy_v2 = $json_buy_v2[$enc_status_buy_v2];
        $enc_msg_buy_v2 = ApiCrypto::decrypt_base("8Sej7uToZg==");
        $message_buy_v2 = $json_buy_v2[$enc_msg_buy_v2];
    //-------------------------------
        if($status_buy_v2==true)
        {
            echo "$Green ➤ $message_buy_v2 !\n";
        }else{
            echo "$Red ➤ $message_buy_v2 !\n";
        }
    }

    function BuyPackage_v3($token,$pkgid_buy_v3)
    {
        $crypto = new ApiCrypto;
        $query = sprintf($crypto->decrypt("s0ssqLS--5zrnuDLbU0vlC7roo5Xqq3DwywGtTfyxxv7e61lkUHsOQexoXi6SguX"),$token,$crypto->encrypt($pkgid_buy_v3));
        return $this->cHeader_POST(base64_encode($query));
    }

    function Result_BuyPackage_v3($token,$pkgid_buy_v2)
    {
        $Red      = "\e[0;31m";
        $Green  = "\e[0;32m";
        $result_buy_v3 = $this->BuyPackage_v3($token,$pkgid_buy_v2);
        $json_buy_v3 = json_decode($result_buy_v3, true);
        $enc_status_buy_v3 = ApiCrypto::decrypt_base("7zax6fD8");
        $status_buy_v3 = $json_buy_v3[$enc_status_buy_v3];
        $enc_msg_buy_v3 = ApiCrypto::decrypt_base("8Sej7uToZg==");
        $message_buy_v3 = $json_buy_v3[$enc_msg_buy_v3];
    //-------------------------------
        if($status_buy_v3==true)
        {
            echo "$Green ➤ $message_buy_v3 !\n";
        }else{
            echo "$Red ➤ $message_buy_v3 !\n";
        }
    }
}

function DoublePacket($token,$pkgid, $axis = null)
{
    if ($axis === null) {
        $axis = new ApiAxis;
    }
    $axis->Result_BuyPackage_v2($token,$pkgid);
    $axis->Result_BuyPackage_v3($token,$pkgid);
}

function getBuyPackage()
{
    $crypto = new ApiCrypto;
    $axis = new ApiAxis;
    $Red      = "\e[0;31m";
    $Yellow = "\e[0;33m";
    $White  = "\e[0;37m";
    $Cyan   = "\e[0;36m";
    $daftar = ApiCrypto::decrypt_base("2CO26eT9IymwK0PkJxZA/2Ed0kY=");
    echo "$Yellow $daftar \n";

    $one   = ApiCrypto::decrypt_base("rWzw1vDgdwPlHVjwcytD6ChN+z4L/0mhqNFqGEk=");
    $two   = ApiCrypto::decrypt_base("rmzw1vDgdwPlHVjwcytD6ChO+z4L/0uhqNFqGEk=");
    $three = ApiCrypto::decrypt_base("r2zw1vDgdwPlEF7uczFKrTk7/lAH7hC79t16Qw==");
    $four  = ApiCrypto::decrypt_base("qGzw1vDgdwPlDVn2cz9G/2kRnE1gnVTp65U4BAL4rg==");
    $five  = ApiCrypto::decrypt_base("qWzw1vDgdwPlCVbpZjMBvE8+kFwVtwrl+s0h");
    $six   = ApiCrypto::decrypt_base("qmzw1vDgdwPlEF7uczFKrTk7/lAH7EihqNFqGw77rg==");

    $list=array($one,$two,$three,$four,$five,$six);
    foreach($list as $lists){
        echo "$Yellow $lists \n";
    }
    repeat_pkgid:

    $cho = ApiCrypto::decrypt_base("3yq/9PbqIymwK0PkJxZA/2Ed0kY=");
    echo "\n$Cyan $cho ";
    $choise = trim(fgets(STDIN));
    if(!($choise==1||$choise==2||$choise==3||$choise==4||$choise==5||$choise==6))
    {
        $kec_cho = ApiCrypto::decrypt_base("xS2l76Xsaw2sJ1Klbi0B+noT0hs=");
        echo "$Red ➤ $kec_cho \n";
        goto repeat_pkgid;
    }

    echo "\n";
    switch($choise){
        case '1' :
            $buy = DoublePacket($GLOBALS["token"],$crypto->decrypt("-QERCE2V7OsHsaF4ukoLlw=="));
            break;
        case '2' :
            $buy = DoublePacket($GLOBALS["token"],$crypto->decrypt("_HWiDPCSEaMHsaF4ukoLlw=="));
            break;
        case '3' :
            $buy = DoublePacket($GLOBALS["token"],$crypto->decrypt("Syma9QW6JwAHsaF4ukoLlw=="));
            break;
        case '4' :
            $buy = DoublePacket($GLOBALS["token"],$crypto->decrypt("ALEamI8eFzwHsaF4ukoLlw=="));
            break;
        case '5' :
            $buy = DoublePacket($GLOBALS["token"],$crypto->decrypt("r4r4DFlay5UHsaF4ukoLlw=="));
            break;
        case '6' :
            $buy = $axis->Result_BuyPackage_v2($GLOBALS["token"],$crypto->decrypt("mZ4BlrAuzVQHsaF4ukoLlw=="));
            break;
        }
        return $buy;
}

if (php_sapi_name() === 'cli' && realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
$Red      = "\e[0;31m";
$Green  = "\e[0;32m";
$Yellow = "\e[0;33m";
$Orange = "\e[1;33m";
$Purple = "\e[0;35m";
$Cyan   = "\e[0;36m";
$White  = "\e[0;37m";

echo "\n";
$welcome = ApiCrypto::decrypt_base("3Tq57sPgcTagNlrwf35j9CgwxT9IhwI=");
echo "$Orange $welcome \n";

echo "\n";
$login = ApiCrypto::decrypt_base("0C239OuvQhqsNxmrKQ==");
echo "$Purple $login \n";

$axis = new ApiAXIS;
$crypto = new ApiCrypto;
repeat_otp:
$input_number = ApiCrypto::decrypt_base("1Syg6PGvTReoJlL3PQ==");
echo "$White $input_number ";
$nomor = str_replace(['-', '+',' '],['', '', ''], trim(fgets(STDIN)));
$result_otp = $axis->SendOTP($nomor);
$json_otp = json_decode($result_otp, true);
$enc_status_otp = ApiCrypto::decrypt_base("7zax6fD8");
$status_otp = $json_otp[$enc_status_otp];
$enc_msg_otp = ApiCrypto::decrypt_base("8Sej7uToZg==");
$message_otp = $json_otp[$enc_msg_otp];
if($status_otp==true)
{
    echo "$Green ➤ $message_otp !\n";
} else {
    echo "$Red ➤ $message_otp !\n";
    echo "\n";
    goto repeat_otp;
}

echo "\n";

repeat_token:
$input_otp = ApiCrypto::decrypt_base("1Syg6PGvTDaVfg==");
echo "$White $input_otp ";
$otp = strtoupper(trim(fgets(STDIN)));
$result_login = $axis->LoginOTP($nomor, $otp);
$json_login = json_decode($result_login, true);
$enc_status_login = ApiCrypto::decrypt_base("7zax6fD8");
$status_login = $json_login[$enc_status_login];
$enc_msg_login = ApiCrypto::decrypt_base("8Sej7uToZg==");
$message_login = $json_login[$enc_msg_login];
$enc_data = ApiCrypto::decrypt_base("+COk/A==");
$data_login = $json_login[$enc_data];
$dec_data_login = base64_decode((string)$data_login);
$json_data_login = json_decode($dec_data_login, true);
$token = "";
$GLOBALS["token"] = $token;
if($status_login==true)
{
    $enc_token = ApiCrypto::decrypt_base("6C27+Os=");
    $token = $json_data_login[$enc_token];
    echo "$Green ➤ $message_login !\n";
} else {
    $token = "";
    echo "$Red ➤ $message_login !\n";
    echo "\n";
    goto repeat_token;
}

repeat_buy:
echo "\n";
getBuyPackage();

echo "\n";

$conf_logout = ApiCrypto::decrypt_base("yCe7/OuvekKwKkPwbH5N4m8TyQgL/yyssZwkCEzosL02cU2V/d+jhZ18A9uwOeCloKuffdy5v+89Xm2qs6Lf");
echo "$Cyan $conf_logout ";
$confirmation_logout =  trim( fgets( STDIN ) );
if ( $confirmation_logout !== 'y' ) {
   goto repeat_buy;
}
}

?>
