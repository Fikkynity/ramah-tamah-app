<?php

require_once dirname(__DIR__, 2) . '/config/database.php';

/**
 * Get users list with search and role filter
 */
function getUsersList(string $search = '', string $role = '', int $page = 1, int $perPage = 50): array
{
    $pdo = getDatabaseConnection();

    $whereConditions = [];
    $params = [];

    if ($search !== '') {
        $whereConditions[] = "(nik ILIKE :search OR nama ILIKE :search OR departemen ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if ($role !== '') {
        $whereConditions[] = "role = :role";
        $params[':role'] = $role;
    }

    $whereSql = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Count total
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM ramah_tamah.users $whereSql");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];

    // Get users
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->prepare("
        SELECT 
            id as user_id,
            nik,
            nama,
            departemen,
            role,
            created_at
        FROM ramah_tamah.users
        $whereSql
        ORDER BY 
            CASE WHEN role = 'admin' THEN 0 ELSE 1 END,
            nama ASC
        LIMIT :limit OFFSET :offset
    ");

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $users = $stmt->fetchAll();

    return [
        'success' => true,
        'data' => $users,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => (int)$total,
            'total_pages' => ceil($total / $perPage)
        ]
    ];
}

/**
 * Update user role
 */
function updateUserRole(string $nik, string $newRole): array
{
    $nik = trim($nik);
    $newRole = trim($newRole);

    if ($nik === '') {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'NIK tidak boleh kosong.'
        ];
    }

    if (!in_array($newRole, ['user', 'admin'], true)) {
        return [
            'success' => false,
            'type' => 'validation',
            'message' => 'Role tidak valid. Pilih user atau admin.'
        ];
    }

    $pdo = getDatabaseConnection();

    // Cek user exists
    $stmt = $pdo->prepare("
        SELECT id, nik, nama, role
        FROM ramah_tamah.users
        WHERE nik = :nik
        LIMIT 1
    ");
    $stmt->execute([':nik' => $nik]);
    $user = $stmt->fetch();

    if (!$user) {
        return [
            'success' => false,
            'type' => 'not_found',
            'message' => 'User tidak ditemukan.'
        ];
    }

    // Update role
    $stmt = $pdo->prepare("
        UPDATE ramah_tamah.users
        SET role = :role,
            updated_at = NOW()
        WHERE nik = :nik
        RETURNING id, nik, nama, departemen, role
    ");
    $stmt->execute([
        ':role' => $newRole,
        ':nik' => $nik
    ]);

    $updated = $stmt->fetch();

    return [
        'success' => true,
        'type' => 'success',
        'message' => "Role {$updated['nama']} berhasil diubah menjadi " . strtoupper($newRole),
        'user' => $updated
    ];
}
