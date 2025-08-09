<?php
require_once "db_conect.php";
class Crud extends Conect{
    private function conditions(Array $params){
        $conditions = array();
        $realParams = array();
        foreach($params as $key => $param){
            if($key != "or" && $key != "and"){
                $conditions[] = $key . " = :" . $key;
                $realParams[$key] = $param;
            }
        }
        return [$conditions, $realParams];
    }
    // Builds SQL conditions and parameter array from input
    private function setFields(Array $fields){
        $stringFields = "";
        if(count($fields) > 0){
            foreach($fields as $field){
                $stringFields .= $field . ",";
            }
        } else {
            $stringFields = "*";
        }
        $stringFields = rtrim($stringFields, ",");
        return $stringFields;
    // Returns a comma-separated string of fields for SELECT
    }
    private function selectOperator(Array $params){
        $boolOr=false;
        $boolAnd=false;
        $change = 0;
        $changeCopy=$change;
        $arrayToArray = array();
        $array = array();
        $orderOperator=array();
        foreach($params as $key => $param){
            if($key=="or"){
                $boolOr=true;
    // Determines the logical operator order (AND/OR) for conditions
                array_push($orderOperator,$key);
                if($boolAnd){
                    $boolAnd=false;
                    $change++;
                }
            }
            if($key=="and"){
                $boolAnd=true;
                array_push($orderOperator,$key);
                if($boolOr){
                    $boolOr=false;
                    $change++;
                }
            }
            if($boolAnd){
                if($changeCopy!=$change){
                    array_push($arrayToArray, $array);        
                    $array=[];
                    $changeCopy=$change;
                }
            }
            if($boolOr){
                if($changeCopy!=$change){
                    array_push($arrayToArray, $array);        
                    $array=[];
                    $changeCopy=$change;
                }
            }
            if($boolOr && $key!="or" && $key!="and"){
                $array[$key]=$param;
            }
            if($boolAnd && $key!="or" && $key!="and"){
                $array[$key]=$param;
            }
        }
        array_push($arrayToArray, $array);
        if(count($orderOperator)==1){
            array_push($arrayToArray, $array);
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
        $stringFields = $this->setFields($fields);
        $query = "select {$stringFields} from {$tableNames[0]}";
    // Reads selected fields from a table with optional conditions
        if($condition!=null){
            $query .= $where.$condition;
        }elseif(count($arrayConditions[1][0]) > 0){
            $query .= $where.$arrayConditions[0];
        }
        if(count($arrayConditions)<1){
            $arrayConditions[2]=$params;
        }
        return $this->executeQuery($this->getConection(), $query, $arrayConditions[2]);
    }

    public function iterateArray(Array $array){
        echo "__ <br>";
        foreach($array as $arr){
            foreach($arr as $key => $value){
            echo $key." : ".$value."<br>";
        }
        echo "__ <br>";
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
            "last_name"=>"Perez"
        );
        $data = $this->readSelectedFields($tables, $fields, $params);
        $this->iterateArray($data);
    // Constructor: example usage of the CRUD class
        // $this->endConection();
    }
}
$newCrud = new Crud();