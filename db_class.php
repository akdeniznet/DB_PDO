<?php

class Db
{
    private static array $connections = []; // Tüm PDO bağlantılarını saklar
    private static string $pk = 'id';      // Varsayılan primary key

    /**
     * PDO bağlantısı kurar
     * @param string $name Bağlantı adı
     * @param string $host Sunucu adresi
     * @param string $db Veritabanı adı
     * @param string $user Kullanıcı adı
     * @param string $pass Parola
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
     * Bağlantıyı getirir
     * @param string $name Bağlantı adı
     * @return PDO|null
     */
    public static function getConnection(string $name): ?PDO
    {
        return self::$connections[$name] ?? null;
    }

    /**
     * Tek satır veri getirir
     * @param string $name Bağlantı adı
     * @param string $table Tablo adı
     * @param string $conditions SQL koşulları (örn. WHERE id=?)
     * @param array $parameters Parametreler
     * @return object|null
     */
    public static function getOne(string $name, string $table, string $conditions = "", array $parameters = []): ?object
    {
        $stmt = self::$connections[$name]->prepare("SELECT * FROM `$table` $conditions LIMIT 1");
        $stmt->execute($parameters);
        return $stmt->fetch() ?: null;
    }

    /**
     * ID ile tek satır veri getirir
     * @param string $name Bağlantı adı
     * @param string $table Tablo adı
     * @param int $id ID değeri
     * @return object|null
     */
    public static function getId(string $name, string $table, int $id): ?object
    {
        $stmt = self::$connections[$name]->prepare("SELECT * FROM `$table` WHERE `" . self::$pk . "`=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Özel tek satır sorgu çalıştırır
     * @param string $name Bağlantı adı
     * @param string $query SQL sorgusu
     * @param array $parameters Parametreler
     * @return object|null
     */
    public static function execOne(string $name, string $query, array $parameters = []): ?object
    {
        $stmt = self::$connections[$name]->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetch() ?: null;
    }

    /**
     * Çoklu satır getirir
     * @param string $name Bağlantı adı
     * @param string $table Tablo adı
     * @param string $conditions SQL koşulları
     * @param array $parameters Parametreler
     * @return array
     */
    public static function getAll(string $name, string $table, string $conditions = "", array $parameters = []): array
    {
        $stmt = self::$connections[$name]->prepare("SELECT * FROM `$table` $conditions");
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * Özel çoklu sorgu çalıştırır
     * @param string $name Bağlantı adı
     * @param string $query SQL sorgusu
     * @param array $parameters Parametreler
     * @return array
     */
    public static function execAll(string $name, string $query, array $parameters = []): array
    {
        $stmt = self::$connections[$name]->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    /**
     * Veritabanına veri ekler
     * @param string $name Bağlantı adı
     * @param string $table Tablo adı
     * @param array $data Eklenecek veri [kolon => değer]
     * @return int|false Insert ID veya false
     */
    public static function insert(string $name, string $table, array $data): int|false
    {
        $cols = implode(',', array_map(fn($c) => "`$c`", array_keys($data)));
        $phs = implode(',', array_fill(0, count($data), '?'));
        $stmt = self::$connections[$name]->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)");
        return $stmt->execute(array_values($data)) ? (int) self::$connections[$name]->lastInsertId() : false;
    }

    /**
     * Veriyi günceller
     * @param string $name Bağlantı adı
     * @param string $table Tablo adı
     * @param int|null $id ID değeri
     * @param array $data Güncellenecek veri [kolon => değer]
     * @param string $conditions Koşullar (ID yoksa)
     * @param array $parameters Koşul parametreleri
     * @return bool|int Güncellenen ID veya false
     */
    public static function update(string $name, string $table, ?int $id, array $data, string $conditions = "", array $parameters = []): bool|int
    {
        $set = implode('=?, ', array_map(fn($c) => "`$c`", array_keys($data))) . '=?';

        if ($id) {
            $stmt = self::$connections[$name]->prepare("UPDATE `$table` SET $set WHERE `" . self::$pk . "`=?");
            return $stmt->execute([...array_values($data), $id]) ? $id : false;
        }

        if ($conditions) {
            $stmt = self::$connections[$name]->prepare("UPDATE `$table` SET $set $conditions");
            return $stmt->execute([...array_values($data), ...$parameters]);
        }

        return false;
    }

    /**
     * Veriyi siler
     * @param string $name Bağlantı adı
     * @param string $table Tablo adı
     * @param int|null $id ID değeri
     * @param string $conditions Koşullar (ID yoksa)
     * @param array $parameters Koşul parametreleri
     * @return bool|int Silinen ID veya false
     */
    public static function delete(string $name, string $table, ?int $id = null, string $conditions = "", array $parameters = []): bool|int
    {
        if ($id) {
            $stmt = self::$connections[$name]->prepare("DELETE FROM `$table` WHERE `" . self::$pk . "`=?");
            return $stmt->execute([$id]) ? $id : false;
        }

        if ($conditions) {
            $stmt = self::$connections[$name]->prepare("DELETE FROM `$table` $conditions");
            return $stmt->execute($parameters);
        }

        return false;
    }

    /**
     * Satır sayısını getirir
     * @param string $name Bağlantı adı
     * @param string $table Tablo adı
     * @param string $conditions Koşullar
     * @param array $parameters Parametreler
     * @return int
     */
    public static function count(string $name, string $table, string $conditions = "", array $parameters = []): int
    {
        $stmt = self::$connections[$name]->prepare("SELECT COUNT(*) FROM `$table` $conditions");
        $stmt->execute($parameters);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Özel count sorgusu çalıştırır
     * @param string $name Bağlantı adı
     * @param string $query SQL sorgusu
     * @param array $parameters Parametreler
     * @return int
     */
    public static function execCount(string $name, string $query, array $parameters = []): int
    {
        $stmt = self::$connections[$name]->prepare($query);
        $stmt->execute($parameters);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Primary key değiştirir
     * @param string $pk
     */
    public static function setPrimaryKey(string $pk): void
    {
        self::$pk = $pk;
    }

    /**
     * Bağlantı var mı kontrol eder
     * @param string $name Bağlantı adı
     * @return bool
     */
    public static function is_connected(string $name): bool
    {
        return isset(self::$connections[$name]) && self::$connections[$name] instanceof PDO;
    }
}

// --- Örnek Kullanım ---
Db::connect("db1", "localhost", "veritabani1", "root", "1234");
Db::connect("db2", "localhost", "veritabani2", "root", "1234");

// Tek satır veri çekme
$user = Db::getOne("db1", "users", "WHERE id=?", [1]);

// Veri ekleme
$newId = Db::insert("db1", "users", ["name"=>"Ali", "email"=>"ali@example.com"]);

// Veri güncelleme
Db::update("db1", "users", $newId, ["email"=>"ali2@example.com"]);

// Veri silme
Db::delete("db1", "users", $newId);

// Satır sayısı
$count = Db::count("db1", "users");
