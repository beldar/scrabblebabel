<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Angry Cheats</title>
    </head>
    <body>
        
        <?php

        mysql_connect('localhost', 'user', 'pass')or die(mysql_error());
        mysql_select_db('cheats')or die(mysql_error());
        /**** DB *****
         * --
            -- Base de datos: `cheats`
            --

            -- --------------------------------------------------------

            --
            -- Estructura de tabla para la tabla `jugades`
            --

            CREATE TABLE IF NOT EXISTS `jugades` (
            `idJugada` int(11) NOT NULL AUTO_INCREMENT,
            `idPartida` int(11) NOT NULL,
            `paraula` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `ini` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `end` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `orientacio` char(1) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`idJugada`,`idPartida`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

            -- --------------------------------------------------------

            --
            -- Estructura de tabla para la tabla `partides`
            --

            CREATE TABLE IF NOT EXISTS `partides` (
            `idPartida` int(11) NOT NULL AUTO_INCREMENT,
            `jugador` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `idioma` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`idPartida`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;
         * 
         */
        function strTojugada($str){
            $v = explode(",",$str);
            $re['palabra'] = preg_replace("/'/", '', trim($v[1]));
            $re['ini']['x'] = trim(str_replace("(",'',$v[2]));
            $re['ini']['y'] = trim(str_replace(")",'',$v[3]));
            $re['end']['x'] = trim(str_replace("(",'',$v[4]));
            $re['end']['y'] = trim(str_replace(")",'',$v[5]));
            if($re['ini']['y']==$re['end']['y']) $re['o'] = 'h';
            if($re['ini']['x']==$re['end']['x']) $re['o'] = 'v';
			echo '<pre>';
			var_dump($re);
			echo '<pre>';
            return $re;
        }
        function insereixJugada($partidaid,$jug){
            $qi = "INSERT INTO jugades (idPartida,paraula,ini,end,orientacio) VALUES ($partidaid,'".utf8_decode($jug['paraula'])."','".implode(",",$jug['ini'])."','".implode(",",$jug['end'])."','".$jug['o']."')";
            mysql_query($qi)or die(mysql_error());
        }
        if(isset($_POST['jugador']) && isset($_POST['carrega'])){
            $jugador = $_POST['jugador'];
            $q = mysql_query("SELECT * FROM partides WHERE jugador LIKE '%$jugador%'") or die(mysql_error());
            if(mysql_num_rows($q)>0){
                $partida= mysql_fetch_assoc($q);
                $partidaid = $partida['idPartida'];
				$_POST['lang'] = $partida['idioma'];
                $qj = mysql_query("SELECT * FROM jugades WHERE idPartida=".$partidaid)or die(mysql_error());
                $jugs = array();
                while($jj = mysql_fetch_assoc($qj)){
                    $jugs[$jj['ini']] = $jj;
                    $jugs[$jj['ini']]['ini'] = explode(",",$jugs[$jj['ini']]['ini']);
                    $jugs[$jj['ini']]['end'] = explode(",",$jugs[$jj['ini']]['end']);
                }
                $iwv = 0;
                $iwh = 0;
                $o = '';
                for($i=0;$i<15;$i++){
                    for($j=0;$j<15;$j++){
                        if(isset($jugs[$j.','.$i]) && $jugs[$j.','.$i]['orientacio']=='h'){
                            $cwv = $jugs[$j.','.$i]['paraula'];
                            $iwv = strlen($cwv);
                            $o = 'h';
                        }
                        if(isset($jugs[$i.','.$j]) && $jugs[$i.','.$j]['orientacio']=='v'){
                            $cwh = $jugs[$i.','.$j]['paraula'];
                            $iwh = strlen($cwh);
                            $o = 'v';
                        }
                        if($iwh!=0){
                            $hl = strlen($cwh)-$iwh;
                            $iwh--;
                            $_POST['board'][$i][$j] = $cwh[$hl];
                        }
                        if($iwv!=0){ 
                            $vl = strlen($cwv)-$iwv;
                            $iwv--;
                            $_POST['board'][$j][$i] = $cwv[$vl];
                        }
                    }
                }
            }
        }
        if(isset($_POST['jugador']) && !isset($_POST['carrega'])){
            $jugador = $_POST['jugador'];
            $q = mysql_query("SELECT * FROM partides WHERE jugador LIKE '%$jugador%'") or die(mysql_error());
            if(mysql_num_rows($q)>0){
                $partida= mysql_fetch_assoc($q);
                $partidaid = $partida['idPartida'];
                $qj = mysql_query("SELECT * FROM jugades WHERE idPartida=".$partidaid)or die(mysql_error());
                $jugs = array();
                while($jj = mysql_fetch_assoc($qj)){
                    $jugs[$jj['paraula']] = $jj;
                    $jugs[$jj['paraula']]['ini'] = explode(",",$jugs[$jj['paraula']]['ini']);
                    $jugs[$jj['paraula']]['end'] = explode(",",$jugs[$jj['paraula']]['end']);
                }
            }else{
                $q = mysql_query("INSERT INTO partides (jugador,idioma) VALUES ('$jugador','".$_POST['lang']."')")or die(mysql_error());
                $partidaid = mysql_insert_id();
            }
            $wv = '';
            $wh = '';
            $jugades = array();
            $wvi = array();
            $whi = array();
            $ju = array();
            $board = $_POST['board'];
            for($i=0;$i<16;$i++){
                for($j=0;$j<16;$j++){
                    //Vertical
                    if(@$board[$i][$j]!=''){
                        $wv .= $board[$i][$j];
                        if(count($wvi)==0) $wvi = array('x'=>$i,'y'=>$j);
                    }elseif($wv!='' && strlen($wv)>1){
                        $ju['paraula'] = $wv;
                        $ju['ini'] = $wvi;
                        $ju['end'] = array('x'=>$i,'y'=>$j-1);
                        $ju['o'] = 'v';
                        $jugades[] = $ju;
                        $wv = '';
                        $wvi = array();
                    }elseif(strlen($wv)==1){
                        $wv='';
                        $wvi = array();
                    }

                    //Horitzontal
                    if(@$board[$j][$i]!=''){
                        $wh .= $board[$j][$i];
                        if(count($whi)==0) $whi = array('x'=>$j,'y'=>$i);
                    }elseif($wh!='' && strlen($wh)>1){
                        $ju['paraula'] = $wh;
                        $ju['ini'] = $whi;
                        $ju['end'] = array('x'=>$j-1,'y'=>$i);
                        $ju['o'] = 'h';
                        $jugades[] = $ju;
                        $wh = '';
                        $whi = array();
                    }elseif(strlen($wh)==1){
                        $wh = '';
                        $whi = array();
                    }
                }
            }
            $myFile = "jugades.py";
            $fh = fopen($myFile, 'w');
            $opening = "import board\nb = board.Board(\"".$_POST['lang']."\")\n";
            
            foreach($jugades as $jug){
                //comparar amb DB i guardar si no existeix
                if(!isset($jugs[$jug['paraula']])){
                    insereixJugada($partidaid,$jug);
                }else{
                    if(count(array_diff($jug['ini'], $jugs[$jug['paraula']]['ini']))!=0 && count(array_diff($jug['ini'], $jugs[$jug['paraula']]['ini']))!=0){
                        insereixJugada($partidaid,$jug);
                    }
                }
                $opening .= 'b.play("'.mb_strtolower(utf8_encode($jug['paraula'],'UTF-8')).'",('.$jug['ini']['x'].','.$jug['ini']['y'].'),('.$jug['end']['x'].','.$jug['end']['y'].'))';
                $opening .= "\n";
                //fwrite($fh,$jw);
            }
            //$opening .= "b\n";
            if(isset($_POST['letters']) && $_POST['letters']!='') $opening .= "c = b.moves(\"".$_POST['letters']."\")\nfor i in xrange(10):\n\tprint `c[i]`\n";
            fwrite($fh,$opening);
            fclose($fh);
            if(isset($_POST['letters']) && $_POST['letters']!=''){
				exec('python jugades.py',$output);
				echo "<h2>Top 10 jugades:</h2><ol>";
				for($n=0;$n<10;$n++){
					echo "<li>".$output[$n]."</li>";
				}
				echo '</ol>';
            }
            //insereixJugada($partidaid,strTojugada($output[0]));
        }
        echo '<form action="index.php" method="post" style="width:90%;float:left;margin:20px">';
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
            <p>Lletres:  &nbsp;&nbsp;<input type="text" name="letters" value="<?=(isset($_POST['letter']) ? $_POST['letters'] : '')?>" /></p>
            <p>Jugador: <input type="text" name="jugador" value="<?=(isset($_POST['jugador']) ? $_POST['jugador'] : '')?>" /></p>
			<p>Idioma: <select name="lang" id="lang">
				<option value="scrabble.txt" <?=((isset($_POST['lang']) && $_POST['lang']=="scrabble.txt") ? 'selected="selected"' : '')?>>Castellà</option>
				<option value="scrabble_cat.txt" <?=((isset($_POST['lang']) && $_POST['lang']=="scrabble_cat.txt") ? 'selected="selected"' : '')?>>Català</option>
				<option value="scrabble_en.txt" <?=((isset($_POST['lang']) && $_POST['lang']=="scrabble_en.txt") ? 'selected="selected"' : '')?>>Anglès</option>
			</select>
			</p>
            <p>Carrega: <input type="checkbox" name="carrega" value="1" /></p>
            <input type="submit" value="Cheat!" />
        </form>
    </body>
</html>