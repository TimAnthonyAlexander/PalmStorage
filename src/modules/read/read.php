<?php
namespace PalmStorage;


class read{
    public function readvals(String $database, String $statement): array{
        $statement_parts = explode("|", $statement);
        [$identifier] = $statement_parts;
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(\JsonException $e){
            $nowinformation = [];
        }
        foreach($nowinformation as $key => $val){
            if($key == $identifier){
                return $val;
            }
        }
        return [];
    }
    public function readval(String $database, String $statement){
        $statement_parts = explode("|", $statement);
        [$col, $identifier] = $statement_parts;
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(\JsonException $e){
            $nowinformation = [];
        }
        foreach($nowinformation as $key => $val){
            foreach($val as $newkey => $newval){
                if($key == $identifier && $newkey == $col){
                    return $newval;
                }
            }
        }
        return null;
    }
    public function readdb(String $database){
        try{
            $nowinformation = json_decode(file_get_contents("../storage/db/$database.pst"), true, 512, JSON_THROW_ON_ERROR);
        }catch(\JsonException $e){
            $nowinformation = [];
        }
        return $nowinformation;
    }

}
