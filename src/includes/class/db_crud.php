<?php
// Import the db_conect for use the PDO object
require_once "db_conect.php";
class Crud extends Conect{ // this class extends the Conect class to use its database connection.
    // ____ Formatting methods ____
    private function conditions(array $params):array{ // This method processes an array and returns two new arrays, formatted for use in a query
        $conditions = array();
        $realParams = array();
        $keyCopy="";
        foreach($params as $key => $param){
            if(!in_array($param, ['or', 'and'])){
                if (is_numeric((int)$key[-1]) && (int)$key[-1]> 0){
                        $keyCopy = substr($key, 0, -1);
                    }else{
                        $keyCopy=$key;
                    }
                $conditions[] = "{$keyCopy} = :{$key}";
                $realParams[$key] = $param;
            }
        }
        return [$conditions, $realParams];
    }
    private function selectOperator(array $params):array { // This method is responsible for deconstructing the input array.It returns the logical operators (AND/OR) and a collection of parameter arrays, each formatted as key-value pairs.
      //arrays elements
        $arrayToArray = array();
        $array = array();
        $orderOperator=array();
        //this foreach loop iterates through the received array
        foreach($params as $key => $param){
            if($param === 'or' || $param === 'and'){ // This conditional checks if the $key is a logical operator.If true, it adds the operator to the $orderOperator array.
                if(!empty($array)){ // This statement  verifies if the $array contains any data.If it does, the $array is added to the $arrayToArray array for the further processing.
                    $arrayToArray[]=$array;
                    $array=[]; // empties the $array for subsequent assignment of new values.
                }
                $orderOperator[]=$param;
            }else{ // if the key doesn't correspond to a logical operator, it stores the key and its corresponding value in the $array.
                $array[$key]=$param;
            }
        }
        if(!empty($array)){// This statement  verifies if the $array contains any data.If it does, the $array is added to the $arrayToArray array for the further processing.
            $arrayToArray[]=$array;
        }
        array_push($arrayToArray,$orderOperator);// Appends the $orderOperator array to the $arrayToArray array using array_push(), so it can be included in the final response.
        return $arrayToArray;
    }
    
    /* Constructs the WHERE clause and parameters for a dynamic SQL query.
    *
    *This method processes an array of parameters to build a full SQL condition string and separates the logical operators from the actual parameter values.
    *@param array $params An associative array containing the query conditions and logical operators.
    *@return array A three-element array containing:
    * 1. The final WHERE condition string.
    * 2. An array of conditions.
    * 3. An associative array of clean parameters ready for biding.
    */
    public function getCondition(array $params):array{
        $actually=0;
        $ensemble=false;
        $realParams = array(); // Initialize a new array that stores the real parameters for binds.
        $condition=""; // Condition is a variable that store the query.
        $arrayOperators = $this->selectOperator($params);// Separate the logical operators(AND/OR) from the condition parameters.
        $operators = array_pop($arrayOperators);// Extracts the array of operators
        for($i=0;$i<count($operators);$i++){ // Iterate the $operators array for create the query.
            $conditions = $this->conditions($arrayOperators[$i]); // Received the conditions for the bind 
            $countElements = count($conditions[0]);
            if($i!=$actually){
                $condition .= " {$operators[$i]} ";
                if(count($arrayOperators)!=count($operators)){
                    $i++;
                } // if $operators has more elements $i add one
                $actually=$i+1;
            }
            if($countElements>1){
                $condition .= "(";
                $ensemble = true;
            }
            $condition .= implode(" {$operators[$i]} ", $conditions[0]); // add to string the clause where
            if($ensemble){
                $condition .= ")";
                $ensemble = false;
            }
            $realParams = $realParams + $conditions[1]; // add to $realParams array the $conditions[1] array.
            if($countElements < 2 && $i==$actually){
                $condition .= " {$operators[$i]} ";
                $actually=$actually+1; 
            }
        }
        // Returns a structured array with three elements: the SQL condition string or query, the conditions array, and the bind parameters array.
        return [$condition, $conditions, $realParams];
    }
    // Iterate array
    public function iterateArray(array $array){ //this method iterates the received array and prints it content to the screen
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
    // Execute query
    public function executeQuery(object $conectionObject,string $query,array $params):array{ // This method constructs a prepared statement from the query and binds the input parameters before executing it.
        $stmt = $conectionObject->prepare($query);
        if(count($params)>0){ // Binds the input parameters before execution, provided the parameter count is greater than zero.
            $stmt->execute($params);
        }else{
            $stmt->execute();
        }
        // Returns an array containing the results of the query
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ____ Create query (CRUD) ____
    public function readAllTable(string $tableName):array{// Constructs a basic SELECT query to retrieve all columns and rows from the table passed as a parameter.
        $query = "select * from {$tableName}";
        // Returns an array containing the results of the query
        return $this->executeQuery($this->getConection(), $query,array());
    }
    public function readSelectedFields(array $tableNames, array $fields, array $params){ // This method generates a parameterzide SQL query by mapping user-specified fields and values to the query's structure.
        $condition = null;
        $where = " where "; // This is the keyword used to filter records
        $arrayConditions=array();
        if(count($params)>1){ // Check if more conditions exist in the array to be processed.
            $arrayConditions = $this->getCondition($params); // get the clause where condition 
        }else{ // else iterate the $params array and create the clause for the query
            foreach($params as $key=>$param){
                $condition .= $key."=:".$key;
            }
        }
        $stringFields = empty($fields) ? '*' : implode(',', $fields); // creates a comma-separated string from the $fields array, or a '*' if the array is empty
        $query = "select {$stringFields} from {$tableNames[0]}"; // add to the query to fields input for the user in order and separated for commas
        if($condition!=null){ // Check if $condition string is different to null value.
            $query .= $where.$condition; // appends the WHERE clause and the SQL condition to the query string.
        }elseif(isset($arrayConditions[1][0]) && count($arrayConditions[1][0]) > 0){ // If the parameter for the condition exist, add the WHERE clause and the SQL condition to the query.
            $query .= $where.$arrayConditions[0];
        }
        if(count($arrayConditions)<1){ // check if it has no elements
            $arrayConditions[2]=$params; // store the parameters in $arrayConditions
        }
        return $this->executeQuery($this->getConection(), $query, $arrayConditions[2]); // returns the query execution result.
    }

    public function __construct(){
        $this->conect();
        // $data=$this->readAllTable("patient");
        // Prints array results in HTML format
        // $this->iterateArray($data);

        $tables = array("patient");
        $fields = array("name","last_name", "id","mobil");
        $params = array(
            "and"=>"and",
            "name"=>"Angela",
            "or1"=>"or",
            "last_name2"=>"Carranza",
            "last_name1"=>"Perez",
            "last_name"=>"Garcia"
        );
        $data = $this->readSelectedFields($tables, $fields, $params);
        $this->iterateArray($data);
        // Constructor: example usage of the CRUD class
        // $this->endConection();
    }
}
$newCrud = new Crud();