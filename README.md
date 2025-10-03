# engse-219-web_api
# Lab10 - Products Web API (PHP + MySQL)

## การติดตั้ง (XAMPP)
1. ใส่ไฟล์โปรเจกต์ลงใน `xampp/htdocs/appliances_api/`
2. Import `webapi_demo.sql` เข้า MySQL (phpMyAdmin หรือ CLI)
3. แก้ไขการตั้งค่า DB ใน `src/Database.php` (user/password/host)
4. เปิด Apache & MySQL ใน XAMPP
5. เข้า API ผ่าน URL:
   - Base: `http://localhost:3000/products`

## Endpoints
- GET `/api/products`  
  รองรับ query params:
  - `category`, `brand`, `min_price`, `max_price`, `search`, `sort` (`price_asc`, `price_desc`, `created_desc`, ...), `page`, `per_page`

- GET `/api/products/{id}`  
  ดูรายละเอียดรายการเดียว

- POST `/api/products`  
  สร้างสินค้า (JSON body)
  ```json
  {
    "sku":"TSLA-M3",
    "name":"Tesla Model 3",
    "brand":"Tesla",
    "model":"Model 3",
    "category":"รถยนต์ไฟฟ้า",
    "price":32990,
    "stock":5,
    "year":2023
  }
