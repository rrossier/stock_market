<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');

	$name='%'.safe($_POST['q']).'%';
	$connexion=new Connexion_db();
	$req=$connexion->prepare("SELECT * FROM firms WHERE name LIKE :name LIMIT 0,10 ");
	$req->execute(array('name'=>$name));
	if($req->rowCount() == 0){
		$str='<div class="alert">Nothing found</div>';
	}
	else{
		$str="<ul>";
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$str.= "<li><strong><a href='firm.php?id=".$row['id']."'>".htmlspecialchars($row['name'])."</a></strong></li>";
		}
		$str.= "</ul>";
	}
	echo json_encode($str);
}
?>