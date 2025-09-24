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

---

## Tekil Veri Çekme (getOne)

```php
$post = Database::getOne('post', 'WHERE draft = ?', array(0));
echo $post->title;

## Tekil Veri Çekme (execOne)

```php
$post = Database::execOne('SELECT * FROM post WHERE created = ?', array('2025-09-24 10:00'));
echo $post->title;

Tekil Veri Çekme (getId)
$category = Database::getId('category', 5);
echo $category->name;

Çoklu Veri Çekme (getAll)
$posts = Database::getAll('post', 'WHERE draft = ?', array(0));
foreach ($posts as $post) {
    echo $post->title . '<br>';
}

Çoklu Veri Çekme (execAll)
$posts = Database::execAll('SELECT * FROM post WHERE draft = ?', array(0));
foreach ($posts as $post) {
    echo $post->title . '<br>';
}

Çoklu Veri Çekme LIKE Komutu
$posts = Database::execAll('SELECT * FROM post WHERE draft = ? AND title LIKE ?', array(0, '%Gönderi%'));
foreach ($posts as $post) {
    echo $post->title . '<br>';
}

Veri Ekleme
$insert = Database::insert('post', [
    'title' => 'Yeni Gönderi',
    'body'  => 'Gönderi içeriği',
    'draft' => 0
]);

echo $insert ? $insert . ' eklendi' : 'Eklenemedi';
