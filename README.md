# Pratik-PDO

PDO ile yazılmış pratik ve kullanımı kolay bir veritabanı sınıfı.  
Veritabanındaki tablolarla çalışırken CRUD işlemlerini hızlıca yapabilirsiniz.

> Not: Veritabanından gelen veri **object** olarak döner.  
> `$post->title` şeklinde kullanmalısınız.

---

## Örnek Tablo: `post`

| id  | title              | body                     | draft | created             |
|-----|------------------|-------------------------|-------|--------------------|
| 1   | İlk Gönderi       | İçerik 1               | 0     | 2025-09-24 10:00   |
| 2   | İkinci Gönderi    | İçerik 2               | 1     | 2025-09-24 11:00   |

---

## Bağlantı Kurma ve Ayarlar

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

```php
$post = Database::getOne('post', 'WHERE draft = ?', array(0));
echo $post->title;
``` 

### Tekil Veri Çekme (execOne)

```php
$post = Database::execOne('SELECT * FROM post WHERE created = ?', array('2025-09-24 10:00'));
echo $post->title;
```

### Tekil Veri Çekme (getId)

```php
$category = Database::getId('category', 5);
echo $category->name;
```

### Çoklu Veri Çekme (getAll)

```php
$posts = Database::getAll('post', 'WHERE draft = ?', array(0));
foreach ($posts as $post) {
    echo $post->title . '<br>';
}
```

### Çoklu Veri Çekme (execAll)

```php
$posts = Database::execAll('SELECT * FROM post WHERE draft = ?', array(0));
foreach ($posts as $post) {
    echo $post->title . '<br>';
}
```

### Çoklu Veri Çekme LIKE Komutu

```php
$posts = Database::execAll('SELECT * FROM post WHERE draft = ? AND title LIKE ?', array(0, '%Gönderi%'));
foreach ($posts as $post) {
    echo $post->title . '<br>';
}
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
