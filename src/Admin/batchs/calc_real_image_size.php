<?php

require_once('../Util/fix_mysql.inc.php');
require_once('../conf/ompinfo.php');
require_once('../Models/Model.php');
$m=new model();

echo '-- Starting ' . __FILE__ . ' ' . date('d/m/Y H:i:s') . SEP . "\n\n";

$sql = "select v.id, v.inst_id, v.atri_id, v.text_val, v.img_info, a.tag 
from omp_values v
, omp_attributes a
where 1=1
and (v.img_info is null or v.img_info='' or v.img_info='.')
and a.type='I'
and a.id = v.atri_id";
$rows = $m->get_data($sql);
foreach ($rows as $row) {
    $p_valor=$row['text_val'];
    $value_id=$row['id'];
    
    if (!filter_var($p_valor, FILTER_VALIDATE_URL)) {
        $file = public_path($p_valor);
    } else {
        $file = $p_valor;
    }
    
    $ii=@getimagesize($file);
    if ($ii) {
        $wh=$ii[0].'.'.$ii[1];
        echo $value_id." ".$p_valor." ".$row['tag']." --> updated to $wh\n";

        $sql="update omp_values set img_info='$wh' where id=$value_id";
        //echo "$sql\n";
        $m->update_one($sql);
    } else {
        echo $value_id." ".$p_valor." ".$row['tag']." --> Image size not found!\n";
    }
}
echo '-- Ending ' . __FILE__ . ' ' . date('d/m/Y H:i:s') . SEP . "\n\n";
