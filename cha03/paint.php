<?php
	$data = json_decode(file_get_contents('data'),1);

	echo '<style>div > div{display:inline-block;height:6px;width:6px;font-size:0;}</style>';
	echo '<body>';
	foreach($data as $x=>$ys){
		echo '<div>';
		foreach($ys as $y=>$value){
			echo '<div style="background-color:rgba(0,0,0,'.($value/100).');" data-x="'.$x.'" data-y="'.$y.'">'.$value.'</div>';
		}
		echo '</div>';
	}
	echo '</body>';
?>
