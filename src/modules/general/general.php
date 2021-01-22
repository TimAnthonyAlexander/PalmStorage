<?php
namespace PalmStorage;


class general{
    public function dbexists(String $database){
        //
    }
    public function listdb(): array{
        $databaselist = (new read)->readdb("databases");
        $reallist = [];
        foreach($databaselist as $key => $val){
            $reallist[] = $val["databasename"];
        }
        return $reallist;
    }
}
