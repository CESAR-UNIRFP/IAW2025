<?php
/**
 * usuarios_crud.php
 * CRUD visual para la tabla `usuarios` (MySQL + mysqli + Bootstrap 5).
 *
 * Basado en tus ficheros de ejemplo:
 * - BBDD: mi_nueva_base_de_datos2
 * - Tabla: usuarios(id, nombre, apellido, email, reg_date)
 */

declare(strict_types=1);
session_start();

// ======================
// Configuración DB (ajusta si lo necesitas)
// ======================
$db = [
  'host' => 'localhost',
  'name' => 'mi_nueva_base_de_datos2',
  'user' => 'root',
  'pass' => '',
  'charset' => 'utf8mb4',
];

// ======================
// Utilidades
// ======================
function h(?string $s): string {
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function flash_set(string $type, string $msg): void {
  $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function flash_get(): ?array {
  if (!isset($_SESSION['flash'])) return null;
  $f = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $f;
}

function csrf_token(): string {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function csrf_check(string $token): bool {
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function redirect_self(array $params = []): never {
  $base = strtok($_SERVER['REQUEST_URI'], '?') ?: 'usuarios_crud.php';
  $qs = $params ? ('?' . http_build_query($params)) : '';
  header("Location: {$base}{$qs}");
  exit;
}

// ======================
// Conexión DB
// ======================
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = null;
$db_error = null;

try {
  $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
  $conn->set_charset($db['charset']);
} catch (Throwable $e) {
  $db_error = $e->getMessage();
}

// ======================
// Acciones (Create/Update/Delete)
// ======================
$errors = [];

if ($conn && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $token  = $_POST['csrf'] ?? '';

  if (!csrf_check($token)) {
    flash_set('danger', 'Sesión caducada o token CSRF inválido. Recarga la página e inténtalo de nuevo.');
    redirect_self();
  }

  try {
    if ($action === 'create') {
      $nombre   = trim((string)($_POST['nombre'] ?? ''));
      $apellido = trim((string)($_POST['apellido'] ?? ''));
      $email    = trim((string)($_POST['email'] ?? ''));

      if ($nombre === '')   $errors[] = 'El nombre es obligatorio.';
      if ($apellido === '') $errors[] = 'El apellido es obligatorio.';
      if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'El email no es válido.';

      if (!$errors) {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $nombre, $apellido, $email);
        $stmt->execute();
        $stmt->close();

        flash_set('success', 'Usuario creado correctamente.');
        redirect_self();
      }
    }

    if ($action === 'update') {
      $id       = (int)($_POST['id'] ?? 0);
      $nombre   = trim((string)($_POST['nombre'] ?? ''));
      $apellido = trim((string)($_POST['apellido'] ?? ''));
      $email    = trim((string)($_POST['email'] ?? ''));

      if ($id <= 0)         $errors[] = 'ID inválido.';
      if ($nombre === '')   $errors[] = 'El nombre es obligatorio.';
      if ($apellido === '') $errors[] = 'El apellido es obligatorio.';
      if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'El email no es válido.';

      if (!$errors) {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ? WHERE id = ?");
        $stmt->bind_param('sssi', $nombre, $apellido, $email, $id);
        $stmt->execute();
        $stmt->close();

        flash_set('success', 'Usuario actualizado correctamente.');
        redirect_self();
      }
    }

    if ($action === 'delete') {
      $id = (int)($_POST['id'] ?? 0);
      if ($id <= 0) $errors[] = 'ID inválido.';

      if (!$errors) {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        flash_set('success', 'Usuario eliminado correctamente.');
        redirect_self();
      }
    }
  } catch (Throwable $e) {
    $errors[] = 'Error en la operación: ' . $e->getMessage();
  }
}

// ======================
// Listado (Search + Sort + Pagination)
// ======================
$q    = trim((string)($_GET['q'] ?? ''));
$sort = (string)($_GET['sort'] ?? 'id');
$dir  = strtolower((string)($_GET['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;

$allowed_sort = ['id', 'nombre', 'apellido', 'email', 'reg_date'];
if (!in_array($sort, $allowed_sort, true)) $sort = 'id';

$total = 0;
$rows  = [];

if ($conn) {
  // Count
  if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre LIKE ? OR apellido LIKE ? OR email LIKE ?");
    $stmt->bind_param('sss', $like, $like, $like);
  } else {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios");
  }
  $stmt->execute();
  $stmt->bind_result($total);
  $stmt->fetch();
  $stmt->close();

  $pages = max(1, (int)ceil($total / $per_page));
  if ($page > $pages) $page = $pages;

  $offset = ($page - 1) * $per_page;

  // Data
  if ($q !== '') {
    $like = '%' . $q . '%';
    $sql = "SELECT id, nombre, apellido, email, reg_date
            FROM usuarios
            WHERE nombre LIKE ? OR apellido LIKE ? OR email LIKE ?
            ORDER BY {$sort} {$dir}
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssii', $like, $like, $like, $per_page, $offset);
  } else {
    $sql = "SELECT id, nombre, apellido, email, reg_date
            FROM usuarios
            ORDER BY {$sort} {$dir}
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $per_page, $offset);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = $res->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
} else {
  $pages = 1;
}

$flash = flash_get();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRUD de Usuarios</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{
      --bg1:#0b1020;
      --bg2:#121a35;
      --card:#0f172a;
      --muted:rgba(255,255,255,.65);
      --ring:rgba(99,102,241,.35);
    }
    body{
      background: radial-gradient(1200px 500px at 20% 0%, rgba(99,102,241,.35), transparent 60%),
                  radial-gradient(900px 400px at 80% 20%, rgba(34,211,238,.25), transparent 55%),
                  linear-gradient(180deg, var(--bg1), var(--bg2));
      color: #e5e7eb;
      min-height: 100vh;
    }
    .glass{
      background: rgba(15, 23, 42, .65);
      border: 1px solid rgba(255,255,255,.08);
      backdrop-filter: blur(10px);
      border-radius: 18px;
      box-shadow: 0 20px 60px rgba(0,0,0,.35);
    }
    .brand-badge{
      display:inline-flex; align-items:center; gap:.5rem;
      padding:.35rem .65rem; border-radius:999px;
      background: rgba(99,102,241,.15);
      border: 1px solid rgba(99,102,241,.35);
      color: #c7d2fe;
      font-weight: 600;
      letter-spacing: .2px;
    }
    .muted{ color: var(--muted); }
    .table{
      --bs-table-bg: transparent;
      --bs-table-striped-bg: rgba(255,255,255,.035);
      --bs-table-hover-bg: rgba(99,102,241,.08);
      color: #e5e7eb;
    }
    .table th{
      color:#cbd5e1;
      border-color: rgba(255,255,255,.1)!important;
      font-weight: 600;
    }
    .table td{
      border-color: rgba(255,255,255,.08)!important;
      vertical-align: middle;
    }
    .btn-primary{
      --bs-btn-bg: #6366f1;
      --bs-btn-border-color: #6366f1;
      --bs-btn-hover-bg: #5457e6;
      --bs-btn-hover-border-color: #5457e6;
      --bs-btn-focus-shadow-rgb: 99,102,241;
    }
    .btn-outline-light{
      --bs-btn-color:#e5e7eb;
      --bs-btn-border-color: rgba(255,255,255,.2);
      --bs-btn-hover-bg: rgba(255,255,255,.12);
      --bs-btn-hover-border-color: rgba(255,255,255,.28);
    }
    .form-control, .form-select{
      background: rgba(2,6,23,.35);
      border: 1px solid rgba(255,255,255,.12);
      color:#e5e7eb;
    }
    .form-control:focus, .form-select:focus{
      border-color: rgba(99,102,241,.6);
      box-shadow: 0 0 0 .25rem var(--ring);
      background: rgba(2,6,23,.35);
      color:#e5e7eb;
    }
    .pagination .page-link{
      background: rgba(2,6,23,.25);
      border-color: rgba(255,255,255,.12);
      color:#e5e7eb;
    }
    .pagination .page-link:hover{
      background: rgba(99,102,241,.12);
    }
    .pagination .page-item.active .page-link{
      background:#6366f1;
      border-color:#6366f1;
    }
    .modal-content{
      background: rgba(15,23,42,.92);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 18px;
    }
    .dropdown-menu{
      background: rgba(15,23,42,.95);
      border: 1px solid rgba(255,255,255,.12);
    }
    .dropdown-item{ color:#e5e7eb; }
    .dropdown-item:hover{ background: rgba(99,102,241,.12); }
    .codehint{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size: .85rem;
      color:#a5b4fc;
    }
  </style>
</head>
<body>
  <div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 mb-4">
      <div>
        <div class="brand-badge mb-2">
          <i class="bi bi-people-fill"></i>
          CRUD · Tabla <span class="codehint">usuarios</span>
        </div>
        <h1 class="display-6 fw-semibold mb-1">Gestión de Usuarios</h1>
        <div class="muted">Alta, edición, búsqueda y borrado con una interfaz limpia.</div>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="bi bi-plus-lg me-1"></i> Nuevo usuario
        </button>

        <div class="dropdown">
          <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-sort-down me-1"></i> Orden
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <?php
              $sortLabels = [
                'id' => 'ID',
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'email' => 'Email',
                'reg_date' => 'Fecha',
              ];
              foreach ($sortLabels as $col => $label):
                $newDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                $qs = ['q'=>$q, 'sort'=>$col, 'dir'=>$newDir, 'page'=>1];
            ?>
              <li>
                <a class="dropdown-item" href="?<?=h(http_build_query($qs))?>">
                  <?=h($label)?>
                  <?php if ($sort === $col): ?>
                    <span class="muted ms-1">(<?=h(strtoupper($dir))?>)</span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <?php if ($db_error): ?>
      <div class="alert alert-danger glass">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>No se pudo conectar a MySQL</div>
        <div class="small muted mb-2"><?=h($db_error)?></div>
        <div class="small">
          Revisa credenciales en <span class="codehint">$db</span> y que exista la BBDD
          <span class="codehint">mi_nueva_base_de_datos2</span> con la tabla <span class="codehint">usuarios</span>.
        </div>
      </div>
    <?php endif; ?>

    <?php if ($flash): ?>
      <div class="alert alert-<?=h($flash['type'])?> glass">
        <?=h($flash['msg'])?>
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="alert alert-danger glass">
        <div class="fw-semibold mb-1">Revisa lo siguiente:</div>
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
            <li><?=h($e)?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="glass p-3 p-lg-4">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <form class="d-flex gap-2 flex-grow-1" method="get">
          <div class="input-group">
            <span class="input-group-text bg-transparent text-light border-0" style="background: rgba(2,6,23,.15)!important; border: 1px solid rgba(255,255,255,.12)!important;">
              <i class="bi bi-search"></i>
            </span>
            <input class="form-control" type="search" name="q" value="<?=h($q)?>" placeholder="Buscar por nombre, apellido o email…">
          </div>
          <input type="hidden" name="sort" value="<?=h($sort)?>">
          <input type="hidden" name="dir" value="<?=h($dir)?>">
          <button class="btn btn-outline-light" type="submit">Buscar</button>
          <?php if ($q !== ''): ?>
            <a class="btn btn-outline-light" href="?<?=h(http_build_query(['sort'=>$sort,'dir'=>$dir]))?>">Limpiar</a>
          <?php endif; ?>
        </form>

        <div class="text-nowrap muted small">
          <i class="bi bi-database me-1"></i>
          Total: <span class="fw-semibold text-light"><?=h((string)$total)?></span>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 80px;">ID</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Email</th>
              <th style="width: 170px;">Actualización</th>
              <th style="width: 140px;" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$conn): ?>
            <tr><td colspan="6" class="muted">Sin conexión a la base de datos.</td></tr>
          <?php elseif (!$rows): ?>
            <tr><td colspan="6" class="muted">No hay resultados.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td class="fw-semibold"><?=h((string)$r['id'])?></td>
                <td><?=h($r['nombre'])?></td>
                <td><?=h($r['apellido'])?></td>
                <td><?=h($r['email'] ?? '')?></td>
                <td class="muted small"><?=h($r['reg_date'] ?? '')?></td>
                <td class="text-end">
                  <button
                    class="btn btn-sm btn-outline-light me-1"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEdit"
                    data-id="<?=h((string)$r['id'])?>"
                    data-nombre="<?=h($r['nombre'])?>"
                    data-apellido="<?=h($r['apellido'])?>"
                    data-email="<?=h($r['email'] ?? '')?>"
                    >
                    <i class="bi bi-pencil-square me-1"></i> Editar
                  </button>

                  <button
                    class="btn btn-sm btn-outline-light"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDelete"
                    data-id="<?=h((string)$r['id'])?>"
                    data-nombre="<?=h($r['nombre'])?>"
                    data-apellido="<?=h($r['apellido'])?>"
                    >
                    <i class="bi bi-trash3 me-1"></i> Borrar
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <?php if ($conn && $pages > 1): ?>
        <nav class="mt-3">
          <ul class="pagination justify-content-center">
            <?php
              $mk = function(int $p) use ($q, $sort, $dir) {
                return '?' . http_build_query(['q'=>$q,'sort'=>$sort,'dir'=>$dir,'page'=>$p]);
              };
              $prev = max(1, $page - 1);
              $next = min($pages, $page + 1);
            ?>
            <li class="page-item <?=($page<=1?'disabled':'')?>">
              <a class="page-link" href="<?=$mk($prev)?>" aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>

            <?php
              // Ventana de páginas (compacta)
              $window = 2;
              $start = max(1, $page - $window);
              $end   = min($pages, $page + $window);
              if ($start > 1) {
                echo '<li class="page-item"><a class="page-link" href="'.h($mk(1)).'">1</a></li>';
                if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
              }
              for ($p=$start; $p<=$end; $p++) {
                $active = $p === $page ? 'active' : '';
                echo '<li class="page-item '.$active.'"><a class="page-link" href="'.h($mk($p)).'">'.h((string)$p).'</a></li>';
              }
              if ($end < $pages) {
                if ($end < $pages-1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                echo '<li class="page-item"><a class="page-link" href="'.h($mk($pages)).'">'.h((string)$pages).'</a></li>';
              }
            ?>

            <li class="page-item <?=($page>=$pages?'disabled':'')?>">
              <a class="page-link" href="<?=$mk($next)?>" aria-label="Siguiente">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    </div>

    <div class="muted small mt-3">
      Consejo: si aún no has creado la tabla, usa tu script <span class="codehint">4creartabla.php</span>.
      Esta página usa <span class="codehint">mysqli</span> con consultas preparadas.
    </div>
  </div>

  <!-- Modal: Crear -->
  <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <form method="post" class="modal-body pt-0">
          <input type="hidden" name="action" value="create">
          <input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="nombre" required maxlength="30" placeholder="Ej. Ana">
          </div>
          <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input class="form-control" name="apellido" required maxlength="30" placeholder="Ej. García">
          </div>
          <div class="mb-3">
            <label class="form-label">Email (opcional)</label>
            <input class="form-control" name="email" type="email" maxlength="50" placeholder="ana@correo.com">
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i>Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Editar -->
  <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <form method="post" class="modal-body pt-0" id="formEdit">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
          <input type="hidden" name="id" id="edit_id">

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Nombre</label>
              <input class="form-control" name="nombre" id="edit_nombre" required maxlength="30">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Apellido</label>
              <input class="form-control" name="apellido" id="edit_apellido" required maxlength="30">
            </div>
            <div class="col-12">
              <label class="form-label">Email (opcional)</label>
              <input class="form-control" name="email" id="edit_email" type="email" maxlength="50">
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save2 me-1"></i>Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Borrar -->
  <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title"><i class="bi bi-trash3-fill me-2"></i>Eliminar usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <form method="post" class="modal-body pt-0">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
          <input type="hidden" name="id" id="delete_id">

          <div class="p-3 rounded-3" style="background: rgba(244,63,94,.10); border: 1px solid rgba(244,63,94,.25);">
            <div class="fw-semibold mb-1">¿Seguro que quieres eliminar este usuario?</div>
            <div class="muted small">Esta acción no se puede deshacer.</div>
            <div class="mt-2">
              <span class="muted small">Usuario:</span>
              <span class="fw-semibold" id="delete_label"></span>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-trash3 me-1"></i>Sí, borrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Rellenar modal de edición con data-*
    const modalEdit = document.getElementById('modalEdit');
    modalEdit?.addEventListener('show.bs.modal', (ev) => {
      const btn = ev.relatedTarget;
      if (!btn) return;
      document.getElementById('edit_id').value = btn.getAttribute('data-id') || '';
      document.getElementById('edit_nombre').value = btn.getAttribute('data-nombre') || '';
      document.getElementById('edit_apellido').value = btn.getAttribute('data-apellido') || '';
      document.getElementById('edit_email').value = btn.getAttribute('data-email') || '';
    });

    // Rellenar modal de borrado
    const modalDelete = document.getElementById('modalDelete');
    modalDelete?.addEventListener('show.bs.modal', (ev) => {
      const btn = ev.relatedTarget;
      if (!btn) return;
      const id = btn.getAttribute('data-id') || '';
      const nombre = btn.getAttribute('data-nombre') || '';
      const apellido = btn.getAttribute('data-apellido') || '';
      document.getElementById('delete_id').value = id;
      document.getElementById('delete_label').textContent = `#${id} · ${nombre} ${apellido}`;
    });

    // Auto-abrir modales si hay errores (para no perder contexto)
    <?php if ($errors && ($_POST['action'] ?? '') === 'create'): ?>
      new bootstrap.Modal('#modalCreate').show();
    <?php elseif ($errors && ($_POST['action'] ?? '') === 'update'): ?>
      new bootstrap.Modal('#modalEdit').show();
      document.getElementById('edit_id').value = <?=json_encode((string)($_POST['id'] ?? ''))?>;
      document.getElementById('edit_nombre').value = <?=json_encode((string)($_POST['nombre'] ?? ''))?>;
      document.getElementById('edit_apellido').value = <?=json_encode((string)($_POST['apellido'] ?? ''))?>;
      document.getElementById('edit_email').value = <?=json_encode((string)($_POST['email'] ?? ''))?>;
    <?php elseif ($errors && ($_POST['action'] ?? '') === 'delete'): ?>
      new bootstrap.Modal('#modalDelete').show();
      document.getElementById('delete_id').value = <?=json_encode((string)($_POST['id'] ?? ''))?>;
      document.getElementById('delete_label').textContent = `#<?=h((string)($_POST['id'] ?? ''))?>`;
    <?php endif; ?>
  </script>
</body>
</html>
