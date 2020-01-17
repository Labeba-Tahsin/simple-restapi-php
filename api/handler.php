<?php

$conn= new mysqli('localhost','root','','restapi');
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['id'])){
        $id= $conn->real_escape_string($_GET['id']);
        $sql= $conn->query("SELECT name,age from customers where id='$id'");
        $data=$sql->fetch_assoc();
    }
    else{
        $data=array();
        $sql= $conn->query("SELECT name,age from customers");
        while($d = $sql->fetch_assoc() ){
            $data[]=$d;
        }
    }

    exit(json_encode($data));
}

else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['name']) && isset($_POST['age']) ){
        $name=$conn->real_escape_string($_POST['name']);
        $age=$conn->real_escape_string($_POST['age']);

        $conn->query("INSERT INTO customers (name,age,addOn) values ('$name','$age',NOW())");
        exit(json_encode(array("status"=>'success')));
    }                             
    else{
        exit(json_encode(array("failed"=>'check Inputs')));
    }
}

else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    if(!isset($_GET['id'])){
        exit(json_encode(array("failed"=>'check Inputs')));
    }
    $allParams=array();
    $data=file_get_contents("php://input");
    if(strpos($data,'=') !== false){
        $data=explode('&',$data);
        
        foreach($data as $pair){
            $pair = explode('=',$pair);
            $allParams[$pair[0]]=$pair[1];
        }

        $customerID=$conn->real_escape_string($_GET['id']);
        if (isset($allParams['name']) && isset($allParams['age'])) {
            $conn->query("UPDATE customers SET age='".$allParams['age']."', name='".$allParams['name']."' WHERE id='$customerID'");
        } else if (isset($allParams['name'])) {
            $conn->query("UPDATE customers SET name='".$allParams['name']."' WHERE id='$customerID'");
        } else if (isset($allParams['age'])) {
            $conn->query("UPDATE customers SET age='".$allParams['age']."' WHERE id='$customerID'");
        } else
            exit(json_encode(array("status" => 'failed', 'reason' => 'Check Your Inputs')));

        exit(json_encode(array("status" => 'success')));
        

        
    }
    else{
        exit(json_encode(array("failed"=>'check Inputs')));  
    }

}

else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    if (!isset($_GET['id']))
            exit(json_encode(array("status" => 'failed', 'reason' => 'Check Your Inputs')));

        $customerID = $conn->real_escape_string($_GET['id']);
        $conn->query("DELETE FROM customers WHERE id='$customerID'");
        exit(json_encode(array("status" => 'success')));
}
?>