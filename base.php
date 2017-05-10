<?php

require "class-http-request.php";
$input = file_get_contents('php://input');
$updates = json_decode($input, true);


//main variables
$message = $updates['message']['text'];
$message_id = $updates['message']['message_id'];
$user_id = $updates['message']['from']['id'];
$chat_id = $updates['message']['chat']['id'];
$first_name = $updates['message']['from']['first_name'];
$last_name = $updates['message']['from']['last_name'];
$username = $updates['message']['from']['username'];
$chat_username = $updates['message']['chat']['username'];
$chat_type = $updates['message']['chat']['type'];
$reply_message = $updates["message"]["reply_to_message"]["text"];
$reply_message_id = $updates ['message']['reply_to_message']['message_id'];
$reply_user_id = $updates["message"]["reply_to_message"]["from"]["id"];
//callback (inline keyboards) variables
if($updates['callback_query'])
$cb_data = $updates['callback_query']['data'];
$cb_id = $updates["callback_query"]["id"];
$cb_msg_id = $updates["callback_query"]["message"]["message_id"];
$cb_chat_id = $updates["callback_query"]["message"]["chat"]["id"];
$cb_user_id = $updates["callback_query"]["from"]["id"];
$cb_first_name = $updates["callback_query"]["from"]["first_name"];
$cb_last_name = $updates["callback_query"]["from"]["last_name"];
$cb_username = $updates["callback_query"]["from"]["username"];
//inline
if($updates["inline_query"])
{
$inline_id = $updates["inline_query"]["id"];
$inline_message = $updates["inline_query"]["query"];
$inline_user_id = $updates["inline_query"]["from"]["id"];
$inline_username = $updates["inline_query"]["from"]["username"];
$inline_first_name = $updates["inline_query"]["from"]["first_name"];
}



/*
*Telegram bot class 
*/
class Telegram{

	private $token;
	private $api;
	private $message;
	private $updates;

	function __construct($token)
	{
		$this->token = $token;
		$this->api = "https://api.telegram.org/bot".$token."/";
		$this->updates = json_decode(file_get_contents('php://input'), true);
		$this->message = $this->updates['message']['text'];
	}

	//getMessage
	function getMessage()
	{
		return $this->message;
	}

	/*
	*Use the function sendMessage() to send a message
	*in the chat you want (see example on file example.php)
	*Error message is WIP
	*/
	function sendMessage($message_text, $keyboard = null, $chat_id = null, $parse_mode = 'HTML')
	{
		if ($chat_id == null) {
		global $chat_id;
		}else{
			$chat_id = $chat_id;
		}
		if ($keyboard) {
			$keyboardn = json_encode($keyboard);
		}else{
			$keyboardn = null;
		}
		$args = [
			"chat_id" => $chat_id,
			"text" => $message_text,
			"parse_mode" => $parse_mode,
			"reply_markup" => $keyboardn,
			"disable_web_page_preview" => true
		];
		$r = new HttpRequest("get", $this->api."sendMessage", $args);
		return json_encode($r);
	}

	//answerCallbackQuery (an alert text)
	function answerCallbackQuery($cb_id, $alert_text, $show_alert = false)
	{
		$args = [
			"callback_query_id" => $cb_id,
			"text" => $alert_text,
			"show_alert" => $show_alert
		];
		$r = new HttpRequest("get", $this->api."answerCallbackQuery", $args);
	}

	//editMessageText
	function editMessageText($chat_id, $message_id, $text,  $keyboard = null, $parse_mode = 'HTML')
	{
		if ($keyboard) {
			$keyboardn = json_encode($keyboard);
		}else{
			$keyboardn = null;
		}
		$args = [
			"chat_id" => $chat_id,
			"message_id" => $message_id,
			"text" => $text,
			"parse_mode" => $parse_mode,
			"reply_markup" => $keyboardn
		];
		$r = new HttpRequest("get", $this->api."editMessageText", $args);
	}

	/*
	*sendChatAction 
	*Available action (must be a string) :
	*typing, upload_photo, record_video, upload_video, record_audio, upload_audio, upload_document, find_location
	*/
	function sendChatAction($chat_id, $action)
	{
		$args = [
			"chat_id" => $chat_id,
			"action" => $action
		];
		$r = new HttpRequest("get", $this->api."sendChatAction", $args);
	}

	//getChatAdministrator
	function getChatAdministrators($chat_id){
		$args = [
			'chat_id' => $chat_id
		];
		$request = new HttpRequest("get", $this->api."getChatAdministrators", $args);
		$result = $request->getResponse();
		$admins = json_decode($result, true);
		$text = "Group's staff:";
		foreach($admins[result] as $admins_list)
		{
			if($admins_list[status] == "creator")
			{
				$text .= "
				@".$admins_list[user][username]." [Creator]";
			}else{
				$text .= "
				@".$admins_list[user][username];
			}
		}
		sendMessage($chat_id, $text);
	}

	//answerInlineQuery
	function answerInlineQuery($args)
	{
		$r = new HttpRequest("get", $this->api."answerInlineQuery", $args);
	}
	

	//isAdmin
	public function isAdmin($chat_id, $user_id)
	{
		$args = [
			"chat_id" => $chat_id
		];
		$r = new HttpRequest("get", $this->api."getChatAdministrators", $args);
		$rr = $r->getResponse();
		//$this->sendMessage($rr, null, $chat_id);
		$admins = json_decode($rr, true);
		foreach ($admins["result"] as $admin) {
			if ($admin["user"]["id"] == $user_id) {
				return true;
			}
		}
		return false;
	}


	//ban
	public function kickChatMember($chat_id, $user_id)
	{
		$args = [
			"chat_id" => $chat_id,
			"user_id" => $user_id
		];
		$r = new HttpRequest("get", $this->api."kickChatMember", $args);
	}

	//unban
	public function unbanChatMember($chat_id, $user_id)
	{
		$args = [
			"chat_id" => $chat_id,
			"user_id" => $user_id
		];
		$r = new HttpRequest("get", $this->api."unbanChatMember", $args);
	}

	//deleteMessage
	public function deleteMessage($chat_id, $reply_message_id)
	{
		global $message_id;
		$args = [
			"chat_id" => $chat_id,
			"message_id" => $reply_message_id
		];
		$r = new HttpRequest("get", $this->api."deleteMessage", $args);
		$args = [
			"chat_id" => $chat_id,
			"message_id" => $message_id
		];
		$r = new HttpRequest("get", $this->api."deleteMessage", $args);
	}


}//class end

?>