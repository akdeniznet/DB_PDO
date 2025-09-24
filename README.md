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
