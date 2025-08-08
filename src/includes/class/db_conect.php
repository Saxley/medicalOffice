<?
// import the db_conect class to get login credentials
require_once 'db_data.php';
// The Conect class connects to MariaDB or MySQL using PDO.
class Conect{
  //private variables and functions
  private object $data; // store a data object
  private object $conectionObject; // store a data object
  
  private function setData(){ // this function creates and initializes a Data object
    $this->data = new Data(); 
  }
  private function getData(){ // this function returns an object of type Data.
    return $this->data;
  }
  private function setConection(object $conection){// this function assigns a Conect object to the $conectionObject variable
    $this->conectionObject = $conection;
  }
  // protected functions
  protected function conect(){ // this function initializes the database connection.
    $this -> setData(); // initialize the data object
    $objectData = $this -> getData(); // requests the data object and stores it in the $objectData variable.
    $dns = "mysql:host={$objectData->getHost()};dbname={$objectData->getDbName()};charset=utf8mb4"; // Sets up the DNS connection string for PDO, using data from the object. 
    // The next line stores the database username and pw, which are retrieved from the Data object
    $user = $objectData -> getUsername();
    $password = $objectData -> getPassword();
    try{ // Instantiates a new PDO connectiin object and assigns it via the setConection method
      $conection = new PDO($dns, $user, $password);
      $conection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->setConection($conection);
    }catch(PDOException $e){ // if try not is success then show the error.
      echo "Error de conexion: ".$e->getMessage();
      die();
    }
  }
  protected function getConection(){
    return $this->conectionObject;
  }
  protected function endConection(){
    $this->conectionObject = null;
  }
}