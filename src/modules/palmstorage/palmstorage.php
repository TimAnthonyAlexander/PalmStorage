<?php
namespace PalmStorage;
use JsonException;

// Should all values be encrypted before writing and decrypted when reading?
// This creates a server key specific to its creation
// Your data will only be readable with that exact server key
// This is highly recommended
$GLOBALS["encryption_enabled"] = false; // Only if enabled, encrypted databases will work. The same vice versa


class palmstorage{
    public function insert(String $database, String $statement): bool{
        if(!file_exists("../storage/db/")){
            if(!file_exists("../storage/")){
                mkdir("../storage");
            }
            mkdir("../storage/db");
        }
        $statement_parts = explode("||", $statement);
        [$identifier, $cols, $vals] = $statement_parts;
        $cols_parts = explode("|", $cols);
        $vals_parts = explode("|", $vals);
        try{
            if(file_exists("../storage/db/$database.pst")){
                $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
            }else{
                $nowinformation = json_decode("{}", true, 512, JSON_THROW_ON_ERROR);
            }
            $informationarray = $nowinformation;
        }catch(JsonException $e){
            $nowinformation = "{}";
        }
        $newident = 1;
        foreach($nowinformation as $key => $val){
            $newident = $key+1;
        }
        foreach($cols_parts as $key => $col){
            if($GLOBALS["encryption_enabled"] === true){
                $col = (new encryption)->encrypt($col);
            }
            if($identifier === ""){
                $identifier = $newident;
            }
            if($GLOBALS["encryption_enabled"] === true){
                $informationarray[$identifier][$col] = (new encryption)->encrypt($vals_parts[$key]);
            }else{
                $informationarray[$identifier][$col] = $vals_parts[$key];
            }
        }
        ksort($informationarray);
        try{
            $informationjson = json_encode($informationarray, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }catch(JsonException $e){
            $informationjson = "{}";
        }
        if( $database !== "databases" && !file_exists("../storage/db/$database.pst")){
            (new self)->insert("databases", "||databasename||$database");
        }
        $db = fopen("../storage/db/$database.pst", 'wb');
        fwrite($db, $informationjson."\n");
        fclose($db);
        return true;
    }
    public function remove($database, String $statement): bool{
        $statement_parts = explode("||", $statement);
        [$identifier] = $statement_parts;
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(JsonException $e){
            $nowinformation = "{}";
        }
        unset($nowinformation[$identifier]);
        $informationarray = $nowinformation;
        ksort($informationarray);
        try{
            $informationjson = json_encode($informationarray, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }catch(JsonException $e){
            $informationjson = "{}";
        }
        if( $database !== "databases" && !file_exists("../storage/db/$database.pst")){
            (new self)->insert("databases", "||databasename||$database");
        }
        $db = fopen("../storage/db/$database.pst", 'wb');
        fwrite($db, $informationjson."\n");
        fclose($db);
        return true;
    }
    public function readvals(String $database, String $statement): array{
        $statement_parts = explode("||", $statement);
        [$identifier] = $statement_parts;
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(JsonException $e){
            $nowinformation = [];
        }
        foreach($nowinformation as $key => $val){
            if($GLOBALS["encryption_enabled"] === true){
                if((new encryption)->encrypt($key) === (new encryption)->encrypt($identifier)){
                    $return = $val;
                    break;
                }
            }else{
                if($key === $identifier){
                    $return = $val;
                    break;
                }
            }
        }
        if($return !== null){
            $realreturn = [];
            foreach($val as $value){
                if($GLOBALS["encryption_enabled"] === true){
                    $realreturn[] = (new encryption)->decrypt($value);
                }else{
                    $realreturn[] = $value;
                }
            }
            return $realreturn;
        }
        return [];
    }
    public function readval(String $database, String $statement){
        $statement_parts = explode("||", $statement);
        [$col, $identifier] = $statement_parts;
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(JsonException $e){
            $nowinformation = [];
        }
        foreach($nowinformation as $key => $val){
            if($GLOBALS["encryption_enabled"] === true){
                foreach($val as $newkey => $newval){
                    if($newkey === (new encryption)->encrypt($col) && (new encryption)->encrypt($key) === (new encryption)->encrypt($identifier)){
                        return (new encryption)->decrypt($newval);
                    }
                }
            }else{
                foreach($val as $newkey => $newval){
                    if($newkey === $col && $key === $identifier){
                        return $newval;
                    }
                }
            }
        }
        return null;
    }
    public function search($database, $statement){
        $statement_parts = explode("||", $statement);
        if(count($statement_parts) === 3){
            $alls = false;
            if($statement_parts[0] === "*"){
                $alls = true;
            }
            [$col, $identcol, $identifier] = $statement_parts;
        }else{
            [$identcol, $identifier] = $statement_parts;
            $alls = true;
        }
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(\JsonException $e){
            $nowinformation = [];
        }

        foreach($nowinformation as $key => $val){
            if($GLOBALS["encryption_enabled"] === true){
                foreach($val as $subkey => $subval){
                    $subkey = (new encryption)->decrypt($subkey);
                    $subval = (new encryption)->decrypt($subval);
                    if($alls === false){
                        $cryptcol = (new encryption)->encrypt($col);
                        if($subkey == $identcol && $subval == $identifier){
                            return (new encryption)->decrypt($val[$cryptcol]);
                        }
                    }else{
                        if($subkey == $identcol && $subval == $identifier){
                            return $val;
                        }
                    }
                }
            }else{
                foreach($val as $subkey => $subval){
                    if($alls === false){
                        if($subkey == $identcol && $subval == $identifier){
                            return $val[$col];
                        }
                    }else{
                        if($subkey == $identcol && $subval == $identifier){
                            return $val;
                        }
                    }
                }
            }
        }
        return "";
    }
    public function readdb(String $database){
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(JsonException $e){
            $nowinformation = [];
        }
        return $nowinformation;
    }

}
