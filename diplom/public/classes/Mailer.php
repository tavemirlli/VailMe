<?php
// Подключаем PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Подключаем автозагрузку (если через Composer)
require_once __DIR__ . '/../vendor/autoload.php';

class Mailer {
    /**
     * Отправка письма через SMTP
     */
    public static function send($to, $subject, $body, $altBody = '') {
        
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.mail.ru';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'vailme@mail.ru';
            $mail->Password   = 'your-password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            $mail->setFrom('vailme@mail.ru', 'VailMe');
            $mail->addAddress($to);
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            
            if (!empty($altBody)) {
                $mail->AltBody = $altBody;
            }
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Письмо не отправлено: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    public static function sendInvoice($order) {
        $to = $order['customer_email'];
        $subject = "Счет на оплату №{$order['order_number']}";
        
        $body = self::generateInvoiceHtml($order);
        
        return self::send($to, $subject, $body);
    }

    private static function generateInvoiceHtml($order) {
        $items = Order::getOrderItems($order['id']);
        
        $itemsHtml = '';
        foreach ($items as $item) {
            $productName = htmlspecialchars($item['product_name']);
            $quantity = $item['quantity'];
            $productPrice = number_format($item['product_price'], 0, '.', ' ');
            $totalPrice = number_format($item['total_price'], 0, '.', ' ');
            
            $itemsHtml .= "<tr>";
            $itemsHtml .= "<td>{$productName}</td>";
            $itemsHtml .= "<td>{$quantity}</td>";
            $itemsHtml .= "<td>{$productPrice} ₽</td>";
            $itemsHtml .= "<td>{$totalPrice} ₽</td>";
            $itemsHtml .= "</tr>";
        }
        
        $orderNumber = $order['order_number'];
        $customerName = htmlspecialchars($order['customer_name']);
        $customerPhone = $order['customer_phone'];
        $customerEmail = $order['customer_email'];
        $totalAmount = number_format($order['total_amount'], 0, '.', ' ');
        $currentDate = date('d.m.Y');
        
        $html = "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Счет на оплату №{$orderNumber}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: #f5f5f5;
                }
                .invoice {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 30px;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 16px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                }
                .header {
                    text-align: center;
                    border-bottom: 2px solid #F0B1D3;
                    padding-bottom: 15px;
                    margin-bottom: 25px;
                }
                .header h2 {
                    margin: 0 0 5px 0;
                    color: #333;
                }
                .header p {
                    margin: 0;
                    color: #666;
                }
                h3 {
                    color: #333;
                    margin: 20px 0 10px 0;
                    font-size: 16px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                }
                th, td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #eee;
                }
                th {
                    background: #f9f9f9;
                    font-weight: 600;
                }
                .total {
                    font-size: 24px;
                    font-weight: bold;
                    color: #F0B1D3;
                    text-align: right;
                    margin-top: 20px;
                    padding-top: 15px;
                    border-top: 2px solid #F0B1D3;
                }
                .bank-details {
                    background: #f9f9f9;
                    padding: 20px;
                    margin-top: 25px;
                    border-radius: 12px;
                }
                .bank-details h3 {
                    margin-top: 0;
                }
                .bank-details p {
                    margin: 8px 0;
                    color: #555;
                    font-size: 14px;
                }
                .footer-note {
                    text-align: center;
                    margin-top: 30px;
                    color: #999;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class='invoice'>
                <div class='header'>
                    <h2>Счет на оплату №{$orderNumber}</h2>
                    <p>Дата: {$currentDate}</p>
                </div>
                
                <h3>Данные покупателя:</h3>
                <p><strong>{$customerName}</strong><br>
                Телефон: {$customerPhone}<br>
                Email: {$customerEmail}</p>
                
                <h3>Товары:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Кол-во</th>
                            <th>Цена</th>
                            <th>Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                </table>
                
                <div class='total'>
                    Итого к оплате: {$totalAmount} ₽
                </div>
                
                <div class='bank-details'>
                    <h3>Реквизиты для оплаты:</h3>
                    <p><strong>ИП Фамилия Имя Отчество</strong><br>
                    ИНН: 1234567890<br>
                    Расчетный счет: 40802810XXXXXXXXXX<br>
                    Банк: Т-Банк<br>
                    БИК: 044525974<br>
                    Корр. счет: 30101810145250000974</p>
                    <p>После оплаты, пожалуйста, сообщите нам об этом по телефону <strong>+7-988-888-88-88</strong></p>
                </div>
                
                <div class='footer-note'>
                    <p>Спасибо за заказ!</p>
                    <p>Администратор свяжется с вами для уточнения деталей доставки.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $html;
    }
}
?>