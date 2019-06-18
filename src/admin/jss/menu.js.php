oCMenu=new makeCM("oCMenu") //Making the menu object. Argument: menuname

oCMenu.frames=0

//Menu properties   
oCMenu.pxBetween=10
oCMenu.fromLeft=20 
oCMenu.fromTop=0   
oCMenu.rows=1
oCMenu.menuPlacement="left"
                                                             
oCMenu.offlineRoot=""
oCMenu.onlineRoot="" 
oCMenu.resizeCheck=1 
oCMenu.wait=1000 
oCMenu.fillImg="cm_fill.gif"
oCMenu.zIndex=0

oCMenu.useBar=1
oCMenu.barWidth="100%"
oCMenu.barHeight="menu" 
oCMenu.barClass="clBar"
oCMenu.barX=0 
oCMenu.barY=0
oCMenu.barBorderX=0
oCMenu.barBorderY=0
oCMenu.barBorderClass=""

oCMenu.level[0]=new cm_makeLevel() //Add this for each new level
oCMenu.level[0].width=150
oCMenu.level[0].height=18 
oCMenu.level[0].regClass="clLevel0"
oCMenu.level[0].overClass="clLevel0over"
oCMenu.level[0].borderX=0
oCMenu.level[0].borderY=0
oCMenu.level[0].borderClass="clLevel0border"
oCMenu.level[0].offsetX=0
oCMenu.level[0].offsetY=0
oCMenu.level[0].rows=0
oCMenu.level[0].arrow=0
oCMenu.level[0].arrowWidth=0
oCMenu.level[0].arrowHeight=0
oCMenu.level[0].align="bottom"

oCMenu.level[0].clippx=2
oCMenu.level[0].cliptim=2
oCMenu.level[0].filter="progid:DXImageTransform.Microsoft.Fade(duration=0.1)" 

oCMenu.level[1]=new cm_makeLevel() //Add this for each new level (adding one to the number)
oCMenu.level[1].width=oCMenu.level[0].width-4
oCMenu.level[1].height=18
oCMenu.level[1].regClass="clLevel1"
oCMenu.level[1].overClass="clLevel1over"
oCMenu.level[1].borderX=0
oCMenu.level[1].borderY=0
oCMenu.level[1].align="right" 
oCMenu.level[1].offsetX=0
oCMenu.level[1].offsetY=0
oCMenu.level[1].borderClass="clLevel1border"
oCMenu.level[1].clippx=2
oCMenu.level[1].cliptim=2
oCMenu.level[1].filter="progid:DXImageTransform.Microsoft.Fade(duration=0.1)" 

oCMenu.level[2]=new cm_makeLevel() //Add this for each new level (adding one to the number)
oCMenu.level[2].width=150
oCMenu.level[2].height=18
oCMenu.level[2].offsetX=0
oCMenu.level[2].offsetY=0
oCMenu.level[2].regClass="clLevel2"
oCMenu.level[2].overClass="clLevel2over"
oCMenu.level[2].borderClass="clLevel2border"

oCMenu.makeMenu('js_menu_inicio','','<?php echo getMessage('navigation_home'); ?>','/get_main','','120',0)

<?php
	$lg = $_SESSION['u_lang'];
	$sql = "select id, caption_".$lg." as lg_cap from omp_class_groups order by ordering";

	global $dbh;
	$ret = mysql_query($sql, $dbh);
	if(!$ret) {
		die("error:". mysql_error());
	}

	while ($row = mysql_fetch_array($ret, MYSQL_ASSOC)) {
		echo "oCMenu.makeMenu('js_menu".$row['id']."','','".$row['lg_cap']."','','','200')"."\n";;
		$sql2 = "select c.id, c.name_".$lg." as lg_name from omp_classes c, omp_roles_classes rc where c.grp_id = ".$row['id']." and c.id = rc.class_id and rc.rol_id = ".$_SESSION['user_id']." order by c.grp_order";
        $ret2 = mysql_query($sql2, $dbh);
		while ($row2 = mysql_fetch_array($ret2, MYSQL_ASSOC)) {
			echo "oCMenu.makeMenu('js_menu1_".$row2['id']."','js_menu".$row['id']."','".$row2['lg_name']."','','','120',0)"."\n";
			echo "oCMenu.makeMenu('js_menu".$row2['id']."_edit','js_menu1_".$row2['id']."','".getMessage('navigation_list')."','listinstances/".$row2['id']."','','120',0)"."\n";
			echo "oCMenu.makeMenu('js_menu".$row2['id']."_new','js_menu1_".$row2['id']."','".getMessage('navigation_new')."','newinstance/".$row2['id']."','','120',0)"."\n";
		}		
	}
?>

oCMenu.construct();