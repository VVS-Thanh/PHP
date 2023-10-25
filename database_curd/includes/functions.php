<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

function getDateFormat($date, $format)
{
    $dateObj = date_create($date);
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

function getError($fieldName)
{
    global $errors;
    if (!empty($errors[$fieldName])) {
        return reset($errors[$fieldName]);
    }

    return false;
}

function getOld($fieldName)
{
    global $old;
    if (!empty($old[$fieldName])) {
        return $old[$fieldName];
    }
    return false;
}

function getMessage($message, $type = 'success  ')
{
    return !empty($message) ? '<div class="alert alert-'.$type.'">'.$message.'</div>' : '';
}

function redirect($to)
{
    header('Location: '.$to);
    exit;
}

function reload()
{
    header("Refresh:0");
    exit;
}

//Hàm xử lý session
function setSession($key, $value)
{
    $_SESSION[$key] = $value;
}

function getSession($key)
{
    if (isset($_SESSION[$key])) {
        return $_SESSION[$key];
    }
    return false;
}

function removeSession($key)
{
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

function getFlashData($key)
{
    $data = getSession($key);
    removeSession($key);

    return $data;
}

function sendMail($to, $subject, $content)
{
    $mail = new PHPMailer(true);

    $mail->CharSet = "UTF-8";

    //Server settings
    $mail->SMTPDebug = false;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'contact@unicode.vn';                     //SMTP username
    $mail->Password   = 'ktkqzcoyblrdfvuw';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('contact@unicode.vn', 'Unicode Academy');    //Add a recipient
    $mail->addAddress($to);               //Name is optional
    $mail->addReplyTo('contact@unicode.vn', 'Unicode Academy');

//Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $content;

    return $mail->send(); //Trả về true false
}
