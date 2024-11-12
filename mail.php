<?php
use PHPMailer\PHPMailer\PHPMailer;
require dirname(__FILE__)."/../vendor/autoload.php";

/*function send_mails($cart, $order, $mail, &$mail_body){
	$body = create_mail($cart, $order, $mail);
	$mail_body = $body;
	return send_mail_multiples("$mail, orders@empresafalsa.com", 
                        	$body, "Order $order confirmed");
}*/


/*function create_mail($cart, $order, $mail){
	$text = "<h1>Order nº $order </h1><h2>Restaurant: $mail </h2>";
	$text .= "Order detail:";
	$products = load_products(array_keys($cart));	
	$text .= "<table>"; 
	$text .= "<tr><th>Name</th><th>Description</th><th>Weight</th><th>Units</th></tr>";
	foreach($products as $product){
		$cod = $product['CodProd'];
		$nom = $product['Name'];
		$des = $product['Description'];
		$weight = $product['Weight'];
		$units = $_SESSION['cart'][$cod];									    
		$text .= "<tr><td>$nom</td><td>$des</td><td>$weight</td><td>$units</td>
		<td> </tr>";
	}
	$text .= "</table>";
	
	return $text;
}

/*function send_mail_multiples($mail_list,  $body,  $subject = ""){
		$mail = new PHPMailer();		
		$mail->IsSMTP(); 					
		$mail->SMTPDebug  = 0; 
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "tls";                 
		$mail->Host       = "smtp.gmail.com";      
		$mail->Port       = 587;                   
		$mail->Username   = "tim.casiano7@gmail.com";  
		$mail->Password   = "ltnt fkwa hjjs molc";           
		$mail->SetFrom('noreply@empresafalsa.com', 'Order system');
		$mail->Subject    = $subject;
		$mail->MsgHTML($body);
		$mails = explode(",", $mail_list);
		foreach($mails as $mailaddress){
			$mail->AddAddress($mailaddress, $mailaddress);
		}
		if(!$mail->Send()) {
		  return $mail->ErrorInfo;
		} else {
		  return TRUE;
		}
	}	*/
