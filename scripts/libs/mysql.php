<?php
$pdo = new PDO(
"mysql:host={$cnf['DB_HOST']};dbname={$cnf['DB_DATABASE']};charset=utf8;",
$cnf['DB_USERNAME'],
$cnf['DB_PASSWORD'],
    [
        PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => TRUE,
    ]
);

function setsql($array,$keys=false){
    global $pdo;
    if(is_array($keys)){
        foreach($keys as $key){
            $set.=",{$key}=".$pdo->quote($array[$key]);
        }
    }elseif($keys==false){
        foreach($array as $key=>$val){
            $set.=",{$key}=".$pdo->quote($val);
        }
    }else{
        foreach($array as $key=>$val){
            if($key!=$keys){
                $set.=",{$key}=".$pdo->quote($val);
            }
        }
    }
    $set=substr($set,1);
    return $set;
}

function pdoexec($sql){
    global $pdo, $err, $cnf;
    if($sql && !$err){
        try {
            $pdo->exec($sql);
        }catch (Exception $e) {
            $err[]=$e->getMessage();
            $logs=date('c')."\n".print_r($e->getMessage(),true)."\n====================================================================================\n";
            file_put_contents($cnf['utils']['log_path']."all.log", $logs, FILE_APPEND);
        }
    }else{
        error_log('empty SQL or some error ');
    }
}