<?php
// Import the db_conect for use the PDO object
require_once "db_conect.php";
class Crud extends Conect{ // this class extends the Conect class to use its database connection.
    // ____ Formatting methods ____
    private function conditions(array $params):array{ // This method processes an array and returns two new arrays, formatted for use in a query
        $conditions = array();
        $realParams = array();
        $keyCopy="";
        $keyCopyKey="";
        foreach($params as $key => $param){
            if(!in_array($key, ['or', 'and'])){
                 if (is_numeric((int)$key[-1]) && (int)$key[-1]> 0){
                        $keyCopy = substr($key, 0, -1);
                    }
                if($keyCopy==substr($key,0,-1)){
                    $keyCopyKey=$key;
                    $key=$keyCopy;
                }else{
                    $keyCopyKey=$key;
                }
                $conditions[] = "{$key} = :{$keyCopyKey}";
                $realParams[$key] = $param;
            }
        }
        return [$conditions, $realParams];
    }

    private function selectOperator(array $params):array{

        $arrayToArray = array();
        $array = array();
        $orderOperator=array();
        foreach($params as $key => $param){
            if($key === 'or' || $key === 'and'){
                if(!empty($array)){
                    $arrayToArray[]=$array;
                    $array=[];
                }
                $orderOperator[]=$key;
            }else{
                if (is_numeric((int)$key[-1]) && (int)$key[-1]> 0){
                    $key = substr($key, 0, -1);
                }
                $array[$key]=$param;
            }
        }
        if(!empty($array)){
            $arrayToArray[]=$array;
        }
        array_push($arrayToArray,$orderOperator);
        return $arrayToArray;
    }
    public function executeQuery(object $conectionObject,string $query,Array $params){
        $stmt = $conectionObject->prepare($query);
        if(count($params)>0){
            $stmt->execute($params);
        }else{
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function readAllTable(string $tableName){
        $query = "select * from {$tableName}";
        return $this->executeQuery($this->getConection(), $query,array());
    }
    public function getCondition(Array $params){
        $arrayOperators = $this->selectOperator($params);
        $operators = array_pop($arrayOperators);
        $realParams = array();
        $actually=0;
        $condition="";
        for($i=0;$i<count($operators);$i++){
            $conditions = $this->conditions($arrayOperators[$i]);
            if($i!=$actually){
                $condition .= " {$operators[$i]} ";
    // Reads all rows from a table
                $actually=$i;
            }
            $condition .= implode(" {$operators[$i]} ", $conditions[0]);
            $realParams = $realParams + $conditions[1];
            // Builds the WHERE clause and parameters for complex queries
        }
        return [$condition, $conditions, $realParams];
    }
    public function readSelectedFields(Array $tableNames, Array $fields, Array $params){
        $condition = null;
        $where = " where ";
        $arrayConditions=array();
        if(count($params)>1){
            $arrayConditions = $this->getCondition($params);
        }else{
            foreach($params as $key=>$param){
                $condition .= $key."=:".$key;
            }
        }
        $stringFields = empty($fields) ? '*' : implode(',', $fields); // creates a comma-separated string from the $fields array, or a '*' if the array is empty
        $query = "select {$stringFields} from {$tableNames[0]}";
        if($condition!=null){
            $query .= $where.$condition;
        }elseif(count($arrayConditions[1][0]) > 0){
            $query .= $where.$arrayConditions[0];
        }
        if(count($arrayConditions)<1){
            $arrayConditions[2]=$params;
        }
        echo var_dump($arrayConditions[2]);
        return $this->executeQuery($this->getConection(), $query, $arrayConditions[2]);
    }

    public function iterateArray(Array $array){
        echo "__ <br>";
        if(empty($array)){
            echo "No se encontrarón coincidencias";
        }else{
            foreach($array as $arr){
                foreach($arr as $key => $value){
                    echo $key." : ".$value."<br>";
                }
                echo "__ <br>";
            }
        }
    }
    public function __construct(){
        $this->conect();
        // $data=$this->readAllTable("patient");
    // Prints array results in HTML format
        // $this->iterateArray($data);

        $tables = array("patient");
        $fields = array("name","last_name", "id","mobil");
        $params = array(
            "and"=>true,
            "name"=>"Angela",
            "last_name"=>"Carranza",
            "or"=>true,
            "last_name1"=>"Perez"
        );
        $data = $this->readSelectedFields($tables, $fields, $params);
        $this->iterateArray($data);
    // Constructor: example usage of the CRUD class
        // $this->endConection();
    }
}
$newCrud = new Crud();