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

    public function fetchAllWithLeftJoin($selector, $table, array $leftjoins, array $columns, $orderBy = null)
    {
        $leftQuery[] = null;

        if (count($leftjoins) <= 3) {

            foreach ($leftjoins as $key => $paramLeft) {
                $column1 = array_search($key, $columns);
                $leftQuery[] = "LEFT JOIN $paramLeft ON $table.$column1 = $paramLeft.$key";
            }
            $leftQuery = implode(' ', $leftQuery);

            $sql = "SELECT $selector FROM  $table $leftQuery $orderBy";

            $queryStatement = $this->db->connectToDB()->prepare($sql);
            $queryStatement->execute();

        }
        return $queryStatement->fetchAll();
    }
    public function fetchOneWithLeftJoin($selector, $table, array $leftjoins, array $columns,array $where)
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
                $count++;
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
        return $queryStatement->fetchObject();
    }

    public function fetchOneById($selector, $table, $id)
    {
        $sql = "SELECT $selector FROM  $table WHERE id_$table = $id ";
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