<?php

//creator_id
$creator_id = "YOUR_TELEGRAM_ID";

require "base.php";

//connect to database
mysql_select_db("YOUR_DATABASE_NAME");

//bot object
$telegram = new Telegram("YOUR_BOT_API_KEY");

//minecraft object
require_once "minecraft.php";
$minecraft = new Minecraft();

//phpfunc object
require_once "phpfunc.php";
$php = new phpfunc();

//tables
$table = "YOUR_DATABASE_TABLE_NAME";

//database variables
$select = mysql_query("select * from $table where user_id = '$user_id'");
$select_result = mysql_fetch_assoc($select);
$state = $select_result["state"];

//commands array
$commands = ["/start", "Cancel", "Contact me", "Minecraft Info", "/premium"];
//start_command array
$prefix_command = ["!", "/", ".", "#"];
$start_command = ["!startg", ".startg", "/startg", "#startg"];
$start_command_ex = ["qualcosa", "qualcosa2"];

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


//group manager

if ($php->startsWith($telegram->getMessage(), $prefix_command, 1) == true) {

$message = str_replace(substr($telegram->getMessage(), 0, 1), "", $telegram->getMessage());	
$ex = explode(" ", $message);



if ($ex[0] == "startg") {
	if ($data == null) {
		if ($chat_type == "private") {
			$text = "Welcome to this new GroupManagerBot.
			Bot made by @Kaneki666
			You can find the bot source code at github.com/SavinoSchiavone
			/help for a command list";
			$button1 = [
				"text" => "My github :D",
				"url" => "https://www.github.com/SavinoSchiavone"
			];
			$button2 = [
				"text" => "HELP",
				"callback_data" => "help"
			];
			$structure = [
				[$button1],
				[$button2]
			];
			$menu = ["inline_keyboard" => $structure];
			$telegram->sendMessage($text, $menu);
		} else if ($chat_type == "group" or $chat_type == "supergroup") {
			$telegram->sendMessage("<b>Contact me in private chat</b>");
				$text = "Welcome to this new GroupManagerBot.
			Bot made by @Kaneki666
			You can find the bot source code at github.com/SavinoSchiavone
			/help for a commands list";
			$button1 = [
				"text" => "My github :D",
				"url" => "https://www.github.com/SavinoSchiavone"
			];
			$button2 = [
				"text" => "HELP",
				"callback_data" => "help"
			];
			$structure = [
				[$button1],
				[$button2]
			];
			$menu = ["inline_keyboard" => $structure];
			$telegram->sendMessage($text, $menu, $user_id);
		}
	} else if (in_array($data, $start_command_ex)) {
		if ($data == "qualcosa") {
			$telegram->sendMessage("qualcosa");
		} else if ($data == "qualcosa2") {
			$telegram->sendMessage("qualcosa2");
		}
	} elseif (!in_array($data, $start_command_ex) && $data != null) {
		$telegram->sendMessage("No riconosciuto");
	}
} 


/*
if () {
	$text = "Commands list

Admins's commands:
	/ban - Ban an user
	/unban - Unban an user
	/kick - Kick an user
	/info - Get user info
	/settings - Open setting menu

Users's commands:
	/info - Get your info
	/link - Get group link if setted
	@admin - Alert to all admins

More commands are coming.
Use /help {command} to see help page for a command
You can use all /commands also with .command, !command or #command";
	$telegram->sendMessage($text);
}

*/



}//prefix control end


?>
