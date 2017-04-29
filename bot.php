<?php

//creator_id
$creator_id = "131693439";

require "base.php";

//connect to database
mysql_select_db("my_micheletelegram");

//bot object
$telegram = new Telegram("307878714:AAF3b87T3bvw3HoOw80sLP8-gCZkqBBWkuM");

//message
//$message = $telegram->getMessage();

//minecraft object
require_once "minecraft.php";
$minecraft = new Minecraft();

//tables
$table = "Kaneki666Contact";

//database variables
$select = mysql_query("select * from $table where user_id = '$user_id'");
$select_result = mysql_fetch_assoc($select);
$state = $select_result["state"];

//commands array
$commands = ["/start", "Cancel", "Contact me", "Minecraft Info", "/premium"];

//start message
if ($telegram->getMessage() == "/start") {
	$select = mysql_query("select * from $table where user_id = '$user_id'");
  	if (!mysql_num_rows($select)) {
    	mysql_query("insert into $table (user_id, username) values ('$user_id', '$username')");
  	} 
	$menu = [
		["Contact me"],
		["Minecraft Info"]
	];
	$menu = [
    "keyboard" => $menu,
    "resize_keyboard" => true
	];
	$text = "Hi! Welcome to my main bot. 
	Select what you want to do: ";
	$telegram->sendMessage($text, $menu);
	mysql_query("update $table set state = '' where user_id = '$user_id'");
}

//contact me
if ($telegram->getMessage() == "Contact me") {
	$query = "update $table set state = 'contact' where user_id = '$user_id'";
	mysql_query($query);
	$menu = [
		["Cancel"],
	];
	$menu = [
    "keyboard" => $menu,
    "resize_keyboard" => true
	];
	$text = "Send now your message, I will answer as fast as possible.

	Click <code>Cancel</code> to cancel.";
	$telegram->sendMessage($text, $menu);
}

//contact message
if (!in_array($telegram->getMessage(), $commands) and $state == "contact") {
	if ($username) {
		$have_username = $username;
	}else{
		$have_username = "*no username*";
	}
	$text = "Message from £".$user_id."£

	Name: ".$first_name."
	Username: ".$have_username."
	Message text: ".$telegram->getMessage()."


	Reply to this message to answer.";
	$telegram->sendMessage($text, $menu = null, $creator_id, $parse_mode = null);
	$menu = [
		["Contact me"],
		["Minecraft Info"]
	];
	$menu = [
    "keyboard" => $menu,
    "resize_keyboard" => true
	];
	$telegram->sendMessage("Ok. Wait for an answer...", $menu);
	mysql_query("update $table set state = '' where user_id = '$user_id'");
}elseif (in_array($telegram->getMessage(), $commands) and $state == "contact") {
	$menu = [
		["Contact me"],
		["Minecraft Info"]
	];
	$menu = [
    "keyboard" => $menu,
    "resize_keyboard" => true
	];
	$text = "Hi! Welcome to my main bot. 
	Select what you want to do: ";
	$telegram->sendMessage($text, $menu);
	mysql_query("update $table set state = '' where user_id = '$user_id'");
}

//answer to a contact message
if (strpos(" ".$reply_message, "Message from £") and $user_id == $creator_id and $telegram->getMessage()) {
	$explode = explode("£", $reply_message);
	$uid = $explode[1];
	$telegram->sendMessage("You have a new private message from @Kaneki666:

		".$telegram->getMessage(), null, $uid);
	$telegram->sendMessage("Ok!");
}

//Minecarft isPremium
if (strpos(" ".$telegram->getMessage(), "/premium")) {
	$args = explode(" ", $telegram->getMessage());
	if ($args[1] == null) {
		$telegram->sendMessage("You have to insert a nickname");
	}else{
		if ($args[1]) {
			if ($args[2] == null) {
				if ($minecraft->isPremium($args[1]) == true) {
					$telegram->sendMessage("$args[1] is premium!");
				}else{
					$telegram->sendMessage("$args[1] is not premium!");
				}
			}else{
				$telegram->sendMessage("Nickname can't contain spaces");
			}
		}
	}
}


if ($telegram->getMessage() == "Minecraft Info") {
	$telegram->sendMessage($minecraft->mojangInfo());
}

//sponsor bot
if ($telegram->getMessage() == "/sponsor") {
	$button1 = [
		"text" => "Contattaci",
		"callback_data" => "contattaci"
	];
	$button2 = [
		"text" => "Info & Costi",
		"callback_data" => "info_costi"
	];
	$button3 = [
		"text" => "About",
		"callback_data" => "about"
	];
	$structure = [
		[$button1],
		[$button2],
		[$button3]
	];
	$menu = ["inline_keyboard" => $structure];
	$text = "Benvenuto nel bot";
	$telegram->sendMessage($text, $menu);
}

if ($cb_data == "sponsor") {
	$button1 = [
		"text" => "Contattaci",
		"callback_data" => "contattaci"
	];
	$button2 = [
		"text" => "Info & Costi",
		"callback_data" => "info_costi"
	];
	$button3 = [
		"text" => "About",
		"callback_data" => "about"
	];
	$structure = [
		[$button1],
		[$button2],
		[$button3]
	];
	$menu = ["inline_keyboard" => $structure];
	$text = "Benvenuto nel bot";
	$telegram->editMessageText($cb_chat_id, $cb_msg_id, $text, $menu);
}

if ($cb_data == "contattaci") {
	$button1 = [
		"text" => "Indietro",
		"callback_data" => "sponsor"
	];
	$structure = [
		[$button1]
	];
	$menu = ["inline_keyboard" => $structure];
	$text = "Ecco i nostri username:
	@luckymls
	@cunotah
	@Piket_564
	@Kaneki666";
	$telegram->editMessageText($cb_chat_id, $cb_msg_id, $text, $menu);
}

if ($cb_data == "info_costi") {
	$button1 = [
		"text" => "Indietro",
		"callback_data" => "sponsor"
	];
	$structure = [
		[$button1]
	];
	$menu = ["inline_keyboard" => $structure];
	$text = "Ecco a te i costi del servizio:
	costi da decidere";
	$telegram->editMessageText($cb_chat_id, $cb_msg_id, $text, $menu);	
}

if ($cb_data == "about") {
	$button1 = [
		"text" => "Indietro",
		"callback_data" => "sponsor"
	];
	$structure = [
		[$button1]
	];
	$menu = ["inline_keyboard" => $structure];
	$text = "Ecco a te varie informazioni riguardanti il servizio da noi offerto";
	$telegram->editMessageText($cb_chat_id, $cb_msg_id, $text, $menu);	
}

?>