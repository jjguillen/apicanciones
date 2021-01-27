<?php

    include_once("Controllers/Controller.php");

    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    
    //Quitamos todos los paths hasta quedarnos con apicanciones
    $uri = strstr($uri,"apicanciones");

    //Pasamos lo que queda de ruta a un array
    $paths = explode("/",$uri);

    $apiname = array_shift($paths); //apicanciones
    $resource = array_shift($paths); //songs
   
    if($resource == 'songs'){
        //Creamos objeto controlador
        $controller = new Controller();

        //Sacamos el siguiente parámetro de la url
        $action = array_shift($paths);

        //Sacamos todas las canciones
        if(empty($action)){
            $controller->handle_base($method);
        } 

        switch ($action) {
            case "toprated":  
                //Mostrar las favoritas
                $controller->show_toprated($method);              
                break;
            case "vote":
                //Vota por una canción, a continuación debe venir id de una canción y la nota
                $id = array_shift($paths);
                $vote =  array_shift($paths);
                if (!empty($id) && !empty($vote))
                    $controller->vote_for_song($method, $id, $vote);
                else 
                    header('HTTP/1.1 404 Not Found');                
            case "genre":
                //Acciones para mostrar canciones por género
                $genre = array_shift($paths);
                if (!empty($genre))
                    $controller->show_by_genre($method, $genre);
                else
                    header('HTTP/1.1 404 Not Found');
                break;
            case "id":
                //Acciones pasándole un id
                $id = array_shift($paths);
                if (!empty($id))
                    $controller->handle_id($method, $id);
                else 
                    header('HTTP/1.1 404 Not Found');
                       
        }

        
    } else {
        // Sólo se aceptan resources desde 'songs'
        header('HTTP/1.1 404 Not Found');
    }      

?>