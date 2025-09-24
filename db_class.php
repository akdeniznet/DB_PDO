<?php

class Db
{
    private static array $connections = [];
    private static string $pk = 'id';

    /**
     * PDO bağlantısı kur
     */
    public static function connect(string $name, string $host, string $db, string $user, string $pass): void
    {
        self::$connections[$name] = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]
        );
    }

    public static function getConnection(string $name): ?PDO
    {
        return self::$connections[$name] ?? null;
    }

    // -------------------- Transaction Metodları --------------------

    public static function beginTransaction(string $name): bool
    {
        return self::$connections[$name]->beginTransaction();
    }

    public static function commit(string $name): bool
    {
        return self::$connections[$name]->commit();
    }

    public static function rollback(string $name): bool
    {
        return self::$connections[$name]->rollBack();
    }

    // -------------------- CRUD --------------------

    public static function getOne(string $name, string $table, string $conditions = "", array $parameters = []): ?object
    {
        $sql = "SELECT * FROM `$table` $conditions LIMIT 1";
        $stmt = self::$connections[$name]->prepare($sql);
        $stmt->execute($parameters);
        return $stmt->fetch() ?: null;
    }

    public static function getId(string $name, string $table, int $id): ?object
    {
        $sql = "SELECT * FROM `$table` WHERE `" . self::$pk . "`=? LIMIT 1";
        $stmt = self::$connections[$name]->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function execOne(string $name, string $query, array $parameters = []): ?object
    {
        $stmt = self::$connections[$name]->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetch() ?: null;
    }

    public static function getAll(string $name, string $table, string $conditions = "", array $parameters = []): array
    {
        $sql = "SELECT * FROM `$table` $conditions";
        $stmt = self::$connections[$name]->prepare($sql);
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    public static function execAll(string $name, string $query, array $parameters = []): array
    {
        $stmt = self::$connections[$name]->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    public static function insert(string $name, string $table, array $data): int|false
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $colString = implode(',', array_map(fn($c) => "`$c`", $columns));
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $sql = "INSERT INTO `$table` ($colString) VALUES ($placeholders)";
        $stmt = self::$connections[$name]->prepare($sql);

        if ($stmt->execute($values)) {
            return (int) self::$connections[$name]->lastInsertId();
        }
        return false;
    }

    public static function update(string $name, string $table, ?int $id, array $data, string $conditions = "", array $parameters = []): bool|int
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $setString = implode('=?, ', array_map(fn($c) => "`$c`", $columns)) . '=?';

        if ($id) {
            $sql = "UPDATE `$table` SET $setString WHERE `" . self::$pk . "`=?";
            $stmt = self::$connections[$name]->prepare($sql);
            return $stmt->execute([...$values, $id]) ? $id : false;
        }

        if ($conditions) {
            $sql = "UPDATE `$table` SET $setString $conditions";
            $stmt = self::$connections[$name]->prepare($sql);
            return $stmt->execute([...$values, ...$parameters]);
        }

        return false;
    }

    public static function delete(string $name, string $table, ?int $id = null, string $conditions = "", array $parameters = []): bool|int
    {
        if ($id) {
            $sql = "DELETE FROM `$table` WHERE `" . self::$pk . "`=?";
            $stmt = self::$connections[$name]->prepare($sql);
            return $stmt->execute([$id]) ? $id : false;
        }

        if ($conditions) {
            $sql = "DELETE FROM `$table` $conditions";
            $stmt = self::$connections[$name]->prepare($sql);
            return $stmt->execute($parameters);
        }

        return false;
    }

    public static function count(string $name, string $table, string $conditions = "", array $parameters = []): int
    {
        $sql = "SELECT COUNT(*) FROM `$table` $conditions";
        $stmt = self::$connections[$name]->prepare($sql);
        $stmt->execute($parameters);
        return (int) $stmt->fetchColumn();
    }

    public static function execCount(string $name, string $query, array $parameters = []): int
    {
        $stmt = self::$connections[$name]->prepare($query);
        $stmt->execute($parameters);
        return (int) $stmt->fetchColumn();
    }

    public static function setPrimaryKey(string $pk): void
    {
        self::$pk = $pk;
    }

    public static function is_connected(string $name): bool
    {
        return isset(self::$connections[$name]) && self::$connections[$name] instanceof PDO;
    }
}

// -------------------- Örnek Kullanım --------------------
Db::connect("db1", "localhost", "veritabani1", "root", "1234");

try {
    Db::beginTransaction("db1");

    $userId = Db::insert("db1", "users", [
        "name" => "Ali",
        "email" => "ali@example.com"
    ]);

    Db::update("db1", "users", $userId, ["email" => "ali2@example.com"]);

    Db::commit("db1");
} catch (Exception $e) {
    Db::rollback("db1");
    echo "Hata: " . $e->getMessage();
}
