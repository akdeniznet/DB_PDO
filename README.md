### PDO

PDO ile yazılmış pratik ve kullanımı kolay bir veritabanı sınıfı.  
Veritabanındaki tablolarla çalışırken CRUD işlemlerini hızlıca yapabilirsiniz.

> Not: Veritabanından gelen veri **object** olarak döner.  
> `$post->title` şeklinde kullanmalısınız.

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
    
```

### Veri Ekleme
$insert = Database::insert('post', [
    'title' => 'Yeni Gönderi',
    'body'  => 'Gönderi içeriği',
    'draft' => 0
]);

echo $insert ? $insert . ' eklendi' : 'Eklenemedi';

Veri Güncelleme

ID'ye göre güncelleme:

$update = Database::update('post', 1, [
    'title' => 'Güncellenmiş Başlık',
    'body'  => 'Yeni içerik'
]);

echo $update ? 'Başarıyla güncellendi' : 'Güncellenemedi';


ID olmadan şart ve parametrelerle güncelleme:

$update = Database::update('post', 0, [
    'title' => 'Yeni Başlık',
    'body'  => 'Düzenlenen içerik'
], 'WHERE title = ?', array('Eski Başlık'));

echo $update ? 'Başarıyla güncellendi' : 'Güncellenemedi';

Veri Silme

ID ile silme:

$delete = Database::delete('post', 1);
echo $delete ? 'Başarıyla silindi' : 'Silinemedi';


Şart ve parametrelerle silme:

$delete = Database::delete('post', 0, 'WHERE title = ?', array('Silinmelik Gönderi'));
echo $delete ? 'Başarıyla silindi' : 'Silinemedi';

Toplam Satır Sayısı Alma
$count = Database::count('post');
echo $count ? 'Toplam ' . $count . ' gönderi mevcut' : 'Henüz hiç gönderi yok';


Şartlı sayım:

$count = Database::count('post', 'WHERE draft = ?', array(0));
echo $count ? 'Toplam ' . $count . ' yayımda olan gönderi mevcut' : 'Henüz hiç yayımlanmış gönderi yok';


SQL ile manuel sayım:

$count = Database::execCount('SELECT * FROM post WHERE draft = ?', array(1));
echo $count ? 'Toplam ' . $count . ' taslak olan gönderi mevcut' : 'Hiç taslak gönderi yok';
