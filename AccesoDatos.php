<?php
class AccesoDatos
{
	private static $_objetoAccesoDatos;
    private $_objetoPDO;
 
    private function __construct()
    {
        try {
 
            $this->_objetoPDO = new PDO('mysql:host=localhost;dbname=login_pdo;charset=utf8',"root","");
 
            $this->_objetoPDO->exec("SET CHARACTER SET utf8");
 
        } catch (PDOException $e) {
 
            print "Error!!!<br/>" . $e->getMessage();
 
            die();
        }
    }
 
    public function RetornarConsulta($sql)
    { 
		try {
            
            return $this->_objetoPDO->prepare($sql);

        } 
        catch (Exception $e) 
        {
            print "Error!!!<br/>" . $e->getMessage();
        }
        
    }
    
     public function RetornarUltimoIdInsertado()
    { 
		$consulta= $_objetoAccesoDatos->RetornarConsulta("SELECT id from usuarios order by id desc limit 1 ");
        return $consulta->execute();
        //return $this->objetoPDO->lastInsertId();
    }
 
    public static function dameUnObjetoAcceso()
    { 
		if (!isset(self::$_objetoAccesoDatos)) 
        {
            self::$_objetoAccesoDatos = new AccesoDatos();
        }

        return self::$_objetoAccesoDatos;
    }
 
    public function __clone()
    { 
 		trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR); 
    }
}