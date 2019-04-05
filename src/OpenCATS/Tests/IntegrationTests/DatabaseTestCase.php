<?php
namespace OpenCATS\Tests\IntegrationTests;

use PHPUnit\Framework\TestCase;
class DatabaseTestCase extends TestCase


//    $preserveGlobalState = FALSE;
//   $runTestInSeparateProcess = TRUE;

    //setup and teardown functions
//    protected function setUp() { }
//    protected function tearDown() { }


{
    private $connection;

    function setUp() 
    {
        global $mySQLConnection;
        parent::setUp();
        include_once('./constants.php');
	include_once('./config.php');
	include_once('./lib/DatabaseConnection.php');
	 if(!defined('DATABASE_NAME')){
		define('DATABASE_NAME', 'cats_integrationtest');
	 }
         if(!defined('DATABASE_HOST')){
		define('DATABASE_HOST', 'integrationtestdb');
	 }
//        include_once('./config.php');
//        include_once('./lib/DatabaseConnection.php');
        $mySQLConnection = @mysqli_connect(
            DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME
            );
        if (!$mySQLConnection)
        {
            throw new \Exception('Error connecting to the mysql server');
        }
	print "connected successfully\n";
        $this->mySQLQuery('DROP DATABASE IF EXISTS ' . DATABASE_NAME);
        $this->mySQLQuery('CREATE DATABASE ' . DATABASE_NAME);

        @mysqli_select_db(DATABASE_NAME, $mySQLConnection);
	print "database selected\n";
        $this->mySQLQueryMultiple(file_get_contents('db/cats_schema.sql'), ";\n");
    }
//	print "mysql queried\n";
//    }
    // TODO: remove duplicated code
    private function MySQLQueryMultiple($SQLData, $delimiter = ';')
    {
        $SQLStatments = explode($delimiter, $SQLData);

        foreach ($SQLStatments as $SQL)
        {
            $SQL = trim($SQL);

            if (empty($SQL))
            {
                continue;
            }

            $this->mySQLQuery($SQL);
        }
    }

    private function mySQLQuery($query, $ignoreErrors = true)
    {
        global $mySQLConnection;

        $queryResult = mysqli_query($mySQLConnection, $query);
        if (!$queryResult && !$ignoreErrors)
        {
            $error = mysqli_error($mySQLConnection);

            if ($error == 'Query was empty')
            {
                return $queryResult;
            }

            die (
                '<p style="background: #ec3737; padding: 4px; margin-top: 0; font:'
                . ' normal normal bold 12px/130% Arial, Tahoma, sans-serif;">Query'
                . " Error -- Please Report This Bug!</p><pre>\n\nMySQL Query "
                . "Failed: " . $error . "\n\n" . $query . "</pre>\n\n"
                );
        }

        return $queryResult;
    }


    function tearDown()
    {
        $this->mySQLQuery('DROP DATABASE IF EXISTS ' . DATABASE_NAME);
    }
}
?>
