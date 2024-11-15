<?php
class MessagesDB extends SQLite3 {

    function __construct()
    {
        $this->open(__DIR__ . "/main.db");
        $this->exec("CREATE TABLE IF NOT EXISTS messages (id INTEGER PRIMARY KEY, author INTEGER, content TEXT)");
    }

    function get_message_history(): array
    {
        $res = $this->query("SELECT * FROM messages ORDER BY id DESC LIMIT 100");
        if ($res === false) {
            exit(500);
        }
        $ret = array();
        while (true) {
            $row = $res->fetchArray(SQLITE3_ASSOC);
            if ($row === false) break;
            array_push($ret, array(
                "id" => intval($row["id"]),
                "author" => intval($row["author"]),
                "content" => strval($row["content"])
            ));
        }
        return array_reverse($ret);
    }

    function add_message(int $author, string $content): bool {
        return $this->exec("INSERT INTO messages (author, content) VALUES (" . $author . ", '" . $this::escapeString($content) . "')");
    }

}