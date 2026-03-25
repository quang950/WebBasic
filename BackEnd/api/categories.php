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
            // Default: return all categories with product counts
            $sql = "SELECT c.id, c.name, c.description, c.is_visible,
                    COUNT(p.id) as product_count
                    FROM categories c
                    LEFT JOIN products p ON p.category = c.name
                    GROUP BY c.id, c.name, c.description, c.is_visible
                    ORDER BY c.name ASC";
            
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
        $name = trim($_PUT['name'] ?? '');
        $description = trim($_PUT['description'] ?? '');
        $is_visible = isset($_PUT['is_visible']) ? (int)$_PUT['is_visible'] : 1;
        
        if ($id <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID or name']);
            exit;
        }
        
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, is_visible = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $description, $is_visible, $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            throw new Exception("Update failed: " . $stmt->error);
        }
        $stmt->close();
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
