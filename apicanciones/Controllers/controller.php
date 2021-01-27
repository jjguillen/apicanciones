<?php
    include_once("autoload.php");
    use Songs\ConexionDB;
    use Songs\SongsDB;
    
    class Controller {

        private $method;

        /**
         * Constructor
         */
        public function __construct() {
            $this->method = "";
        }

        /**
         * Handle_base: la url solo lleva "songs", se muestran todas las canciones
         */
        public function handle_base($method) {
            $this->method = $method;
            
            switch($method){
                case 'PUT':
                    $this->create_song();
                    break;
                case 'POST':
                    $this->create_song_post();
                    break;
                case 'GET':
                    $this->display_songs();  
                    break;         
            }
        }

        /**
         * Show_toprated: la url lleva "toprated", se muestran las más votadas
         */
        public function show_toprated($method) {
            if ($method == "GET") {
                header("Content-Type: application/json; charset=UTF-8");
                echo SongsDB::getTopRated(); 
            } else {
                header('HTTP/1.1 404 Not Found');
            }
        }

        /**
         * Vote_for_song: la url lleva "vote", espera un id de la canción por la que se vota
         */
        public function vote_for_song($method,$id,$vote) {
            if ($method == "PUT") {
                header("Content-Type: application/json; charset=UTF-8");
                echo SongsDB::voteForSong($id,$vote);
            } else {
                header('HTTP/1.1 404 Not Found');
            }
        }

        /**
         * Show_by_genre: la url lleva "genre", se muestran las canciones por género
         */
        public function show_by_genre($method, $genre) {
            if ($method == "GET") {
                header("Content-Type: application/json; charset=UTF-8");
                echo SongsDB::getByGenre($genre); 
            } else {
                header('HTTP/1.1 404 Not Found');
            }            
        }

        /**
         * Handle_id: la url lleva "id", se realizan acciones GET,PUT,DELETE por id de canción
         */
        public function handle_id($method, $id) {
            $this->method = $method;
            switch($method){
                case 'DELETE':
                    $this->delete_song($id);
                    break;
                case 'GET':
                    $this->display_song($id);
                    break;
                default:
                    header('HTTP/1.1 405 Method not allowed');
                    header('Allow: GET, PUT, DELETE');
                    break;
            }            
        }

        /**
         * Display_songs: muestra todas las canciones
         */
        public function display_songs() {
            header("Content-Type: application/json; charset=UTF-8");
            echo SongsDB::getAll();          
        }

        /**
         * Create_song: crea una nueva canción por PUT, requiere objeto
         */
        public function create_song() {
            header("Content-Type: application/json; charset=UTF-8");
            echo SongsDB::newSong(); 
        }

        /**
         * Create_song_post: crea una nueva canción por POST, requiere request
         */
        public function create_song_post() {
            header("Content-Type: application/json; charset=UTF-8");
            echo SongsDB::newSongPOST(); 
        }

        /**
         * Delete_song: borra canción por id
         */
        public function delete_song($id) {
            header("Content-Type: application/json; charset=UTF-8");
            echo SongsDB::deleteOne($id);
        }

        /**
         * Display_song: muestra la canción de ese id
         */
        public function display_song($id) {
            header("Content-Type: application/json; charset=UTF-8");
            echo SongsDB::getOne($id);
        }

    }



?>