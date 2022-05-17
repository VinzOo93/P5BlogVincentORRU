<?php

namespace App\Manager;

use App\DataBaseConnexion\PdoConnexion;

abstract class QueryManager
{
    private PdoConnexion $db;
    private string $param = '?';
    private array $columns;
    private array $datas;
    private array $values;

    public function __construct()
    {
        $this->db = new PdoConnexion();
    }

    public function fetchAll($selector, $table)
    {
        $sql = "SELECT $selector FROM  $table  ";
        $queryStatement = $this->db->connectToDB()->prepare($sql);
        $queryStatement->execute();

        return $queryStatement->fetchAll();
    }

    public function fetchOneById($selector, $table, $id)
    {
        $sql = "SELECT $selector FROM  $table WHERE id = $id ";
        $queryStatement = $this->db->connectToDB()->prepare($sql);
        $queryStatement->execute();

        return $queryStatement->fetchObject();
    }

    public function insert($table, array $params)
    {
        if (!empty($params)) {

            $this->getDatas($params);

            $sqlColumns = implode(',', $this->columns);
            $sqlParams = implode(',', $this->datas);
            try {
                $sql = "INSERT INTO $table($sqlColumns) VALUES($sqlParams);";
                $queryStatement = $this->db->connectToDB()->prepare($sql);
                $queryStatement->execute($this->values);
            } catch (\Exception $exception) {
                echo 'erreur lors de l\'ajout';
            }


        }
    }

    public function update($table, array $params)
    {
        if (!empty($params)) {
            $sqlSet = [];
            $this->getDatas($params);
            $id = $this->values[0];
            foreach (array_slice($this->columns,1)  as $key => $column) {
                $key++;
                $value = $this->values[$key];
                if (!empty($value)) {
                    $sqlSet[] = "$column = '$value'";
                }
            }
            $strSqlSet = implode(",", $sqlSet);
            try {
                if (!empty($id)) {
                    $sql = "UPDATE $table SET $strSqlSet WHERE id = $id;";
                    $queryStatement = $this->db->connectToDB()->prepare($sql);
                    $queryStatement->execute();
                }
            } catch (\Exception $exception){
                echo 'erreur lors de la mise à jour';
            }
        }
    }

    public function delete ($table, $id)
    {

        $sql = "DELETE FROM  $table WHERE id = $id ";
        $queryStatement = $this->db->connectToDB()->prepare($sql);
        $queryStatement->execute();
        return $queryStatement->fetchObject();
    }

    private function getDatas($params)
    {
        $this->columns = [];
        $this->datas = [];
        $this->values = [];
        foreach ($params as $key => $value) {
            $this->columns[] = $key;
            $this->datas[] = $this->param;
            $this->values[] = $value;
        }
    }
}