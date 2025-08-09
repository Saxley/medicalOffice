<?
class Data{
    private string $host = 'localhost';
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