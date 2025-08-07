<?
require_once 'db_data.php';
class Conect{
  //private variables and functions
  private object $data;
  private object $conectionObject;
  private function setData(){
    $this->data = new Data();
  }
  private function getData(){
    return $this->data;
  }
  private function setConection(object $conection){
    $this->conectionObject = $conection;
  }
  // protected functions
  protected function conect(){
    $this -> setData();
    $objectData = $this -> getData();
    $dns = "mysql:host={$objectData->getHost()};dbname={$objectData->getDbName()};charset=utf8mb4";
    $user = $objectData -> getUsername();
    $password = $objectData -> getPassword();
    try{
      $conection = new PDO($dns, $user, $password);
      $conection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->setConection($conection);
    }catch(PDOException $e){
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