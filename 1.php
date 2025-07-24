<?php
echo "=== Ví dụ ngoài lề: Hậu tăng trong biểu thức số học ===\n";

$a = 5;
// Hậu tăng $a trong biểu thức sẽ trả về giá trị cũ, rồi mới tăng $a lên
$result = $a++ + 10;  // $a++ trả về 5, nên result = 5 + 10 = 15; sau đó $a → 6

echo "Giá trị được dùng trong phép tính (trước khi tăng): " . ($result - 10) . "\n";  // 5
echo "Kết quả phép tính (\$a++ + 10): $result\n";                                 // 15
echo "Giá trị của \$a sau đó: $a\n";                                              // 6

echo "\n=== Ví dụ ngoài lề: Hậu tăng khi truy xuất mảng ===\n";

$colors = ['Đỏ', 'Lục', 'Xanh'];
$idx = 0;
// Mỗi lần dùng $idx++, ta lấy phần tử mảng tại chỉ số cũ, sau đó $idx tăng
echo "Màu đầu tiên: " . $colors[$idx++] . "\n";  // Lấy colors[0] = 'Đỏ', rồi $idx → 1
echo "Màu thứ hai: " . $colors[$idx++] . "\n";
echo "Màu thứ hai: " . $colors[$idx++] . "\n";    // Lấy colors[1] = 'Lục', rồi $idx → 2
echo "Chỉ số hiện tại của \$idx: $idx\n";         // 2
