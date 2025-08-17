<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>

<body class="d-flex align-items-center justify-content-center" style="height:100vh;background:#f6f8fb">
    <div class="card p-4" style="width:360px">
        <h4>Masuk</h4><?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?><form method="post" action="<?= base_url('auth/login') ?>">
            <div class="mb-2"><input class="form-control" name="username" placeholder="Username" required></div>
            <div class="mb-2"><input class="form-control" name="password" type="password" placeholder="Password" required></div><button class="btn btn-primary w-100">Login</button>
        </form>
        <!-- <div class="mt-3 text-muted small">Default: admin/admin123, viewer/admin123</div> -->
    </div>
</body>

</html>