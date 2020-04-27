<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class concert
{
    public $conn;
        /**
         * Konstruktor se připojí k DB ASW
         */
    public function __construct($host, $port, $dbname, $user, $pass)
    {
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port";
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try
            {
                $this->conn = new PDO($dsn, $user, $pass, $options);
            }
        catch(PDOException $e)
            {
                echo "Nelze se připojit k MySQL: ";  echo $e->getMessage();
            }
    }

    public function getActualConcerts()
    {
        try {
            $dotaz = $this->conn->prepare("SELECT * FROM `koncerty2020` WHERE `datum` >= :datum ORDER BY `datum` ASC");
            $datum=date("Y-m-d");
            $dotaz->bindParam(':datum',$datum);
            $dotaz->execute();
            return $dotaz->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e)
        {
            echo "Chyba čtení tabulky: ";
            echo $e->getMessage();
        }
    }

    public function getConcert($id)
    {
        try {
            $dotaz = $this->conn->prepare("SELECT * FROM `koncerty2020` WHERE `id` = :id");
            $dotaz->bindParam(':id',$id);
            $dotaz->execute();
            return $dotaz->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e)
        {
            echo "Chyba čtení tabulky: ";
            echo $e->getMessage();
        }
    }

    public function getHistoricalConcerts($rok)
    {
        try {
            $dotaz = $this->conn->prepare("SELECT * FROM `koncerty2020` WHERE `datum` < :datum AND `datum` LIKE CONCAT(:rok, '-%') ORDER BY `datum`  DESC ");
            $date=date("Y-m-d");
            $dotaz->bindParam(':datum',$date);
            $dotaz->bindParam(':rok',$rok, PDO::PARAM_INT);
            $dotaz->execute();
            return $dotaz->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e)
        {
            echo "Chyba čtení tabulky: ";
            echo $e->getMessage();
        }
    }
    public function getNumberConcerts($rok)
    {
        try {
            $dotaz = $this->conn->prepare("SELECT COUNT(*) AS counts FROM `koncerty2020` WHERE `datum` < :datum AND `datum` LIKE CONCAT(:rok, '-%')");
            $date=date("Y-m-d");
            $dotaz->bindParam(':datum',$date);
            $dotaz->bindParam(':rok',$rok, PDO::PARAM_INT);
            $dotaz->execute();
            $temp=$dotaz->fetch(PDO::FETCH_OBJ);
            return $temp->counts;
        } catch (PDOException $e)
        {
            echo "Chyba čtení tabulky: ";
            echo $e->getMessage();
        }
    }
    public function addConcert($id,$date,$time,$where,$name,$note)
    {
        try
        {
            if ($id == "")
            {
                $stmt = $this->conn->prepare("INSERT INTO `koncerty2020` (`ID`,`datum`, `cas`, `kde`, `co`, `poznamka`) VALUES (NULL, :date, :time, :where, :name, :note);");
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':time', $time);
                $stmt->bindParam(':where', $where);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':note',$note);
                $stmt->execute();
            }
            else
            {
                $stmt = $this->conn->prepare("UPDATE `koncerty2020` SET `datum`= :date, `cas`= :time, `kde`= :where, `co`= :name, `poznamka`= :note WHERE id = :id;");

                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':time', $time);
                $stmt->bindParam(':where', $where);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':note',$note);
                //var_dump($stmt);
                $stmt->execute();
            };

        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
    }

}    

    public function deleteConcert($id)
    {
        try
        {
            $stmt = $this->conn->prepare("DELETE FROM `koncerty2020` WHERE id = :id;");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
    }

}

    public function verifyAdmin($name, $password)
    {
        $stmt = $this->conn->prepare("SELECT password FROM `credentials` WHERE username = :username ORDER BY id ASC;");
        $stmt->bindParam(':username', $name);
        $stmt->execute();
        if($stmt->rowCount() == 0)
            return false;
        $dotaz = $stmt->fetch(PDO::FETCH_OBJ);
        return(password_verify($password, $dotaz->password));
    }

}
?>