<?php
/**
 * Authentication View
 */

if ($action === 'logout') {
    session_destroy();
    redirect('/index.php?page=auth&action=login');
}

if ($method === 'POST' && $action === 'login') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Email and password are required";
    } elseif (!validateEmail($email)) {
        $error = "Invalid email format";
    } else {
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user'] = $user;
            
            redirect('/index.php?page=dashboard');
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo esc(APP_NAME); ?></title>
    <link rel="stylesheet" href="/css/output.css">
</head>
<body class="bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-600 mb-2"><?php echo esc(APP_NAME); ?></h1>
                <p class="text-gray-600">Real Estate CRM System</p>
            </div>

            <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <?php echo esc($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="admin@crm.com" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 text-center mb-4">Demo Credentials:</p>
                <div class="bg-gray-50 p-3 rounded-lg text-xs space-y-1">
                    <p><strong>Admin:</strong> admin@crm.com / admin123</p>
                    <p><strong>Sales:</strong> john@crm.com / admin123</p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center text-white text-sm">
            <p>📍 Real Estate CRM v1.0</p>
        </div>
    </div>
</body>
</html>
