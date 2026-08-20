<?php

// Auth routes are registered from routes/auth.php, required below.
require __DIR__.'/auth.php';

// User management routes (super admin only) are registered from routes/users.php.
require __DIR__.'/users.php';

// RBAC (roles & permissions) routes are registered from routes/rbac.php.
require __DIR__.'/rbac.php';

// Trainer profile routes are registered from routes/trainers.php.
require __DIR__.'/trainers.php';

// Project routes are registered from routes/projects.php.
require __DIR__.'/projects.php';

// Training session routes are registered from routes/sessions.php.
require __DIR__.'/sessions.php';

// Registration form routes (including the public application endpoints)
// are registered from routes/registration.php.
require __DIR__.'/registration.php';

// Attendance routes are registered from routes/attendance.php.
require __DIR__.'/attendance.php';

// Task and evaluation routes are registered from routes/tasks.php.
require __DIR__.'/tasks.php';

// Trainee output routes are registered from routes/outputs.php.
require __DIR__.'/outputs.php';

// Center settings routes are registered from routes/center_settings.php.
require __DIR__.'/center_settings.php';

// Certificate routes are registered from routes/certificates.php.
require __DIR__.'/certificates.php';
