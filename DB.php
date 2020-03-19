<?php

class DB
{
    private static $host;
    private static $user;
    private static $password;
    private static $conn;
    private static $dbname = 'me_design_testcase';
    private static $table = 'sentences';

    public static function init($host, $user, $password, $dbname = null)
    {
        self::$host = $host;
        self::$user = $user;
        self::$password = $password;
        self::$dbname = $dbname ?: self::$dbname;

        self::initTables();
        self::connect();

        return self::class;
    }

    protected static function connect()
    {
        self::$conn = new mysqli(self::$host, self::$user, self::$password, self::$dbname);
    }

    protected static function initTables()
    {
        $generalConn = new mysqli(self::$host, self::$user, self::$password);
        $dbname = self::$dbname;
        $table = self::$table;
        $query = "create schema IF NOT EXISTS $dbname collate utf8_general_ci;
                use $dbname;
             
                create table $table
                (
                body text not null
                );
        ";

        $generalConn->multi_query($query);
    }

    public static function insertSentences(array $sentences)
    {
        $table = self::$table;

        $sql = "insert into $table (body) VALUES ";
        foreach ($sentences as $sentence) {
            $sql .= "('$sentence'),";
        }
        $sql = rtrim($sql, ',');
        self::exec($sql);

        self::deleteDuplicates();

        return true;
    }

    public static function exec($query)
    {
        $result = self::$conn->query($query, MYSQLI_USE_RESULT);
        if (!$result) {
            printf("Errormessage: %s\n", self::$conn->error);
        }
        if (gettype($result) != 'boolean') {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }


    protected static function deleteDuplicates()
    {
        $table = self::$table;
        $dbname = self::$dbname;

        $query = "
                create table temp as select body from $table group by body;
                drop table $table;
                rename table temp to $table;
         ";

        $response = self::$conn->multi_query($query);
    }

}