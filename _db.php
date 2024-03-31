<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$databaseName = 'course-management';

$con = mysqli_connect($hostname, $username, $password, $databaseName);

if (!$con) {
    die('<h1>DataBase is not connected</h1>');
}
class DB
{
    private $con = null;

    public function __construct(mysqli $con = null)
    {
        $this->con = $con;
    }

    function getAllRecords(string $query): array
    {
        $result = mysqli_query($this->con, $query);
        $rows = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    function getSingleRecord(string $query): array
    {
        $row = [];
        $result = mysqli_query($this->con, $query);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        }
        return $row;
    }

    function insertData(string $query): int
    {
        $result = mysqli_query($this->con, $query);
        $last_id = 0;
        if ($result) {
            $last_id = mysqli_insert_id($this->con);
        }
        return $last_id;
    }

    function insertMultipleData($query)
    {
        if (mysqli_multi_query($this->con, $query)) {
            return true;
        } else {
            return false;
        }
    }

    function updateData(string $query): bool
    {
        $status = false;
        $result = mysqli_query($this->con, $query);
        if ($result) {
            $status = true;
        }
        return $status;
    }

    function checkError(): void
    {
        echo mysqli_error($this->con);
        exit;
    }
    function deleteData($query)
    {
        $status = false;
        $result = mysqli_query($this->con, $query);
        if ($result) {
            $status = true;
        }
        return $status;
    }
}

$db = new DB($con);
