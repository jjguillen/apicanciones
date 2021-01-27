<?php

namespace Songs;
use MongoDB\Client;
use Songs\ConexionDB;
use Songs\Message;

class SongsDB {

    public static function getAll() {
        try {
            $conexion = ConexionDB::conectar("Songs");
            $cursor = $conexion->Songs->find();
            $result = json_encode($cursor->toArray());
        } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
        }
        $conexion = null;
        return $result;
    }

    public static function getOne($id) {
        try {
            $conexion = ConexionDB::conectar("Songs");
            $song = $conexion->Songs->findOne(['id' => intval($id)]);
            if ($song == null) {
                $result = self::json_message("Resource error",false,3);
            } else { 
                $result = json_encode($song);
            }            
        } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
        }            
        $conexion = null;
        return $result;
    }

    public static function getByGenre($genre) {
        try {
            $conexion = ConexionDB::conectar("Songs");
            $cursor = $conexion->Songs->find(array('genre' => $genre));
            $result = json_encode($cursor->toArray());
        } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
        }
        $conexion = null;
        return $result;
    }   
    
    public static function getTopRated() {
        try {
            $conexion = ConexionDB::conectar("Songs");
            $cursor = $conexion->Songs->find(
                [],
                [
                    'limit' => 5,
                    'sort' => ['vote_average' => -1],
                ]);
            $result = json_encode($cursor->toArray());
        } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
        }
        $conexion = null;
        return $result;
    }      

    public static function deleteOne($id) {
        try {
            $conexion = ConexionDB::conectar("Songs");
            $cursor = $conexion->Songs->deleteOne(array('id' => intval($id)));  
            
            $result = self::json_message("Deleted ".$cursor->getDeletedCount()." document(s)\n",true,1);        
        } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
        }
        $conexion = null;
        return $result;
    }

    public static function voteForSong($id,$vote) {
        try {
            $conexion = ConexionDB::conectar("Songs");
            
            //Primero sacamos el número de votos de la canción y su media
            $song = $conexion->Songs->findOne(array('id' => intval($id)));
            $vote_count = $song['vote_count'];
            
            $vote_average = $song['vote_average'];
            //Actualizamos la media
            $new_average = (($vote_count * $vote_average) + $vote) / ($vote_count + 1);
            $vote_count++; //Incrementamos en uno el número de votos

            $cursor = $conexion->Songs->updateOne(
                ['id' => intval($id)],
                ['$set' =>  [
                            'vote_count' => $vote_count,
                            'vote_average' => $new_average
                            ]
                ]
            );
            
            $result = self::json_message("Updated 1 document\n",true,1);        
        } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
        }
        $conexion = null;
        return $result;
    }    

    public static function newSong() {

        try {
            $conexion = ConexionDB::conectar("Songs");

            //La única forma de leer PUT en PHP
            $put = file_get_contents( 'php://input', 'r' );
            //Enviamos en POSTMAN en body la canción en formato JSON como raw (marcar también JSON al final)
            $put_json = json_decode($put,true);
    
            //Primero sacamos el máximo id
            $song = $conexion->Songs->findOne(
                [],
                [
                    'sort' => ['id' => -1],
                ]);
            if (isset($song['id']))
                $max = $song['id'] + 1;
            else 
                $max = 1;

            $result = $conexion->Songs->insertOne([
                'id' => $max,
                'title' => $put_json["title"],
                'author' => $put_json["author"],
                'release_date' => $put_json["release_date"],
                'vote_average' => 0,
                'vote_count' => 0,
                'original_language' => $put_json["original_language"],
                'genre' => $put_json["genre"],
                'album' => $put_json["album"],
                'duration' => $put_json["duration"]
            ]);

            $result = self::json_message("Created 1 document\n",true,1);
          } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
          }
          $conexion = null;
          return $result;
    }

    public static function newSongPOST() {

        try {
            $conexion = ConexionDB::conectar("Songs");
    
            //Primero sacamos el máximo id
            $song = $conexion->Songs->findOne(
                [],
                [
                    'sort' => ['id' => -1],
                ]);
            if (isset($song['id']))
                $max = $song['id'] + 1;
            else 
                $max = 1;

            $result = $conexion->Songs->insertOne([
                'id' => $max,
                'title' => $_POST["title"],
                'author' => $_POST["author"],
                'release_date' => $_POST["release_date"],
                'vote_average' => 0,
                'vote_count' => 0,
                'original_language' => $_POST["original_language"],
                'genre' => $_POST["genre"],
                'album' => $_POST["album"],
                'duration' => $_POST["duration"]
            ]);

            // //Que no salga el warning de crear un objeto no inicializado
            $result = self::json_message("Created 1 document\n",true,1);

          } catch(Exception $e) {
            $result = self::json_message("Database error",false,2);
          }
          $conexion = null;
          return $result;
    }

    public static function json_message($message, $success, $status) {
        error_reporting(0);
        $result->status_message = $message; 
        $result->success = $success;
        $result->status_code = $status;
        $result = json_encode($result);

        return $result;
    }

}

?>