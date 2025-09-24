<?php
Db::connect("db1", "localhost", "veritabani1", "root", "1234");
Db::connect("db2", "localhost", "veritabani2", "root", "1234");

// $user = Db::getOne("db1", "users", "WHERE id=?", [1]);

class Db
{
    private static array $connections = [];
    private static ?PDOStatement $query = null;
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

    /**
     * Bağlantıyı getir
     */
    public static function getConnection(string $name): ?PDO
    {
        return self::$connections[$name] ?? null;
    }

    /**
     * Tek satır getir
     */
    public static function getOne(string $name, string $table, string $conditions = "", array $parameters = []): ?object
    {
        $sql = "SELECT * FROM `$table` $conditions LIMIT 1";
        self::$query = self::$connections[$name]->prepare($sql);
        self::$query->execute($parameters);
        return self::$query->fetch() ?: null;
    }

    /**
     * ID ile tek satır getir
     */
    public static function getId(string $name, string $table, int $id): ?object
    {
        $sql = "SELECT * FROM `$table` WHERE `" . self::$pk . "`=? LIMIT 1";
        self::$query = self::$connections[$name]->prepare($sql);
        self::$query->execute([$id]);
        return self::$query->fetch() ?: null;
    }

    /**
     * Custom tek satır sorgu
     */
    public static function execOne(string $name, string $query, array $parameters = []): ?object
    {
        self::$query = self::$connections[$name]->prepare($query);
        self::$query->execute($parameters);
        return self::$query->fetch() ?: null;
    }

    /**
     * Çoklu satır getir
     */
    public static function getAll(string $name, string $table, string $conditions = "", array $parameters = []): array
    {
        $sql = "SELECT * FROM `$table` $conditions";
        self::$query = self::$connections[$name]->prepare($sql);
        self::$query->execute($parameters);
        return self::$query->fetchAll();
    }

    /**
     * Custom çoklu sorgu
     */
    public static function execAll(string $name, string $query, array $parameters = []): array
    {
        self::$query = self::$connections[$name]->prepare($query);
        self::$query->execute($parameters);
        return self::$query->fetchAll();
    }

    /**
     * Insert
     */
    public static function insert(string $name, string $table, array $data): int|false
    {
        $columns = array_keys($data);
        $values = array_values($data);

        $colString = implode(',', array_map(fn($c) => "`$c`", $columns));
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $sql = "INSERT INTO `$table` ($colString) VALUES ($placeholders)";
        self::$query = self::$connections[$name]->prepare($sql);

        if (self::$query->execute($values)) {
            return (int) self::$connections[$name]->lastInsertId();
        }
        return false;
    }

    /**
     * Update
     */
    public static function update(string $name, string $table, ?int $id, array $data, string $conditions = "", array $parameters = []): bool|int
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $setString = implode('=?, ', array_map(fn($c) => "`$c`", $columns)) . '=?';

        // ID varsa
        if ($id) {
            $sql = "UPDATE `$table` SET $setString WHERE `" . self::$pk . "`=?";
            self::$query = self::$connections[$name]->prepare($sql);
            if (self::$query->execute([...$values, $id])) {
                return $id;
            }
            return false;
        }

        // Conditions varsa
        if ($conditions) {
            $sql = "UPDATE `$table` SET $setString $conditions";
            self::$query = self::$connections[$name]->prepare($sql);
            return self::$query->execute([...$values, ...$parameters]);
        }

        return false;
    }

    /**
     * Delete
     */
    public static function delete(string $name, string $table, ?int $id = null, string $conditions = "", array $parameters = []): bool|int
    {
        if ($id) {
            $sql = "DELETE FROM `$table` WHERE `" . self::$pk . "`=?";
            self::$query = self::$connections[$name]->prepare($sql);
            if (self::$query->execute([$id])) {
                return $id;
            }
            return false;
        }

        if ($conditions) {
            $sql = "DELETE FROM `$table` $conditions";
            self::$query = self::$connections[$name]->prepare($sql);
            return self::$query->execute($parameters);
        }

        return false;
    }

    /**
     * Count
     */
    public static function count(string $name, string $table, string $conditions = "", array $parameters = []): int
    {
        $sql = "SELECT COUNT(*) FROM `$table` $conditions";
        self::$query = self::$connections[$name]->prepare($sql);
        self::$query->execute($parameters);
        return (int) self::$query->fetchColumn();
    }

    /**
     * Custom Count
     */
    public static function execCount(string $name, string $query, array $parameters = []): int
    {
        self::$query = self::$connections[$name]->prepare($query);
        self::$query->execute($parameters);
        return (int) self::$query->fetchColumn();
    }

    /**
     * PK değiştir
     */
    public static function setPrimaryKey(string $pk): void
    {
        self::$pk = $pk;
    }

    /**
     * Bağlantı var mı
     */
    public static function is_connected(string $name): bool
    {
        return isset(self::$connections[$name]) && self::$connections[$name] instanceof PDO;
    }
}

// --- Örnek Kullanım ---
