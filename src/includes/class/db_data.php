<?
// Local test: database connection. This function requests the data for signing in to the database from the .env file.
class Data{
    //A set of private variables.
    private string $host = 'localhost';//dir
    private string $dbName = 'medical_oppointment'; //database
    private string $username = 'root';//user
    private string $password = '4M0pr0gr4m4r$';//pw
    public function getHost(){ // this function returns the hostname
      return $this->host;
    }
    public function getDbName(){ // this function returns the database name
      return $this->dbName;
    }
    public function getUsername(){ // this function returns the username
      return $this->username;
    }
    public function getPassword(){ // this function returns the pw of user.
      return $this->password;
    }
}
