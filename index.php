<?php
include 'config.php';

$edit_mode = false;
$edit_task = null;

// Tambah tugas
if (isset($_POST['add'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($task) && !empty($priority) && !empty($due_date)) {
        $query = "INSERT INTO tasks (nama, status, prioritas, tanggal) 
                  VALUES ('$task', 'Belum Selesai', '$priority', '$due_date')";
        mysqli_query($koneksi, $query);
    }
}

// Update tugas
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    $query = "UPDATE tasks SET nama='$task', prioritas='$priority', tanggal='$due_date' WHERE id=$id";
    mysqli_query($koneksi, $query);
}

// Ambil data untuk edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($koneksi, "SELECT * FROM tasks WHERE id=$id");
    $edit_task = mysqli_fetch_assoc($result);
    $edit_mode = true;
}

// Tandai selesai
if (isset($_GET['done'])) {
    $id = $_GET['done'];
    mysqli_query($koneksi, "UPDATE tasks SET status='Selesai' WHERE id=$id");
}

// Hapus tugas
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM tasks WHERE id=$id");
}

// Ambil semua data
$todolist = mysqli_query($koneksi, "SELECT * FROM tasks ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fde3edff;
            margin: 0;
            padding: 0;
        }
        h2 {
            background: linear-gradient(to right, #eb668eff, #812955ff);
            color: white;
            padding: 20px;
            text-align: center;
            margin: 0;
            font-size: 60px;
        }
        .container {
            width: 90%;
            max-width: 850px;
            margin: 30px auto;
            background: #ffffffff;
            padding: 30px 35px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }
        form input[type="text"],
        form input[type="date"],
        form select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            flex: 1;
            min-width: 150px;
        }
        form button {
            padding: 10px 15px;
            background-color: #9075ceff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #d21919ff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #d3bbfbff;
        }
        td.done {
            color: #2e7d32;
            font-weight: bold;
        }
        td.not-done {
            color: #c62828;
            font-weight: bold;
        }
        .aksi a {
            text-decoration: none;
            padding: 5px 10px;
            margin: 2px;
            display: inline-block;
            border-radius: 5px;
            font-size: 14px;
        }
        .aksi a.edit {
            background-color: #ff9800;
            color: white;
        }
        .aksi a.done {
            background-color: #4CAF50;
            color: white;
        }
        .aksi a.delete {
            background-color: #f44336;
            color: white;
        }
        .footer {
            text-align: center;
            color: #555;
            margin-top: 40px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<h2>📋 Aplikasi To-Do List</h2>
<div class="container">
    <form method="post">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $edit_task['id'] ?>">
        <?php endif; ?>

        <input type="text" name="task" placeholder="Nama Tugas" required
               value="<?= $edit_mode ? htmlspecialchars($edit_task['nama']) : '' ?>">
        <select name="priority" required>
            <option value="">Prioritas</option>
            <option value="Tinggi" <?= $edit_mode && $edit_task['prioritas'] == 'Tinggi' ? 'selected' : '' ?>>Tinggi</option>
            <option value="Sedang" <?= $edit_mode && $edit_task['prioritas'] == 'Sedang' ? 'selected' : '' ?>>Sedang</option>
            <option value="Rendah" <?= $edit_mode && $edit_task['prioritas'] == 'Rendah' ? 'selected' : '' ?>>Rendah</option>
        </select>
        <input type="date" name="due_date" required
               value="<?= $edit_mode ? $edit_task['tanggal'] : '' ?>">
        <button type="submit" name="<?= $edit_mode ? 'update' : 'add' ?>">
            <?= $edit_mode ? '✏️ Update Tugas' : '+ Tambah Tugas' ?>
        </button>
    </form>

    <?php if (mysqli_num_rows($todolist) == 0): ?>
        <p style="text-align:center; color:#777;">Belum ada tugas. Yuk, tambah sekarang!</p>
    <?php else: ?>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Tugas</th>
                <th>Status</th>
                <th>Prioritas</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
            <?php $no = 1; ?>
            <?php while ($row = mysqli_fetch_assoc($todolist)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="<?= $row['status'] === 'Selesai' ? 'done' : 'not-done' ?>">
                        <?= $row['status'] ?>
                    </td>
                    <td><?= $row['prioritas'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td class="aksi">
                        <?php if ($row['status'] !== 'Selesai'): ?>
                            <a class="done" href="?done=<?= $row['id'] ?>">✅ Selesai</a>
                        <?php endif; ?>
                        <a class="edit" href="?edit=<?= $row['id'] ?>">✏️ Edit</a>
                        <a class="delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus tugas ini?')">🗑️ Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> naysilla todolist
</div>
</body>
</html>
