# TTG Jadantara — Dokumentasi REST API

API publik **read-only** untuk mengakses data artikel/tutorial TTG, kategori, dan berita.
Semua respons berformat JSON, dengan URL gambar & PDF dikembalikan sebagai **absolute URL** sehingga langsung bisa dipakai di aplikasi mobile/web tanpa konversi.

## Base URL

| Environment | URL |
|---|---|
| Lokal (dev) | `http://127.0.0.1:8888/api` |
| Produksi | `https://<domain-anda>/api` |

## Endpoint

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/index.php` | Info API + daftar semua endpoint |
| GET | `/api/tutorials.php` | Daftar artikel/tutorial (mendukung filter & pagination) |
| GET | `/api/tutorials.php?id={id}` | Detail satu artikel (lengkap: steps, materials, images) |
| GET | `/api/categories.php` | Daftar semua kategori + jumlah artikel |
| GET | `/api/berita.php` | Daftar berita (kategori `Berita`) |
| GET | `/api/berita.php?id={id}` | Detail satu berita |

> Semua endpoint **hanya menerima GET**. Method lain → `405 Method Not Allowed`.

---

## 1. `GET /api/tutorials.php`

### Parameter (query string)

| Param | Tipe | Default | Deskripsi |
|---|---|---|---|
| `id` | int | — | Ambil detail 1 artikel. Jika diisi, param lain diabaikan |
| `category_id` | int | — | Filter berdasarkan kategori |
| `difficulty` | string | — | Filter tingkat kesulitan (mis. `Easy`, `Medium`) |
| `limit` | int | 50 | Jumlah item per halaman (1–200) |
| `offset` | int | 0 | Jumlah item yang dilewati (untuk pagination) |

### Contoh request

```
GET /api/tutorials.php?limit=2&offset=0
GET /api/tutorials.php?category_id=9
GET /api/tutorials.php?id=110
```

### Response list (200)

```json
{
  "data": [
    {
      "id": 110,
      "title": "Selai Buah Pala",
      "slug": "selai-buah-pala",
      "description": "1\nSELAI PALA\n1.\nPENDAHULUAN\nSelai adalah bahan de…",
      "difficulty": "Medium",
      "duration": 20,
      "category": "Pengolahan",
      "category_id": 9,
      "created_at": "2026-04-09 10:57:35",
      "view_count": 0,
      "image_url": "http://127.0.0.1:8888/uploads/article_110.jpg",
      "pdf_url": "http://127.0.0.1:8888/uploads/pdfs/Selai Buah Pala.pdf",
      "video_url": null
    }
  ],
  "meta": {
    "total": 107,
    "limit": 1,
    "offset": 0,
    "count": 1
  }
}
```

### Pagination

Gunakan kombinasi `limit` + `offset`:

```
Halaman 1: ?limit=20&offset=0
Halaman 2: ?limit=20&offset=20
Halaman 3: ?limit=20&offset=40
```

Total item tersedia di `meta.total`; `meta.count` = jumlah item pada halaman ini.

---

## 2. `GET /api/tutorials.php?id={id}`

Detail lengkap satu artikel. Field tambahan dibanding list: `steps`, `materials`, `images`.

### Response detail (200)

```json
{
  "id": 110,
  "title": "Selai Buah Pala",
  "slug": "selai-buah-pala",
  "description": "…",
  "difficulty": "Medium",
  "duration": 20,
  "category": "Pengolahan",
  "category_id": 9,
  "created_at": "2026-04-09 10:57:35",
  "view_count": 0,
  "image_url": "http://host/uploads/article_110.jpg",
  "pdf_url": "http://host/uploads/pdfs/Selai Buah Pala.pdf",
  "video_url": null,
  "steps": [
    {
      "id": 1975,
      "step_number": 2,
      "title": "PENDAHULUAN",
      "content": "Selai adalah bahan dengan konsistensi gel…",
      "image_path": null,
      "image_url": null
    }
  ],
  "materials": [],
  "images": []
}
```

Keterangan:

- `steps[]` — urut berdasarkan `step_number`. `image_url` absolute bila step punya gambar.
- `materials[]` — bahan/alat; berisi `name`, `quantity`, `local_source` bila terisi.
- `images[]` — semua gambar terkait artikel (utama + gambar step, sudah deduplicated), sebagai array absolute URL.

### Error

| Status | Body | Penyebab |
|---|---|---|
| 404 | `{"error": "Tutorial not found"}` | `id` tidak ada di database |
| 400 | `{"error": "Invalid id"}` | `id` bukan angka positif |

---

## 3. `GET /api/categories.php`

```json
{
  "data": [
    {
      "id": 7,
      "name": "Berita",
      "icon_class": "fas fa-folder",
      "tutorial_count": 8
    },
    {
      "id": 5,
      "name": "Energi",
      "icon_class": "fas fa-bolt",
      "tutorial_count": 3
    }
  ],
  "meta": { "total": 3 }
}
```

`icon_class` adalah class Font Awesome yang bisa langsung dipakai di frontend.

---

## 4. `GET /api/berita.php`

Bentuk respons identik dengan `tutorials.php`, hanya berisi artikel dari kategori `Berita`.

| Param | Deskripsi |
|---|---|
| `id` | Detail 1 berita |
| `limit` | 1–200, default 50 |
| `offset` | Pagination |

```json
{
  "data": [ { "id": 95, "title": "Kegiatan Posyandu", "…": "…" } ],
  "meta": { "total": 8, "limit": 50, "offset": 0, "count": 8 }
}
```

---

## Kode Status

| Kode | Arti |
|---|---|
| 200 | Sukses |
| 204 | (OPTIONS) CORS preflight |
| 400 | Parameter tidak valid (mis. `id < 1`, `limit` di luar 1–200) |
| 404 | Data dengan `id` tersebut tidak ditemukan |
| 405 | Method selain GET |

Bentuk error selalu:

```json
{ "error": "pesan singkat" }
```

## CORS

Header `Access-Control-Allow-Origin: *` dikirim di semua respons — API bebas dikonsumsi dari domain mana pun (SPA, Flutter/React Native, dsb). Request `OPTIONS` dijawab `204 No Content`.

## Contoh Konsumsi

### JavaScript (fetch)

```js
const BASE = 'https://<domain-anda>/api';

// Daftar tutorial, 20 per halaman
async function getTutorials(page = 1, limit = 20) {
  const res  = await fetch(`${BASE}/tutorials.php?limit=${limit}&offset=${(page - 1) * limit}`);
  const json = await res.json();
  return { items: json.data, total: json.meta.total };
}

// Detail tutorial (dengan steps + materials)
async function getTutorial(id) {
  const res = await fetch(`${BASE}/tutorials.php?id=${id}`);
  return (await res.json()).data;
}

// Berita
async function getBerita() {
  const res = await fetch(`${BASE}/berita.php`);
  return (await res.json()).data;
}
```

### PHP (cURL)

```php
$ch = curl_init('https://<domain-anda>/api/tutorials.php?limit=10');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
]);
$json = json_decode(curl_exec($ch), true);
curl_close($ch);

foreach ($json['data'] as $t) {
    echo $t['title'] . ' — ' . $t['pdf_url'] . PHP_EOL;
}
```

## Catatan

- API **read-only**: tidak ada autentikasi, tidak ada endpoint tulis. Data hanya diubah melalui panel admin web.
- File PDF dan gambar dilayani langsung dari path `/uploads/…` (bukan lewat API) — URL lengkapnya diberikan di field `pdf_url` / `image_url`.
- Sumber kode endpoint: folder `public/api/` di repo ini.
