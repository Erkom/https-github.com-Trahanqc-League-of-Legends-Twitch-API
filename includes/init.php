<?php
/**
 * Gain access to the database
 *
 * @return PDO
 */
if(!function_exists('connect_db')) {
    function connect_db() {
        try {
            $db = new PDO('mysql:host=localhost;dbname=sitemeut_louis', 'sitemeut_admin', '4C51d21f9C');
        }
        catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
        return $db;
    }
}

if(!function_exists('far_dump')) {
    function far_dump($string) {
        if($_SERVER['REMOTE_ADDR'] == '24.122.135.50' || $_SERVER['REMOTE_ADDR'] == '76.71.250.234') {
            echo '<pre>';
            var_dump($string);
            echo '</pre>';
        }
    }
}