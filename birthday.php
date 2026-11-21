<?php
// cron: 0 12 * * * php /srv/app/birthday.php   (12:00 по времени получателя)
$cfg = array(
    'name'     => 'Анна Николаевна',
    'phone'    => getenv('SMS_PHONE'),          
    'birthday' => '21.11',                      
#    'birthday' => '29.02',                      
    'fallback' => '28.02',                      
    'tz'       => 'Asia/Kamchatka',             
    'state'    => __DIR__ . '/birthday_sent.json',
);

date_default_timezone_set($cfg['tz']);

$due = $cfg['birthday'];
if ($due === '29.02' && (int)date('L') === 0) { 
    $due = $cfg['fallback'];
}
if (date('d.m') !== $due) { exit(0); }          

$sent = is_file($cfg['state']) ? json_decode(file_get_contents($cfg['state']), true) : array();
$key  = $cfg['phone'] . ':' . date('Y');
if (isset($sent[$key])) { exit(0); }            

$text = $cfg['name'] . ', с днём рождения! Здоровья, тепла и радости каждый день.'
      . ' С уважением, команда «Данила-Мастер».';
$ch = curl_init('https://smsc.ru/sys/send.php');
$a=array(
    CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
    CURLOPT_POSTFIELDS => http_build_query(array(
        'login' => getenv('SMS_LOGIN'), 'psw' => getenv('SMS_PASSWORD'),
        'phones' => $cfg['phone'], 'mes' => $text, 'charset' => 'utf-8', 'fmt' => 3,
    )),
);

curl_setopt_array($ch, $a);

$raw = curl_exec($ch);
echo $raw.PHP_EOL;
curl_close($ch);
$res = json_decode($raw, true);
if (!is_array($res) || isset($res['error'])) {
    error_log('birthday sms failed: ' . $raw);
    error_log($res);
    exit(1);                                    
}
$sent[$key] = date('c');
file_put_contents($cfg['state'], json_encode($sent), LOCK_EX);
