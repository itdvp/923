<?

// ���� ��������� � �������� ����������� �����
// ������ ��������: php test2.php


$srv_doc_root= $_SERVER["PWD"];
$_SERVER["DOCUMENT_ROOT"] = $srv_doc_root;
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];


define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('BX_NO_ACCELERATOR_RESET', true);
define('CHK_EVENT', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define('PUBLIC_AJAX_MODE', true);
$_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"]="N";
$APPLICATION->ShowIncludeStat = false;

 CModule::IncludeModule("sale");

// (start) ������ id ������������� � �������
     $dbBasketItems = CSaleBasket::GetList(
     	array(
          "NAME" => "ASC",
          "ID" => "ASC"
          ),
     	array(
        	"ORDER_ID" => "NULL"
          ),
        false,
        false,
        array("ID", "USER_ID", "FUSER_ID")
      );
	  while ($arItems = $dbBasketItems->Fetch()) {
    	$arBasketItems[] = $arItems;
    	$id_uses[]= $arItems["USER_ID"];
	  }
	  $id_usesss= array_unique($id_uses);
// (stop) ������ id ������������� � �������
	  	
// (start) ������� ������ �������� 	
	$cur_mmm= date("n")+0;				// ������� �����
	$cur_dates= strtotime(date("c"));	// ������� ����, ���.
	$dn_30= 3600*24*30;					// ���. � 30 ���� 
	$dates_dn_30= $cur_dates-$dn_30;	// ���. 30 ���� �����.
	   
	$i1= 0;
	foreach ($id_usesss as $id_us) {		

		$i1++;
		$filter = Array("ID"=> $id_us);
		$rsUsers = CUser::GetList(($by="personal_country"), ($order="desc"), $filter); // �������� �������������
		while($rsUsers->NavNext(true, "f_")) {
			$txt_email= "<br><b>$i1)</b> [".$f_ID."] (".$f_LOGIN.") ".$f_NAME." ".$f_LAST_NAME." ".$f_EMAIL."<br> \n";
			$txt_email= "<br>������ ����, ".$f_NAME." ".$f_LAST_NAME." � ����� �������� �������� ������: <br> \n"; 
		}
	
     	$dbBasketItems = CSaleBasket::GetList(
     		array(
	          "NAME" => "ASC",
    	      "ID" => "ASC"
        	),
	     	array(
    	      	"USER_ID" => $id_us,
        		"ORDER_ID" => "NULL"
	        ),
    	    false,
        	false,
	        array(
    		   		"ID", "PRODUCT_ID", "PRICE", "NAME", 
        			"DATE_INSERT", "USER_ID", "FUSER_ID", "QUANTITY" 
			)
	      );
		  $i2= 0;
		  while ($arItems = $dbBasketItems->Fetch()) {	  	
    		$arBasketItems[] = $arItems;
	    	$name= $arItems["NAME"];
    		$price= $arItems["PRICE"];
			$klv= $arItems["QUANTITY"];
	    	$dates= $arItems["DATE_INSERT"]."";	 
			$dates_sec= strtotime($dates);				// ���� ������ � ���.
			$zkz_mmm= date("n",strtotime($dates))+0;	// ����� ������

			// ���� ����� �� 30 ���� � �� � ������� ������, ����� ������� 
			if ($dates_sec>$dates_dn_30 && $zkz_mmm!=$cur_mmm) {
				$i2++;				
				$txt_email.= " <i>($i2)</i> ����� - $name; ���� - $price; ������ - $klv; ���� - $dates <br> \n"; 		    				
			} 
					    	    	 		
		  }
		  
		  // ���� ���� ��������� ����������, �� �������� ���������   
		  if ($i2>0) {
  		  	  print $txt_email." i2=$i2 <br>";
			  $flag_mail= mail("$f_EMAIL", "����������", "$txt_email");		
			  if ($flag_mail) print "<br> email gut <br>";
			  else print "<br> email shleht <br>";
		  } 
		  
	}

// (stop) ������� ������ �������� 	

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

?>
