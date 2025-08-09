<?
//This class request credentials to log in to the database server. 
class Data{
    private string $host = '127.0.0.1';
    private string $dbName = 'db_agenda_consulta';
    private string $username = 'root';
    private string $password = 'hola';
    
    public function getHost(){
      return $this->host;
    }
    public function getDbName(){
      return $this->dbName;
    }
    public function getUsername(){
      return $this->username;
    }
    public function getPassword(){
      return $this->password;
    }
}
?>