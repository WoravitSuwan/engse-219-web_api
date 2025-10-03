const express = require("express");
const mysql = require("mysql2/promise"); // ใช้ Promise
const app = express();
const PORT = 3000;

app.use(express.json());

// สร้าง connection pool
const pool = mysql.createPool({
  host: "localhost",
  user: "root",      // เปลี่ยนเป็น user ของคุณ
  password: "",      // ใส่ password ของคุณ
  database: "webapi_demo",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// GET สินค้าทั้งหมด
app.get("/products", async (req, res) => {
  try {
    const [rows] = await pool.query("SELECT * FROM products");
    res.json(rows);
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Database error" });
  }
});

// GET สินค้าตาม id
app.get("/products/:id", async (req, res) => {
  try {
    const [rows] = await pool.query("SELECT * FROM products WHERE id = ?", [req.params.id]);
    if (rows.length === 0) return res.status(404).json({ message: "Not found" });
    res.json(rows[0]);
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Database error" });
  }
});

app.listen(PORT, () => {
  console.log(`🚀 API running at http://localhost:${PORT}`);
});
