<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <form action="index.php" method="post">
        <?php
        if($_POST){
            $wv = '';
            $wh = '';
            $jugades = array();
            $wvi = array();
            $whi = array();
            $ju = array();
            $board = $_POST['board'];
            for($i=0;$i<15;$i++){
                for($j=0;$j<15;$j++){
                    if($board[$i][$j]!=''){
                        $wv .= $board[$i][$j];
                        if(count($wvi)==0) $wvi = array('x'=>$i,'y'=>$j);
                    }elseif($wv!=''){
                        echo $board[$i][$j].' -> '.$i.'-'.$j;
                        $ju['paraula'] = $wv;
                        $ju['ini'] = $wvi;
                        $ju['end'] = array('x'=>$i,'y'=>$j-1);
                        $ju['o'] = 'vertical';
                        $jugades[] = $ju;
                        $wv = '';
                        $wvi = array();
                    }
                    if($board[$j][$i]!=''){
                        $wh .= $board[$j][$i];
                        if(count($whi)==0) $whi = array('x'=>$j-1,'y'=>$i);
                    }elseif($wh!=''){
                        $ju['paraula'] = $wh;
                        $ju['ini'] = $whi;
                        $ju['end'] = array('x'=>$j,'y'=>$i);
                        $ju['o'] = 'horitzontal';
                        $jugades[] = $ju;
                        $wh = '';
                        $whi = array();
                    }
                }
            }
            var_dump($jugades);
        }
        for($i=-1;$i<15;$i++){
            
            for($j=-1;$j<15;$j++){
                if($j==-1 && $i!=-1) echo '<input type="text" style="width:20px;height:20px;" name="caca" value="'.$i.'" disabled="disabled">';
                elseif($i==-1 && $j!=-1) echo '<input type="text" style="width:20px;height:20px;" name="caca" value="'.$j.'" disabled="disabled">';
                elseif($j!=-1) echo '<input type="text" style="width:20px;height:20px;" maxlength="1" name="board['.$j.']['.$i.']" value="'.(isset($_POST['board'][$j][$i]) ? $_POST['board'][$j][$i] : '').'">';
                else echo '<div style="width:20px;height:20px;position:relative;float:left;padding:3px"></div>';
            }
            echo '<br />';
        }
        ?>
            <p><input type="text" name="letters" /></p>
            <input type="submit" value="Cheat!" />
        </form>
    </body>
</html>
