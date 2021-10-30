<?php

class SQLite3Session implements SessionHandlerInterface
{
    private $db_path;

    public function open($path, $id)
    {
        $this->db_path = $path;

        try
        {
            $db = new SQLite3($this->db_path);
        }
        catch (Exception $e)
        {
            return false;
        }

        $ok = $db->exec("CREATE TABLE IF NOT EXISTS phpsessions (id TEXT NOT NULL, content TEXT, lastedit INTEGER)");
        if (!$ok)
        {
            return false;
        }

        $ok = $db->exec("CREATE UNIQUE INDEX IF NOT EXISTS phpsessionbyid ON phpsessions(id)");
        if (!$ok)
        {
            return false;
        }

        if (!$db->close())
        {
            return false;
        }

        return true;
    }

    public function read($id)
    {
        try
        {
            $db = new SQLite3($this->db_path, SQLITE3_OPEN_READONLY);
        }
        catch (Exception $e)
        {
            return false;
        }

        $statement = $db->prepare("SELECT content FROM phpsessions WHERE id=:id");
        if (!$statement)
        {
            return false;
        }

        $statement->bindValue(":id", $id, SQLITE3_TEXT);

        $res = $statement->execute();
        if (!$res)
        {
            return false;
        }

        $data = $res->fetchArray(SQLITE3_ASSOC);
        if (!$data)
        {
            return "";
        }

        if (!$db->close())
        {
            return false;
        }

        return $data["content"];
    }

    public function write($id, $data)
    {
        try
        {
            $db = new SQLite3($this->db_path, SQLITE3_OPEN_READWRITE);
        }
        catch (Exception $e)
        {
            return false;
        }

        $statement = $db->prepare("INSERT OR REPLACE INTO phpsessions (id, content, lastedit) VALUES (:id, :content, :lastedit)");
        if (!$statement)
        {
            return false;
        }

        $statement->bindValue(":id", $id, SQLITE3_TEXT);
        $statement->bindValue(":content", $data, SQLITE3_TEXT);
        $statement->bindValue(":lastedit", time(), SQLITE3_INTEGER);

        $ret = $statement->execute();

        if (!$db->close())
        {
            return false;
        }

        return $ret===false?false:true; // If ret is false, return that, otherwise return true (and not a SQLite3Result)
    }

    public function close()
    {
        return true;
    }

    public function destroy($id)
    {
        try
        {
            $db = new SQLite3($this->db_path, SQLITE3_OPEN_READWRITE);
        }
        catch (Exception $e)
        {
            return false;
        }

        $statement = $db->prepare("DELETE FROM phpsessions WHERE id=:id");
        if (!$statement)
        {
            return false;
        }

        $statement->bindValue(":id", $id, SQLITE3_TEXT);

        $ret = $statement->execute();

        if (!$db->close())
        {
            return false;
        }

        return $ret===false?false:true; // If ret is false, return that, otherwise return true (and not a SQLite3Result)
    }

    public function gc($lifetime)
    {
        try
        {
            $db = new SQLite3($this->db_path, SQLITE3_OPEN_READWRITE);
        }
        catch (Exception $e)
        {
            return false;
        }

        $statement = $db->prepare("DELETE FROM phpsessions WHERE lastedit<=:lastedit");
        if (!$statement)
        {
            return false;
        }

        $statement->bindValue(":lastedit", time()-$lifetime, SQLITE3_INTEGER);

        $result = $statement->execute();
        if (!$result)
        {
            return false;
        }

        $count = $db->changes();

        if (!$db->close())
        {
            return false;
        }

        return $count;
    }
}

?>