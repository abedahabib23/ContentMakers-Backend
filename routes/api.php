<?php

// Auth routes are registered from routes/auth.php, required below.
require __DIR__.'/auth.php';

// User management routes (super admin only) are registered from routes/users.php.
require __DIR__.'/users.php';

// RBAC (roles & permissions) routes are registered from routes/rbac.php.
require __DIR__.'/rbac.php';
