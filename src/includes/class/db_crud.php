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
                $actually=$i;
            }
            $condition .= implode(" {$operators[$i]} ", $conditions[0]);
            $realParams = $realParams + $conditions[1];
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
        // $this->iterateArray($data);

        $tables = array("patient");
        $fields = array("name","last_name", "id","mobil");
        $params = array(
            "last_name"=>"Perez"
        );
        $data = $this->readSelectedFields($tables, $fields, $params);
        $this->iterateArray($data);
        // $this->endConection();
    }
}
$newCrud = new Crud();