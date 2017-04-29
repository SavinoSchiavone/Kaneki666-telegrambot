<?php


/**
* Minecraft Class
*/
class Minecraft {

	function isPremium($nickname){
		$r = file_get_contents("https://api.mojang.com/users/profiles/minecraft/$nickname");
		$premium = json_decode($r, true);
		if (isset($premium["name"])) {
			return true;
		}else{
			return false;
		}
	}

	//get uuid
	private function getUUID($nickname)
	{
		$r = file_get_contents("https://api.mojang.com/users/profile/minecraft/$nickname");
		$rr = json_decode($r, true);
		$uuid = $rr["id"];
		return $uuid;
	}

	function mojangInfo()
	{
		$r = file_get_contents("https://status.mojang.com/check");
		$rr = json_decode($r, true);
		$minecraft_net = $rr[0]["minecraft.net"];
		$account_mojang_com = $rr[2]["account.mojang.com"];
		$mojang_com = $rr[9]["mojang.com"];
		if ($minecraft_net == "green") {
			$minecraft_net_status = "✅";
		}elseif ($minecraft_net == "yellow") {
			$minecraft_net_status = "⚠️";
		}elseif ($minecraft_net == "red") {
			$minecraft_net_status = "❌";
		}
		if ($account_mojang_com == "green") {
			$account_mojang_com_status = "✅";
		}elseif ($account_mojang_com == "yellow") {
			$account_mojang_com_status = "⚠️";
		}elseif ($account_mojang_com == "red") {
			$account_mojang_com_status = "❌";
		}
		if ($mojang_com == "green") {
			$mojang_com_status = "✅";
		}elseif ($mojang_com == "yellow") {
			$mojang_com_status = "⚠️";
		}elseif ($mojang_com == "red") {
			$mojang_com_status = "❌";
		}
		$text = "<b>Minecraft.net</b>: $minecraft_net_status
		<b>account.mojang.com</b>: $account_mojang_com_status
		<b>mojang.com</b>: $mojang_com_status";
		return $text;
	}

}//class end


?>