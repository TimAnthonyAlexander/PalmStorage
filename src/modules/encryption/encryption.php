<?php
namespace PalmStorage;
use Exception;

class encryption{
    public function encrypt($input): String{
        $input = base64_encode(base64_encode($input));
        $input = str_replace("=", (new self)->rotn("[", (new self)->getn((new self)->serverkey())), $input);
        return (new self)->rotn(strrev(str_split($input, strlen($input)/2)[1]).str_split($input, strlen($input)/2)[0], -(new self)->serverkey());
    }
    public function decrypt($input): String{
        $input = (new self)->rotn($input, (new self)->serverkey());
        $input = str_split($input, strlen($input)/2)[1].strrev(str_split($input, strlen($input)/2)[0]);
        $input = str_replace((new self)->rotn("[", (new self)->getn((new self)->serverkey())), "=", $input);
        return base64_decode(base64_decode($input));
    }
    private function getn($input): int{
        $serverkey = (new self)->serverkey();
        $key = md5($input);
        return strlen($input.$key)*$serverkey;
    }
    public function rotn($string, $n): String{
        $alphabet = implode(array_reverse(range('a', 'z')));
        $alen = strlen($alphabet);
        $n %= $alen;
        if($n < 0) {$n += $alen;}
        if($n === 0) {return $string;}
        $cycled = substr($alphabet . $alphabet, $n, $alen);
        $alphabet .= strtoupper($alphabet);
        $cycled .= strtoupper($cycled);
        return strtr($string, $alphabet, $cycled);
    }
    public function serverkey(): int{
        static $server_key;
        if(!isset($server_key)){
            $dir = "..";
            if(!file_exists($dir."/server.key")){
                $newserver_key = time() * random_int(1, 9999999);
                $randomlength = random_int(1, 9);
                var_dump($randomlength);
                var_dump($newserver_key);
                $newserver_key = (int)substr("$newserver_key", -$randomlength);
                var_dump($newserver_key);
                $keyfile = fopen($dir."/server.key", "wb");
                fwrite($keyfile, $newserver_key);
                fclose($keyfile);
            }
            $server_key = (int)file_get_contents($dir."/server.key");
        }
        return $server_key;
    }
}
