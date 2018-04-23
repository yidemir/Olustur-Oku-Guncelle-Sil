# Olustur-Oku-Guncelle-Sil
PDO ile çalışan CRUD sınıfı (Oluştur, Oku, Güncelle, Sil)

# Bağlantıyı Sağlama
```php
$pdo = new \PDO('mysql:host=localhost;dbname=database', 'root', 'root');
$crud = new Crud($pdo);

// Şimdi kullanılabilir
```

# Veri elde etme
```php
Crud::query('select * from posts')->fetchAll(); // Veriler
Crud::query('select * from posts where id=?', [$id])->fetch(); // Veri
Crud::query('select * from posts')->rowCount(); // Satır sayısı
Crud::query('select count(*) from posts')->fetchColumn(); // Satır sayısı
```

# Veri oluşturma
```php
Crud::insert('posts', ['title' => 'lorem lipsum', 'body' => 'foo bar']);
// INSERT INTO posts (title,body) VALUES (?,?)

Crud::insert('posts', ['slug' => 'lorem-lipsum', 'title' => 'Lorem lipsum'], ['slug' => 'lorem-lipsum-2']);
// INSERT INTO posts (slug,title) VALUES (?,?) ON DUPLICATE KEY UPDATE slug=?
```

# Veri güncelleme
```php
Crud::update('posts', ['title' => 'Yeni başlık'], ['id' => $id]);
// UPDATE posts SET title=? WHERE id=?

// ya da
Crud::update('posts', ['title' => 'foo bar'], 'where id=?', [5]);

// ya da etkilenen satırları almak için
$rowCount = Crud::update('posts', ['title' => 'Lorem lipsum'], ['is_published' => 1])->rowCount();
```

# Veri silme
```php
Crud::delete('posts', ['id' => 5]);

// ya da
Crud::delete('posts', 'where id=?', [5]);

// ya da etkilenen satırları almak için
$rowCount = Crud::delete('posts', ['is_published' => 1])->rowCount();
echo "$rowCount satır silindi";
```
