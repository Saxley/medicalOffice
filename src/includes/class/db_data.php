<?
//This class request credentials to log in to the database server. 
class Data{
    private string $host = '127.0.0.1';
    private string $dbName = 'medical_oppointment';
    private string $username = 'root';
    private string $password = '4M0pr0gr4m4r$';
    
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