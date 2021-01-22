<?php
namespace PalmStorage;
use Exception;

class write{
    public function insert(String $database, String $statement): bool{
        if(!file_exists("../storage/db")){
            if(!file_exists("../storage/")){
                mkdir("../storage");
            }
            mkdir("../storage/db");
        }
        $statement_parts = explode("||", $statement);
        [$identifier, $cols, $vals] = $statement_parts;
        $cols_parts = explode("°", $cols);
        $vals_parts = explode("°", $vals);
        try{
            if(file_exists("../storage/db/$database.pst")){
                $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
            }else{
                $nowinformation = json_decode("{}", true, 512, JSON_THROW_ON_ERROR);
            }
            $informationarray = $nowinformation;
        }catch(\JsonException $e){
            $informationjson = "{}";
            $nowinformation = "{}";
        }


        $newident = 1;

        foreach($nowinformation as $key => $val){
            $newident = $key+1;
        }



        foreach($cols_parts as $key => $col){
            if($identifier === ""){
                $identifier = $newident;
            }
            $informationarray[$identifier][$col] = $vals_parts[$key];
        }


        ksort($informationarray);


        try{
            $informationjson = json_encode($informationarray, JSON_THROW_ON_ERROR || JSON_UNESCAPED_UNICODE || JSON_PRETTY_PRINT);
        }catch(\JsonException $e){
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
        }catch(\JsonException $e){
            $nowinformation = "{}";
        }
        unset($nowinformation[$identifier]);

        $informationarray = $nowinformation;

        ksort($informationarray);

        try{
            $informationjson = json_encode($informationarray, JSON_THROW_ON_ERROR || JSON_UNESCAPED_UNICODE || JSON_PRETTY_PRINT);
        }catch(\JsonException $e){
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
}
