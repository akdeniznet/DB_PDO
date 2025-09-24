### PDO

PDO ile yazılmış pratik ve kullanımı kolay bir veritabanı sınıfı.  
Veritabanındaki tablolarla çalışırken CRUD işlemlerini hızlıca yapabilirsiniz.

> Not: Veritabanından gelen veri **object** olarak döner.  
> `$post->title` şeklinde kullanmalısınız.

Örnek Akış

- Db::connect() ile veritabanına bağlan.
- Db::getOne() veya Db::getAll() ile veri çek.
- Db::insert() ile veri ekle.
- Db::update() ile veri güncelle.
- Db::delete() ile veri sil.
- Db::count() veya Db::execCount() ile satır sayısını al.
- 
---
### Bağlantı Kurma

```php
Db::connect("db1", "localhost", "veritabani1", "root", "1234");
Db::connect("db2", "localhost", "veritabani2", "root", "1234");

$db1 = Db::getConnection("db1");
$db2 = Db::getConnection("db2");

```

---

### Primary Key Ayarı

```php
Db::setPrimaryKey('id'); // Varsayılan id, değiştirilebilir

```

---


### Bağlantı Kurma ve Ayarlar

```php
$db = new Database('localhost', 'veritabani', 'root', '1234');
$db->setPrimaryKey('id'); // Varsayılan id, değiştirilebilir


[Database Class]
       |
       |--- getOne / getId / execOne ---> Tek satır veri çek
       |
       |--- getAll / execAll ----------> Çoklu veri çek
       |
       |--- insert -------------------> Veri ekle
       |
       |--- update -------------------> Veri güncelle
       |
       |--- delete -------------------> Veri sil
       |
       |--- count / execCount --------> Satır sayısı
       Örnek Kullanım

```
---

### Tekil Veri Çekme (getOne)

#### getOne

```php
$user = Db::getOne("db1", "users", "WHERE id=?", [1]);
echo $user->name;

``` 

#### getId

```php
$user = Db::getId("db1", "users", 1);
echo $user->name;

```

#### execOne

```php
$user = Db::execOne("db1", "SELECT * FROM users WHERE id=?", [1]);
echo $user->name;

```

### Çoklu Veri Çekme

#### getAll
```php
$users = Db::getAll("db1", "users", "WHERE active=?", [1]);
foreach ($users as $user) {
    echo $user->name . "<br>";
    
```

#### execAll

```php
$users = Db::execAll("db1", "SELECT * FROM users WHERE active=?", [1]);
foreach ($users as $user) {
    echo $user->name . "<br>";
}

```

#### execAll LIKE Komutu

```php
$users = Db::execAll("db1", "SELECT * FROM users WHERE active=? AND name LIKE ?", [1, '%Ahmet%']);
foreach ($users as $user) {
    echo $user->name . "<br>";
}

```

---

### Veri Ekleme

```php
$insertId = Db::insert("db1", "users", [
    'name' => 'Ahmet',
    'email' => 'ahmet@example.com'
]);

echo $insertId ? $insertId . " eklendi" : "Eklenemedi";

```
---

### Veri Güncelleme

#### ID ile Güncelleme

```php
$update = Db::update("db1", "users", 1, [
    'name' => 'Mehmet',
    'email' => 'mehmet@example.com'
]);

echo $update ? "Başarıyla güncellendi" : "Güncellenemedi";

```

### Şart ve Parametre ile Güncelleme

```php
$update = Db::update("db1", "users", 0, [
    'name' => 'Veli'
], "WHERE email=?", ["veli@example.com"]);

echo $update ? "Başarıyla güncellendi" : "Güncellenemedi";

```
---

### Veri Silme

#### ID ile Silme

```php
$delete = Db::delete("db1", "users", 1);
echo $delete ? "Başarıyla silindi" : "Silinemedi";

```

#### Şart ve Parametre ile Silme

```php
$delete = Db::delete("db1", "users", 0, "WHERE email=?", ["veli@example.com"]);
echo $delete ? "Başarıyla silindi" : "Silinemedi";

```

---

### Satır Sayısı Alma

#### count

```php
$total = Db::count("db1", "users");
echo "Toplam " . $total . " kullanıcı mevcut";
```
#### count ile Şartlı Sayım

```php
$active = Db::count("db1", "users", "WHERE active=?", [1]);
echo "Aktif kullanıcı sayısı: " . $active;

```

#### execCount

```php
$drafts = Db::execCount("db1", "SELECT * FROM users WHERE active=0");
echo "Taslak kullanıcı sayısı: " . $drafts;
```
