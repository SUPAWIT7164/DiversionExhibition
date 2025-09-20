<?php
session_start();

// ฟังก์ชันสำหรับการสุ่มรางวัล
function generatePrizes()
{
    $prizes = [];

    // รางวัลที่ 1 (1 รางวัล)
    $prizes['first'] = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

    // รางวัลที่ 2 (3 รางวัล)
    $prizes['second'] = [];
    for ($i = 0; $i < 3; $i++) {
        $prizes['second'][] = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    }

    // รางวัลเลขข้างเคียงรางวัลที่ 1 (2 รางวัล)
    $first_num = intval($prizes['first']);
    $adjacent = [];
    $adjacent[] = str_pad(($first_num + 1) % 1000, 3, '0', STR_PAD_LEFT);
    $adjacent[] = str_pad(($first_num - 1 + 1000) % 1000, 3, '0', STR_PAD_LEFT);
    $prizes['adjacent'] = $adjacent;

    // รางวัลเลขท้าย 2 ตัว (10 รางวัล)
    $last_two = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
    $prizes['last_two'] = $last_two;

    return $prizes;
}

// ฟังก์ชันสำหรับตรวจรางวัล
function checkPrize($number, $prizes)
{
    $results = [];
    $num = str_pad($number, 3, '0', STR_PAD_LEFT);

    // ตรวจรางวัลที่ 1
    if ($num === $prizes['first']) {
        $results[] = "รางวัลที่ 1";
    }

    // ตรวจรางวัลที่ 2
    if (in_array($num, $prizes['second'])) {
        $results[] = "รางวัลที่ 2";
    }

    // ตรวจรางวัลเลขข้างเคียง
    if (in_array($num, $prizes['adjacent'])) {
        $results[] = "รางวัลเลขข้างเคียงรางวัลที่ 1";
    }

    // ตรวจรางวัลเลขท้าย 2 ตัว
    if (substr($num, 1, 2) === $prizes['last_two']) {
        $results[] = "รางวัลเลขท้าย 2 ตัว";
    }

    return $results;
}

// จัดการการสุ่มรางวัล
if (isset($_POST['generate_prizes'])) {
    $_SESSION['prizes'] = generatePrizes();
    header('Location: index.php');
    exit();
}

// จัดการการตรวจรางวัล
$check_result = '';

// Debug: ตรวจสอบข้อมูลที่ส่งมา
if ($_POST) {
    $check_result .= "DEBUG: มีข้อมูล POST ส่งมา<br>";
    $check_result .= "DEBUG: check_number = " . (isset($_POST['check_number']) ? 'มี' : 'ไม่มี') . "<br>";
    $check_result .= "DEBUG: lottery_number = " . (isset($_POST['lottery_number']) ? $_POST['lottery_number'] : 'ไม่มี') . "<br>";
}

if (isset($_POST['check_number'])) {
    $input_number = trim($_POST['lottery_number']); // ลบช่องว่าง

    // ตรวจสอบว่าสุ่มรางวัลแล้วหรือยัง
    if (!isset($_SESSION['prizes'])) {
        $check_result = " กรุณากดปุ่ม 'ดำเนินการสุ่มรางวัล' ก่อนตรวจรางวัล";
    }
    // ตรวจสอบว่ากรอกหมายเลขหรือไม่
    elseif (empty($input_number)) {
        $check_result = " กรุณากรอกหมายเลขล็อตเตอรี่";
    }
    // ตรวจสอบว่ากรอกหมายเลขถูกต้องหรือไม่
    elseif (!is_numeric($input_number) || $input_number < 0 || $input_number > 999) {
        $check_result = " กรุณากรอกหมายเลขที่ถูกต้อง (0-999)";
    }
    // ตรวจรางวัล
    else {
        $results = checkPrize($input_number, $_SESSION['prizes']);
        if (empty($results)) {
            $check_result = " หมายเลข " . str_pad($input_number, 3, '0', STR_PAD_LEFT) . " ไม่ถูกรางวัลใดเลย";
        } else {
            $check_result = " หมายเลข " . str_pad($input_number, 3, '0', STR_PAD_LEFT) . " ถูกรางวัล: " . implode(", ", $results);
        }
    }
}

$prizes = $_SESSION['prizes'] ?? null;
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบสุ่มรางวัลล็อตเตอรี่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        /* Force button to be visible */
        #checkBtn {
            background: #00b894 !important;
            color: white !important;
            border: 2px solid #00a085 !important;
            padding: 15px !important;
            font-size: 18px !important;
            border-radius: 10px !important;
            width: 100% !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            cursor: pointer !important;
            margin: 10px 0 !important;
            box-shadow: 0 4px 8px rgba(0,184,148,0.3) !important;
        }

        #checkBtn:hover {
            background: #00a085 !important;
            transform: translateY(-2px) !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="main-container">
            <div class="header">
                <h1><i class="fas fa-trophy"></i> ระบบสุ่มรางวัลล็อตเตอรี่</h1>
                <p>งานนิทรรศการไดเวอร์ซิชั่น</p>
            </div>

            <div class="text-center mb-4">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="generate_prizes" class="btn btn-generate">
                        <i class="fas fa-dice"></i> ดำเนินการสุ่มรางวัล
                    </button>
                </form>
            </div>

            <div class="prize-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-crown"></i> รางวัลที่ 1</th>
                            <th><i class="fas fa-medal"></i> รางวัลที่ 2</th>
                            <th><i class="fas fa-star"></i> รางวัลเลขข้างเคียง</th>
                            <th><i class="fas fa-gift"></i> รางวัลเลขท้าย 2 ตัว</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($prizes): ?>
                            <tr>
                                <td><span class="prize-badge"><?php echo $prizes['first']; ?></span></td>
                                <td>
                                    <?php foreach ($prizes['second'] as $num): ?>
                                        <span class="prize-badge"><?php echo $num; ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php foreach ($prizes['adjacent'] as $num): ?>
                                        <span class="prize-badge"><?php echo $num; ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td><span class="prize-badge"><?php echo $prizes['last_two']; ?></span></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty-table">
                                    <i class="fas fa-info-circle"></i> ยังไม่ได้ดำเนินการสุ่มรางวัล
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="check-section">
                <h3 class="text-center mb-4">
                    <i class="fas fa-search"></i> ตรวจรางวัล
                </h3>

                <?php if (!$prizes): ?>
                    <div class="alert alert-warning text-center mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>กรุณากดปุ่ม "ดำเนินการสุ่มรางวัล" ก่อนตรวจรางวัล</strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success text-center mb-4">

                        <strong>พร้อมตรวจรางวัลแล้ว!</strong> กรอกหมายเลขล็อตเตอรี่ของคุณ
                    </div>
                <?php endif; ?>

                <form method="POST" id="checkForm">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="number" name="lottery_number" id="lotteryInput" class="form-control"
                                placeholder="กรอกหมายเลขล็อตเตอรี่ "
                                min="0" max="999" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <p class="text-muted mb-2"> กดปุ่มด้านล่างเพื่อตรวจรางวัล</p>
                            <input type="submit" name="check_number" value="🔍 ตรวจรางวัล"
                                style="background: #00b894 !important; color: white !important; border: 2px solid #00a085 !important; padding: 15px !important; font-size: 18px !important; border-radius: 10px !important; width: 100% !important; display: block !important; visibility: visible !important; opacity: 1 !important; cursor: pointer !important; margin: 10px 0 !important; box-shadow: 0 4px 8px rgba(0,184,148,0.3) !important;">
                        </div>
                    </div>
                </form>

                <?php if ($check_result): ?>
                    <div class="result-box">
                        <?php echo $check_result; ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="text-center mt-4">
                <small class="text-muted">
                    หมายเหตุ: สามารถสุ่มรางวัลใหม่ได้ตลอดเวลา และระบบจะแสดงผลรางวัลครั้งล่าสุดเท่านั้น
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript สำหรับปุ่มตรวจรางวัล (แบบง่าย)
        document.addEventListener('DOMContentLoaded', function() {
            const checkForm = document.getElementById('checkForm');
            const lotteryInput = document.getElementById('lotteryInput');

            // เมื่อกดปุ่มตรวจรางวัล
            checkForm.addEventListener('submit', function(e) {
                const value = lotteryInput.value;

                // ตรวจสอบว่ากรอกข้อมูลหรือไม่
                if (!value || value === '') {
                    e.preventDefault();
                    alert('กรุณากรอกหมายเลขล็อตเตอรี่');
                    lotteryInput.focus();
                    return false;
                }

                // ตรวจสอบช่วงตัวเลข
                if (value < 0 || value > 999) {
                    e.preventDefault();
                    alert('กรุณากรอกหมายเลขระหว่าง 0-999');
                    lotteryInput.focus();
                    return false;
                }

                // ไม่ต้อง preventDefault เพื่อให้ฟอร์มส่งข้อมูลได้
            });

            // ตรวจสอบเมื่อ Enter
            lotteryInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    checkForm.submit();
                }
            });
        });
    </script>
</body>

</html>