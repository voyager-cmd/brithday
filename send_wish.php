<?php
// 解决跨域问题
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// 配置QQ邮箱SMTP信息（必须修改！）
$smtp_host = "smtp.qq.com"; // QQ邮箱SMTP服务器
$smtp_port = 465; // SSL端口
$smtp_user = "3541797763@qq.com"; // 发送方邮箱（你的QQ邮箱）
$smtp_pass = "tpauhvfmrfdtdcbc"; // 不是登录密码！需在QQ邮箱设置中获取
$to_email = "3541797763@qq.com"; // 接收祝福的目标邮箱

// 接收前端传的参数
$sender_name = $_POST['senderName'] ?? '';
$wish_content = $_POST['wishContent'] ?? '';

// 验证参数
if (empty($sender_name) || empty($wish_content)) {
    echo json_encode([
        'code' => 0,
        'msg' => '请填写你的名字和祝福内容'
    ]);
    exit;
}

// 引入PHPMailer核心文件
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 初始化邮件对象
$mail = new PHPMailer(true);

try {
    // 服务器配置
    $mail->SMTPDebug = 0; // 关闭调试（上线后设为0）
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $smtp_port;

    // 收件人
    $mail->setFrom($smtp_user, "生日祝福小助手");
    $mail->addAddress($to_email, "王志华"); // 接收人昵称

    // 邮件内容
    $mail->isHTML(true); // 开启HTML格式
    $mail->Subject = "【生日祝福】来自{$sender_name}的美好祝愿";
    $mail->Body = "
        <h2>🎉 新的生日祝福来啦！</h2>
        <p><strong>发送人：</strong>{$sender_name}</p>
        <p><strong>祝福内容：</strong>{$wish_content}</p>
        <p><strong>发送时间：</strong>" . date('Y-m-d H:i:s') . "</p>
        <hr>
        <p>此邮件由生日祝福网页自动发送</p>
    ";
    $mail->AltBody = "发送人：{$sender_name}\n祝福内容：{$wish_content}\n发送时间：" . date('Y-m-d H:i:s');

    // 发送邮件
    $mail->send();
    
    // 发送成功返回
    echo json_encode([
        'code' => 1,
        'msg' => '祝福发送成功！邮件已送达目标邮箱'
    ]);
} catch (Exception $e) {
    // 发送失败返回
    echo json_encode([
        'code' => 0,
        'msg' => '邮件发送失败：' . $mail->ErrorInfo
    ]);
}