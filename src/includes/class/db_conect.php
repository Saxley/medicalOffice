<?
// imports db_data that contains credentials to log in to the database server
require_once 'db_data.php';
class Conect{ // This class establishes a connection with the  database server and returns a PDO object.
  //private variables and functions
  private object $data;
  private $conectionObject;
  private function setData(){ // this method initializes a Data object and assigns it to the $data variable
    $this->data = new Data();
  }
  private function getData(){ // this method returns a data object
    return $this->data;
  }
  private function setConection(object $conection){ // Assigns a PDO object to the $conectionObject variable
    $this->conectionObject = $conection;
  }
  // protected functions
  protected function conect(){ // this method initializes and establishes the connection with the database server
    //__ request the credentials
    $this -> setData(); 
    $objectData = $this -> getData();
    $user = $objectData -> getUsername(); 
    $password = $objectData -> getPassword();
    //__
    $dns = "mysql:host={$objectData->getHost()};dbname={$objectData->getDbName()};charset=utf8mb4"; // Creates the DNS(Data Source Name) string for the database connection.
    try{
      $conection = new PDO($dns, $user, $password); // Initializes a new PDO object, passing the DNS, username and password as parameters
      $conection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->setConection($conection);
    }catch(PDOException $e){ // If the connection is not successful, it returns the error.
      echo "Error de conexion: ".$e->getMessage();
      die();
    }
  }
  protected function getConection(){ // it method returns the object into $conectionObject  
    return $this->conectionObject;
  }
  protected function endConection(){ // it method remove the object into $conectoinObject
    $this->conectionObject = null;
  }
}