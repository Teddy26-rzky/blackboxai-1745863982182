<?php
require_once '../functions.php';
redirect_if_not_logged_in();

$stmt = $pdo->query('SELECT * FROM rooms');
$rooms = $stmt->fetchAll();

$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Kamar - Bale's Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <div>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></div>
        <div>
            <a href="history.php" class="mr-4 hover:underline">Histori Pemesanan</a>
            <a href="../logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>
    <main class="p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Daftar Kamar Apartemen</h1>
        <?php if ($message): ?>
            <div class="mb-4 text-green-600"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($rooms as $room): ?>
                <div class="bg-white rounded shadow p-4 flex flex-col">
                    <?php if ($room['foto'] && file_exists('../uploads/' . $room['foto'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($room['foto']) ?>" alt="<?= htmlspecialchars($room['nama']) ?>" class="mb-4 rounded h-48 object-cover" />
                    <?php else: ?>
                        <div class="mb-4 bg-gray-200 h-48 flex items-center justify-center rounded text-gray-500">Tidak ada foto</div>
                    <?php endif; ?>
                    <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($room['nama']) ?></h2>
                    <p class="mb-2"><?= htmlspecialchars($room['deskripsi']) ?></p>
                    <p class="mb-2 font-bold">Harga: Rp <?= number_format($room['harga'], 0, ',', '.') ?></p>
                    <p class="mb-2">Status: 
                        <?php if ($room['status'] === 'available'): ?>
                            <span class="text-green-600 font-semibold">Tersedia</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">Maintenance</span>
                        <?php endif; ?>
                    </p>
                    <?php if ($room['status'] === 'available'): ?>
                        <a href="booking.php?room_id=<?= $room['id'] ?>" class="mt-auto bg-blue-600 text-white py-2 rounded text-center hover:bg-blue-700 transition">Booking</a>
                    <?php else: ?>
                        <button disabled class="mt-auto bg-gray-400 text-white py-2 rounded cursor-not-allowed">Booking Ditutup</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
