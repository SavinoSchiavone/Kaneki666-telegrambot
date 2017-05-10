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
	Bot made by @Kaneki666. You can find source code <a href=\"https://github.com/savinoSchiavone/Kaneki666-telegrambot\">here</a>
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


//start
if ($ex[0] == "startg") {
	if ($ex[1] == null) {
		if ($chat_type == "private") {
			$text = "Welcome to this new GroupManagerBot.
			Bot made by @Kaneki666
			You can find the bot source code <a href=\"https://github.com/savinoSchiavone/Kaneki666-telegrambot\">here</a>
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
			You can find the bot source code <a href=\"https://github.com/savinoSchiavone/Kaneki666-telegrambot\">here</a>
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
	} else if (in_array($ex[1], $start_command_ex)) {
		if ($ex[1] == "qualcosa") {
			$telegram->sendMessage("qualcosa");
		} else if ($ex[1] == "qualcosa2") {
			$telegram->sendMessage("qualcosa2");
		}
	} elseif (!in_array($ex[1], $start_command_ex) && $ex[1] != null) {
		$telegram->sendMessage("No riconosciuto");
	}
} 


//help
if ($ex[0] == "help") {
	if ($chat_type == "private") {
		if ($ex[1] == null) {
			$text = "Commands list

	Admins's commands:
		/ban - Ban an user [reply]
		/unban - Unban an user [reply]
		/kick - Kick an user [reply]
		/info - Get user info [reply]
		/del - Delete message [reply]

	Users's commands:
		/info - Get your info
		@admin - Alert to all admins

	More commands are coming.
	Use /help {command} to see help page for a command
	You can use all /command also with .command, !command or #command";
		$telegram->sendMessage($text);
		} else if (in_array($ex[1], $help_command_ex)) {
			if ($ex[1] == "qualcosa") {
				$telegram->sendMessage("qualcosa");
			} else if ($ex[1] == "qualcosa2") {
				$telegram->sendMessage("qualcosa2");
			}
		} elseif (!in_array($ex[1], $help_command_ex) && $ex[1] != null) {
			$telegram->sendMessage("No riconosciuto");
		}
	} else if ($chat_type == "group" or $chat_type == "supergroup") {
		$telegram->sendMessage("<b>Contact me in private chat</b>");
		$text = "Commands list

	Admins's commands:
		/ban - Ban an user [reply]
		/unban - Unban an user [reply]
		/kick - Kick an user [reply]
		/info - Get user info [reply]
		/del - Delete message [reply]

	Users's commands:
		/info - Get your info
		@admin - Alert to all admins

	More commands are coming.
	Use /help {command} to see help page for a command
	You can use all /command also with .command, !command or #command";
		$telegram->sendMessage($text, null, $user_id);
	}
}

//ban an user by reply
if ($ex[0] == "ban") {
	if ($chat_type == "group" or $chat_type == "supergroup") {
		if ($telegram->isAdmin($chat_id, $user_id) == true) {
			if ($ex[1] == null) {
				if (isset($reply_user_id)) {
					$telegram->kickChatMember($chat_id, $reply_user_id);
					if ($telegram->isAdmin($chat_id, $reply_user_id) == true) {
						$telegram->sendMessage("You can't ban user ".$reply_user_id, null, $user_id);
					} else {
						$telegram->sendMessage("User: ".$reply_user_id." banned.");
					}
				} else {
					$telegram->sendMessage("Invalid user", null, $user_id);
				}
			}
		}
	}
}


//unban an user by reply
if ($ex[0] == "unban") {
	if ($chat_type == "group" or $chat_type == "supergroup") {
		if ($telegram->isAdmin($chat_id, $user_id) == true) {
			if ($ex[1] == null) {
				if (isset($reply_user_id)) {
					$telegram->unbanChatMember($chat_id, $reply_user_id);
					$telegram->sendMessage("User: ".$reply_user_id." unbanned.");
				} else {
					$telegram->sendMessage("Invalid user", null, $user_id);
				}
			}
		}
	}
}


//deleteMessage
/*
*IF THE COMMAND ISN'T WORKING CHECK IF YOUR BOT
*SUPPORT THE METHOD deleteMessage WITH THE LINK:
*api.telegram.org/botYOUR_BOT_TOKEN/deleteMessage
*IF THE RESPONSE IS "METHOD NOT FOUND" YOUR BOT ISN'T
*SUPPORTING THE METHOD AND YOU NEED TO DO ANOTHER ONE
*OR SIMPLY WAIT FOR THE OFFICIAL ANNOUNCE OF THE METHOD
*/
if ($ex[0] == "del") {
	if ($chat_type == "group" or $chat_type == "supergroup") {
		if ($telegram->isAdmin($chat_id, $user_id) == true) {
			if (isset($reply_message_id)) {
				$telegram->deleteMessage($chat_id, $reply_message_id);
			}
		}
	}
}



//get chat_id
if ($ex[0] == "chatid") {
	$telegram->sendMessage($chat_id);
}







}//prefix control end




?>
