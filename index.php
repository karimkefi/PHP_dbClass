<?php

function getDBObject() {
    $db = new PDO('mysql:host=127.0.0.1; dbname=DBClass', 'root');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
}

function hydrateAutoSaveClassObject ($db) {
    $query = $db->prepare("SELECT `id`, `name`, `age`, `DOB` FROM `users`");
    $query->setFetchMode(PDO::FETCH_CLASS, 'AutoSaveClass');
    $query->execute();
    $results = $query->fetchAll();
    return $results;
}

function saveNameToDB ($db, $newObject) {
    $query = $db->prepare("UPDATE `users` SET `name` = :name WHERE `id` = :id;");

    $newName = $newObject->getName();

    $query->bindParam(':id', $newObject->id);
    $query->bindParam(':name', $newName);

    $query->execute();
    return true;
}


class AutoSaveClass {
    public $id;
    protected $name;
    public $age;
    public $DOB;
    public $db;

    public function doSomethingFunc(){
        return "Name is " . $this->name . ". DOB is: " . $this->DOB;
    }

    public function setName($newName){
        $this->name = $newName;
    }

    public function getName(){
        return $this->name;
    }

    public function __destruct() {
        echo "AutoSaving DB";
        saveNameToDB($this->db, $this);
    }
}


$DB = getDBObject();
$arrayObjs = hydrateAutoSaveClassObject($DB);

foreach ($arrayObjs as $arrayObj) {
    echo  nl2br (" \n ");
    echo $arrayObj->doSomethingFunc();
    $arrayObj->db = $DB;
}


list($user1, $user2, $user3) = $arrayObjs;

$user1->setName("Stephen");
unset($user1);

echo  nl2br (" \n ");

