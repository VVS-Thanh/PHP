<?php

//Hàm query
function query($sql, $data=[], $isStatus = true)
{
    global $conn;

    try {
        $statement = $conn->prepare($sql);
        $status = $statement->execute($data);

        if ($isStatus) {
            return $status;
        }

        return $statement;
    } catch (PDOException $e) {
        require_once 'error.php';
        die();
    }
}

//Hàm insert dữ liệu vào table => return true, false
function create($table, $attributes = [])
{
    //name, created_at, updated_at
    if (!empty($attributes)) {
        $keys = array_keys($attributes);
        $sql = "INSERT INTO {$table}(".implode(', ', $keys).") VALUES(:".implode(', :', $keys).")";
        $status = query($sql, $attributes);

        return $status;
    }

    return false;
}

//Hàm insert dữ liệu vào table => return về id vừa insert
function createGetId($table, $attributes = [])
{
    global $conn;
    if (!empty($attributes)) {
        create($table, $attributes);
        return $conn->lastInsertId();
    }

    return false;
}

//Hàm update dữ liệu vào table => return true, false
function update($table, $attributes = [], $condition)
{
    //UPDATE groups SET name=:name, email=:email WHERE id=:id

    if (!empty($attributes)) {
        $keys = array_keys($attributes);
        $update = "";
        foreach ($keys as $key) {
            $update.=$key.'=:'.$key.', ';
        }

        $update = rtrim($update, ', ');

        $sql = "UPDATE {$table} SET ".$update." WHERE $condition";

        $status = query($sql, $attributes);

        return $status;
    }

    return false;
}

//Hàm delete
function delete($table, $condition)
{
    $sql = "DELETE FROM {$table} WHERE $condition";

    $status = query($sql);
    return $status;
}

//Hàm get
function get($sql, $data = [])
{
    global $conn;
    $statement = query($sql, $data, false);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

//hàm first
function first($sql, $data = [])
{
    global $conn;
    $statement = query($sql, $data, false);
    return $statement->fetch(PDO::FETCH_ASSOC);
}

//Hàm getRows
function getRows($sql, $data = [])
{
    global $conn;
    $statement = query($sql, $data, false);
    return $statement->rowCount();
}

function getDateFormat($date, $format){
    $dateObj = date_create(($date));
    return date_format($dateObj, $format);
}

function getOperator($filters)
{
    if (strpos($filters, 'WHERE') !== false) {
        return ' AND ';
    }
    return ' WHERE ';
}

function getPaginateUrl($page)
{
    //Xử lý query string
    $query = [];
    $isPage = false;
    $currentIndex = null;
    if (!empty($_SERVER['QUERY_STRING'])) {
        $query = explode('&', $_SERVER['QUERY_STRING']);

        foreach ($query as $index => $item) {
            $itemArr = explode('=', $item);
            if ($itemArr[0] == 'page') {
                $isPage = true;
                $currentIndex = $index;
                break;
            }
        }
    }

    if (!$isPage) {
        array_push($query, 'page='.$page);
    } else {
        $query[$currentIndex] = 'page='.$page;
    }

    return '?'.implode('&', $query);
}