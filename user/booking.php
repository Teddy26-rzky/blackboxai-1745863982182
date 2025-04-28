<?php
require_once '../functions.php';
redirect_if_not_logged_in();

$room_id = $_GET['room_id'] ?? null;
if (!$room_id) {
    header('Location: rooms.php');
    exit();
}

// Get room details
$stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ?');
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    header('Location: rooms.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_booking = $_POST['tanggal_booking'] ?? '';

    if (!$tanggal_booking) {
        $message = 'Tanggal booking harus diisi.';
    } else {
        // Check room status
        if ($room['status'] !== 'available') {
            $message = 'Maaf, kamar ini sedang dalam perawatan dan tidak dapat dipesan.';
        } else {
            // Check booking conflict
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE room_id = ? AND tanggal_booking = ? AND status IN ("pending", "confirmed")');
            $stmt->execute([$room_id, $tanggal_booking]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $message = 'Maaf, kamar sudah dipesan pada tanggal tersebut.';
            } else {
                // Insert booking with status pending
                $stmt = $pdo->prepare('INSERT INTO bookings (user_id, room_id, tanggal_booking, status) VALUES (?, ?, ?, "pending")');
                $stmt->execute([$_SESSION['user_id'], $room_id, $tanggal_booking]);
                header('Location: rooms.php?message=Booking berhasil dibuat dan menunggu konfirmasi admin.');
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Booking Kamar - Bale's Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6">Booking Kamar: <?= htmlspecialchars($room['nama']) ?></h1>
        <?php if ($message): ?>
            <div class="mb-4 text-red-600"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" action="booking.php?room_id=<?= $room_id ?>" class="space-y-4">
            <div>
                <label for="tanggal_booking" class="block mb-1 font-semibold">Tanggal Booking</label>
                <input type="date" id="tanggal_booking" name="tanggal_booking" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Booking</button>
        </form>
        <p class="mt-4"><a href="rooms.php" class="text-blue-600 hover:underline">Kembali ke daftar kamar</a></p>
    </div>
</body>
</html>
