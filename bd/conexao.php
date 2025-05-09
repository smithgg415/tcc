<?php
require_once 'config.php';

class conexao{
    private static $pdo;

    private function __construct() {

    }
    private static function verificaExtensao(){
        $extensao = 'pdo_mysql';
    }
    public static function getInstance(){

        self::verificaExtensao();
        if(!isset(self::$pdo)){
            try{              
                self::$pdo = new PDO('mysql:host=' . HOST . ':' . PORT . ';dbname=' . DBNAME . ';charset=' . CHARSET . '', USER, PASSWORD);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                
            } catch (PDOException $e) {
                print "Erro: " . $e->getMessage();
            }
        }
        return self::$pdo;
    }
    public static function isConectado(){
        
        if(self::$pdo):
            return true;
        else:
            return false;
        endif;
    }
 
 }