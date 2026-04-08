<?php
/**
 * Categories API - Get/Add/Update/Delete categories
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    if ($method === 'GET') {
        // Get all categories or specific category
        if (!empty($action) && $action === 'list') {
            $sql = "SELECT id, name, description, is_visible FROM categories ORDER BY name ASC";
            $result = $conn->query($sql);
            
            if ($result) {
                $categories = [];
                while ($row = $result->fetch_assoc()) {
                    $categories[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'categories' => $categories
                ]);
            } else {
                throw new Exception("Query failed: " . $conn->error);
            }
        } else {
            // Default: return all categories with product counts and support search filter
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $status = isset($_GET['status']) ? intval($_GET['status']) : -1;
            
            $sql = "SELECT c.id, c.name, c.description, c.is_visible, c.status,
                    COUNT(p.id) as product_count
                    FROM categories c
                    LEFT JOIN products p ON p.category_id = c.id";
            
            $conditions = [];
            if (!empty($search)) {
                $conditions[] = "c.name LIKE '%" . $conn->real_escape_string($search) . "%'";
            }
            if ($status >= 0) {
                $conditions[] = "c.status = " . $status;
            }
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
            
            $sql .= " GROUP BY c.id, c.name, c.description, c.is_visible, c.status
                    ORDER BY c.name ASC";
            
            $result = $conn->query($sql);
            
            if ($result) {
                $categories = [];
                while ($row = $result->fetch_assoc()) {
                    $row['status_text'] = $row['status'] == 1 ? 'Đang hiển thị' : 'Đang ẩn';
                    $categories[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'categories' => $categories
                ]);
            } else {
                throw new Exception("Query failed: " . $conn->error);
            }
        }
    } 
    elseif ($method === 'POST') {
        // Add new category
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_visible = isset($_POST['is_visible']) ? (int)$_POST['is_visible'] : 1;
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Category name is required']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO categories (name, description, is_visible) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $description, $is_visible);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Category added successfully',
                'category_id' => $conn->insert_id
            ]);
        } else {
            throw new Exception("Insert failed: " . $stmt->error);
        }
        $stmt->close();
    }
    elseif ($method === 'PUT') {
        // Update category
        parse_str(file_get_contents("php://input"), $_PUT);
        
        $id = (int)($_PUT['id'] ?? 0);
        $name = isset($_PUT['name']) ? trim($_PUT['name']) : '';
        $description = isset($_PUT['description']) ? trim($_PUT['description']) : '';
        $is_visible = isset($_PUT['is_visible']) ? intval($_PUT['is_visible']) : -1;
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
            exit;
        }
        
        // Check if category exists
        $checkStmt = $conn->prepare("SELECT id FROM categories WHERE id = ? LIMIT 1");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Category not found']);
            exit;
        }
        
        // If only updating is_visible (status toggle)
        if ($is_visible >= 0 && empty($name)) {
            $stmt = $conn->prepare("UPDATE categories SET status = ?, is_visible = ? WHERE id = ?");
            $stmt->bind_param("iii", $is_visible, $is_visible, $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Category visibility updated successfully'
                ]);
            } else {
                throw new Exception("Update failed: " . $stmt->error);
            }
            $stmt->close();
        } else if (!empty($name)) {
            // Full update with all fields
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, is_visible = ?, status = ? WHERE id = ?");
            $status = isset($_PUT['status']) ? intval($_PUT['status']) : $is_visible;
            $stmt->bind_param("ssiii", $name, $description, $is_visible, $status, $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Category updated successfully'
                ]);
            } else {
                throw new Exception("Update failed: " . $stmt->error);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
        }
    }
    elseif ($method === 'DELETE') {
        // Delete category
        parse_str(file_get_contents("php://input"), $_DELETE);
        
        $id = (int)($_DELETE['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } else {
            throw new Exception("Delete failed: " . $stmt->error);
        }
        $stmt->close();
    }
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
