
oCMenu=new makeCM("oCMenu") //Making the menu object. Argument: menuname

oCMenu.frames=0

//Menu properties   
oCMenu.pxBetween=10
oCMenu.fromLeft=20 
oCMenu.fromTop=0   
oCMenu.rows=1
oCMenu.menuPlacement="center"
                                                             
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
oCMenu.level[0].filter="progid:DXImageTransform.Microsoft.Fade(duration=0.5)" 

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
oCMenu.level[1].filter="progid:DXImageTransform.Microsoft.Fade(duration=0.5)" 

oCMenu.level[2]=new cm_makeLevel() //Add this for each new level (adding one to the number)
oCMenu.level[2].width=150
oCMenu.level[2].height=18
oCMenu.level[2].offsetX=0
oCMenu.level[2].offsetY=0
oCMenu.level[2].regClass="clLevel2"
oCMenu.level[2].overClass="clLevel2over"
oCMenu.level[2].borderClass="clLevel2border"

oCMenu.makeMenu('js_menu_inicio','','Inici','controller.php?p_action=get_main','','120',0)


oCMenu.makeMenu('js_menu0','','Crear elemento','','','200')
oCMenu.makeMenu('js_menu1','','Ver/Editar elements','','','200')

oCMenu.makeMenu('js_menu0_1','js_menu0','Secció','controller.php?p_action=new_instance&p_class_id=1','','120',0)
oCMenu.makeMenu('js_menu0_2','js_menu0','P','controller.php?p_action=new_instance&p_class_id=2','','120',0)
oCMenu.makeMenu('js_menu0_3','js_menu0','C','controller.php?p_action=new_instance&p_class_id=3','','120',0)
oCMenu.makeMenu('js_menu0_4','js_menu0','S','controller.php?p_action=new_instance&p_class_id=4','','120',0)
oCMenu.makeMenu('js_menu0_8','js_menu0','E','controller.php?p_action=new_instance&p_class_id=5','','120',0)
oCMenu.makeMenu('js_menu0_12','js_menu0','F','controller.php?p_action=new_instance&p_class_id=6','','120',0)

oCMenu.makeMenu('js_menu1_1','js_menu1','Secció','controller.php?p_action=list_instances&p_class_id=1','','120',0)
oCMenu.makeMenu('js_menu1_2','js_menu1','P','controller.php?p_action=list_instances&p_class_id=2','','120',0)
oCMenu.makeMenu('js_menu1_3','js_menu1','C','controller.php?p_action=list_instances&p_class_id=3','','120',0)
oCMenu.makeMenu('js_menu1_4','js_menu1','S','controller.php?p_action=list_instances&p_class_id=4','','120',0)
oCMenu.makeMenu('js_menu1_8','js_menu1','E','controller.php?p_action=list_instances&p_class_id=5','','120',0)
oCMenu.makeMenu('js_menu1_12','js_menu1','F','controller.php?p_action=list_instances&p_class_id=6','','120',0)

oCMenu.construct()
