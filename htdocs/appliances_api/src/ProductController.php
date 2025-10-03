<?php
// src/ProductController.php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Response.php';

class ProductController {
    private $pdo;
    public function __construct() {
        $db = new Database();
        $this->pdo = $db->pdo();
    }

    // GET /api/products
    public function index($query) {
        // filters: category, min_price, max_price, brand, search, sort (price_asc/price_desc), page, per_page
        $params = [];
        $wheres = [];
        if (!empty($query['category'])) {
            $wheres[] = 'category = :category';
            $params[':category'] = $query['category'];
        }
        if (!empty($query['brand'])) {
            $wheres[] = 'brand = :brand';
            $params[':brand'] = $query['brand'];
        }
        if (isset($query['min_price']) && is_numeric($query['min_price'])) {
            $wheres[] = 'price >= :min_price';
            $params[':min_price'] = $query['min_price'];
        }
        if (isset($query['max_price']) && is_numeric($query['max_price'])) {
            $wheres[] = 'price <= :max_price';
            $params[':max_price'] = $query['max_price'];
        }
        if (!empty($query['search'])) {
            $wheres[] = '(name LIKE :search OR sku LIKE :search OR brand LIKE :search)';
            $params[':search'] = '%' . $query['search'] . '%';
        }

        $where_sql = $wheres ? ('WHERE ' . implode(' AND ', $wheres)) : '';

        // sort
        $order = 'id DESC';
        if (!empty($query['sort'])) {
            if ($query['sort'] === 'price_asc') $order = 'price ASC';
            if ($query['sort'] === 'price_desc') $order = 'price DESC';
            if ($query['sort'] === 'created_asc') $order = 'created_at ASC';
            if ($query['sort'] === 'created_desc') $order = 'created_at DESC';
        }

        // pagination
        $page = isset($query['page']) && (int)$query['page'] > 0 ? (int)$query['page'] : 1;
        $per_page = isset($query['per_page']) && (int)$query['per_page'] > 0 ? min(100, (int)$query['per_page']) : 10;
        $offset = ($page - 1) * $per_page;

        // total count
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM products $where_sql");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT * FROM products $where_sql ORDER BY $order LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', (int)$per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        Response::json([
            'data' => $data,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $per_page,
                'pages' => ceil($total / $per_page)
            ]
        ], 200);
    }

    // GET /api/products/{id}
    public function show($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) Response::json(['error' => 'Not found'], 404);
        Response::json(['data' => $row], 200);
    }

    // POST /api/products
    public function create($input) {
        // validation
        $errors = $this->validate($input, true);
        if (!empty($errors)) Response::json(['error' => 'Validation failed', 'details' => $errors], 400);

        // check SKU unique
        $stmt = $this->pdo->prepare("SELECT id FROM products WHERE sku = :sku");
        $stmt->execute([':sku' => $input['sku']]);
        if ($stmt->fetch()) Response::json(['error' => 'SKU already exists'], 409);

        $sql = "INSERT INTO products (sku, name, brand, model, category, price, stock, year) 
                VALUES (:sku, :name, :brand, :model, :category, :price, :stock, :year)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':sku' => $input['sku'],
            ':name' => $input['name'],
            ':brand' => $input['brand'],
            ':model' => $input['model'] ?? null,
            ':category' => $input['category'],
            ':price' => $input['price'],
            ':stock' => $input['stock'] ?? 0,
            ':year' => $input['year'] ?? null
        ]);
        $newId = $this->pdo->lastInsertId();
        $this->show($newId);
    }

    // PUT/PATCH /api/products/{id}
    public function update($id, $input) {
        // check exists
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) Response::json(['error' => 'Not found'], 404);

        // validation for provided fields only
        $errors = $this->validate($input, false);
        if (!empty($errors)) Response::json(['error' => 'Validation failed', 'details' => $errors], 400);

        // if sku present, check unique
        if (isset($input['sku']) && $input['sku'] !== $row['sku']) {
            $chk = $this->pdo->prepare("SELECT id FROM products WHERE sku = :sku");
            $chk->execute([':sku' => $input['sku']]);
            if ($chk->fetch()) Response::json(['error' => 'SKU already exists'], 409);
        }

        // build update dynamically
        $fields = [];
        $params = [':id' => $id];
        foreach (['sku','name','brand','model','category','price','stock','year'] as $f) {
            if (isset($input[$f])) {
                $fields[] = "$f = :$f";
                $params[":$f"] = $input[$f];
            }
        }
        if (empty($fields)) Response::json(['error' => 'No fields to update'], 400);

        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $this->show($id);
    }

    // DELETE /api/products/{id}
    public function delete($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        if (!$stmt->fetch()) Response::json(['error' => 'Not found'], 404);

        $del = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        $del->execute([':id' => $id]);
        Response::json(['message' => 'Deleted'], 200);
    }

    // validation helper
    private function validate($input, $isCreate = true) {
        $errors = [];
        if ($isCreate) {
            if (empty($input['sku'])) $errors['sku'] = 'sku is required';
            if (empty($input['name'])) $errors['name'] = 'name is required';
            if (empty($input['brand'])) $errors['brand'] = 'brand is required';
            if (empty($input['category'])) $errors['category'] = 'category is required';
            if (!isset($input['price'])) $errors['price'] = 'price is required';
        }

        if (isset($input['price']) && (!is_numeric($input['price']) || $input['price'] < 0)) {
            $errors['price'] = 'must be a number >= 0';
        }
        if (isset($input['stock']) && (!is_numeric($input['stock']) || (int)$input['stock'] < 0)) {
            $errors['stock'] = 'must be integer >= 0';
        }
        if (isset($input['sku']) && strlen($input['sku']) > 32) $errors['sku'] = 'max 32 chars';
        if (isset($input['name']) && strlen($input['name']) > 150) $errors['name'] = 'max 150 chars';

        return $errors;
    }
}
