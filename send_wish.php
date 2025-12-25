<?php
// 完整跨域配置（解决OPTIONS预检请求）
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// 处理OPTIONS预检请求（必须！）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// QQ邮箱SMTP配置
$smtp_host = "smtp.qq.com";
$smtp_port = 465;
$smtp_user = "3541797763@qq.com"; // 发送方邮箱
$smtp_pass = "tpauhvfmrfdtdcbc";  // SMTP授权码（请确认有效性）
$to_email = "3951046498@qq.com";  // 接收方邮箱

// 接收前端参数
$sender_name = $_POST['senderName'] ?? '';
$wish_content = $_POST['wishContent'] ?? '';

// 参数验证
if (empty($sender_name) || empty($wish_content)) {
    echo json_encode([
        'code' => 0,
        'msg' => '参数为空'
    ]);
    exit;
}

// 引入PHPMailer（确保src文件夹和此文件同目录）
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';
require __DIR__ . '/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);

    // 调试模式（先设为2，测试通过后改0）
    $mail->SMTPDebug = 2; 
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $smtp_port;

    // 收件人配置
    $mail->setFrom($smtp_user, "生日祝福小助手");
    $mail->addAddress($to_email, "王志华");

    // 邮件内容
    $mail->isHTML(true);
    $mail->Subject = "【生日祝福】来自{$sender_name}的祝愿";
    $mail->Body = "
        <h2>🎉 新的生日祝福</h2>
        <p><strong>发送人：</strong>{$sender_name}</p>
        <p><strong>祝福内容：</strong>{$wish_content}</p>
        <p><strong>时间：</strong>" . date('Y-m-d H:i:s') . "</p>
    ";
    $mail->AltBody = "发送人：{$sender_name}\n祝福：{$wish_content}\n时间：" . date('Y-m-d H:i:s');

    // 发送邮件
    $mail->send();
    echo json_encode([
        'code' => 1,
        'msg' => '发送成功'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'code' => 0,
        'msg' => '发送失败：' . $mail->ErrorInfo
    ]);
}
?>