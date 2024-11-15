<?php
class UserData {

    public $id;
    public $name;
    protected $security;

    function __construct(int $id, string $name, string $security) {
        $this->id = $id;
        $this->name = $name;
        $this->security = $security;
    }

    function check_password($password): bool {
        return password_verify($password, $this->security);
    }

}

class UsersDB extends SQLite3 {

    function __construct()
    {
        $this->open(__DIR__ . "/main.db");
        $this->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY, name VARCHAR(32) NOT NULL UNIQUE, security VARCHAR(60) NOT NULL)");
    }

    function get_user_by_id(int $id): UserData | false {
        $res = $this->querySingle("SELECT name, security FROM users WHERE id=" . $id, true);
        if ($res === false) {
            exit(500);
        }
        if ($res === NULL) {
            return false;
        }
        return new UserData(
            $id,
            strval($res["name"]),
            strval($res["security"])
        );
    }

    function get_user_by_name(string $name): UserData | false {
        $key = $this::escapeString(strtolower($name));
        $res = $this->querySingle("SELECT id, security FROM users WHERE name='" . $key . "'", true);
        if ($res === false) {
            exit(500);
        }
        if ($res === NULL) {
            return false;
        }
        return new UserData(
            intval($res["id"]),
            $name,
            strval($res["security"])
        );
    }

    // Password should not be hashed yet!
    function add_user(string $name, string $password): UserData | false {
        $key = $this::escapeString(strtolower($name));
        $security = password_hash($password, PASSWORD_BCRYPT);
        $res = $this->exec("INSERT INTO users (name, security) VALUES ('" . $key . "', '" . $this::escapeString($security) . "')");
        if (!$res) {
            return false;
        }
        $res = $this->querySingle("SELECT id FROM users WHERE name='" . $key . "'");
        if ($res === false) {
            exit(500);
        }
        if ($res === NULL) {
            return false;
        }
        return new UserData(
            intval($res),
            $name,
            $security
        );
    }

}