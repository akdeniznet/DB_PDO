# DB_PDO
# Pratik-PDO

PDO ile yazılmış pratik ve kullanımı kolay bir veritabanı sınıfı.  

## Açıklama
Veritabanındaki tablolarla çalışırken en çok yaptığımız işlemler CRUD (veri ekleme, okuma, güncelleme ve silme) işlemleridir.  
Bu sınıf sayesinde veri çekme/okuma, ekleme, silme ve düzenleme işlemlerini kolayca yapabilirsiniz.  

> Not: Veritabanından gelen veri **dizi (array)** değil, **obje (object)** olarak döner.  
> Örneğin: `$post['title']` yerine `$post->title` olarak kullanmalısınız.  

---

## Bağlantı Kurma ve Ayarlar

```php
$db = new Database('localhost', 'Veritabanı adı', 'Kullanıcı adı', 'Şifre');
