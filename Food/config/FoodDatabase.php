<?php
class FoodDatabase
{
    private string $host = 'localhost';
    private string $username = 'root';
    private string $password = '';
    private string $database = 'Food';

    public function connect(): mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );
        
        return $connection;
    }
}
