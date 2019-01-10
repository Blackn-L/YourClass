<?php
function JsonData($code, $data, $msg) {
    return [
        "Code" => $code,
        "Data" => $data,
        "msg" => $msg
    ];
}
function send_mail($toEmail, $name, $title = '', $body = '') {
    $mail = new \PHPMailer\PHPMailer\PHPMailer();           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';          // 使用安全协议
    $mail->Host = "smtp.qq.com"; // SMTP 服务器
    $mail->Port = 465;                  // SMTP服务器的端口号
    $mail->Username = 'blackn@foxmail.com';    // SMTP服务器用户名
    $mail->Password = 'dnmrdxtiqvfqbfjh';     // SMTP服务器密码//这里的密码可以是邮箱登录密码也可以是SMTP服务器密码
    $mail->SetFrom('blackn@foxmail.com', 'YourClass');
    $replyEmail = 'blackn@foxmail.com';                   //留空则为发件人EMAIL
    $replyName = 'YouClassReply';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $title;  // 邮件主题
    $mail->MsgHTML($body); // 邮件内容
    $mail->AddAddress($toEmail, $name);
    return $mail->Send() ? true : $mail->ErrorInfo;
}