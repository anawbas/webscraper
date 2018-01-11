<?php 
/************************************************
* @author Olu Adeniyi
* @copyright 2018

* Database Class to insert feeds into dabase
*
* @param  none
************************************************/
// Generic PDO database class

class Database{

    public $isConn;
    protected $datab;
    private $stmt;

    private $host   = DB_HOST;
    private $user   = DB_USER;
    private $passwd = DB_PASSWD;
    private $dbname = DB_NAME;

    // connect to db

    public function __construct(){


        $this->isConn = TRUE;
        try {
             
            // Set options
            $options = array(
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
            );
            //$dns = "mysql:host={$host};dbname={$dbname};charset=utf8"; 
            $dns = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;

            $this->datab = new PDO($dns, $this->user, $this->passwd , $options);
            $this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        
    }
    
    // disconnect from db
    public function Disconnect(){
        $this->datab = NULL;
        $this->isConn = FALSE;
    }

    // execute the query
    public function execSQL($query, $params = []){
        try {
            $this->stmt = $this->datab->prepare($query);
            return $this->stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }


}

?>
