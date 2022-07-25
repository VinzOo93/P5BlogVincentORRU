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

    public function fetchOneNoLeftJoin($table, $selector ,array $params = null) {
        if (isset($params)){
            foreach ($params as $key => $value ) {
                $whereQuery[] = "$key = '$value'";
            }
            $whereStr = implode(" AND ",$whereQuery);

            $sql = "SELECT $selector FROM $table WHERE $whereStr";
            $queryStatement = $this->db->connectToDB()->prepare($sql);
            $queryStatement->execute();

        }

        return $queryStatement->fetch();
    }

    public function fetchWithLeftJoin($selector, $table, array $leftJoins, array $columns, array $where, $orderBy = null)
    {

        if (count($leftJoins) <= 3) {
            if (!empty($leftJoins) && !empty($columns)){
                foreach ($leftJoins as $key => $paramLeft) {
                    $column1 = array_search($key, $columns);
                    $leftQuery[] = "LEFT JOIN $paramLeft ON $table.$column1 = $paramLeft.$key";
                }
                $leftQuery = implode(' ', $leftQuery);
            } else {
                $leftQuery = '';
            }
            if (!empty($where)) {
                $whereQuery[] = "WHERE ";
                $count = 0;
                foreach ($where as $column => $value) {
                    if ($count === 0){
                        $whereQuery[] = "$column = '$value'";
                    } else {
                        $whereQuery[] = ",$column = '$value'";
                    }
                    $count++;
                }
                $whereQuery = implode('', $whereQuery);
            } else {
                $whereQuery = '';
            }
            $sql = "SELECT $selector FROM $table $leftQuery $whereQuery $orderBy";
            $queryStatement = $this->db->connectToDB()->prepare($sql);
            $queryStatement->execute();

        }
        return $queryStatement->fetchAll();
    }
    public function fetchOneWithLeftJoin($selector, $table, array $leftjoins, array $columns, array $where)
    {

        $leftQuery[] = null;
        $whereQuery[] = null;
        $count = 0;
        if (count($leftjoins) <= 3 && count($where)<= 3) {

            foreach ($leftjoins as $key => $paramLeft) {
                $column1 = array_search($key, $columns);
                $leftQuery[] = "LEFT JOIN $paramLeft ON $table.$column1 = $paramLeft.$key";
            }
            foreach ($where as $key => $paramWhere) {
                if (strpos($paramWhere, "'") !== false){
                    $paramWhere = str_replace("'","''", $paramWhere);
                }
                if ($count <= 1) {
                    $whereQuery[] = "$key = '$paramWhere'";
                } else {
                    $whereQuery[] = "AND $key = '$paramWhere'";
                }
            }

            $leftQuery = implode(' ', $leftQuery);
            $whereQuery = implode(' ', $whereQuery);

            $sql = "SELECT $selector FROM  $table $leftQuery  WHERE $whereQuery";
            $queryStatement = $this->db->connectToDB()->prepare($sql);
            $queryStatement->execute();
        }
        return $queryStatement->fetch();
    }

    public function fetchOneById($selector, $table, $id)
    {
        $sql = "SELECT $selector FROM  $table WHERE id_$table = $id ";
        $queryStatement = $this->db->connectToDB()->prepare($sql);
        $queryStatement->execute();

        return $queryStatement->fetch();
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
            $id = $this->values[0]['id_article'];
            $columnWhere = array_key_first($this->values[0]);

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
                    $sql = "UPDATE $table SET $strSqlSet WHERE $columnWhere = $id;";
                    $queryStatement = $this->db->connectToDB()->prepare($sql);
                    $queryStatement->execute();
                }
            } catch (\Exception $exception){
                echo 'erreur lors de la mise à jour';
            }
        }
    }

    public function delete ($table, $colmun, $params)
    {

        $sql = "DELETE FROM  $table WHERE $colmun = '$params' ";
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