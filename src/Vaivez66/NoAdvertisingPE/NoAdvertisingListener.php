<?php

declare(strict_types=1);

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\block\utils\SignText;
use pocketmine\event\Listener;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

final class NoAdvertisingListener implements Listener{
	public function __construct(private NoAdvertising $plugin){}

	public function onChat(PlayerChatEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$player = $event->getPlayer();
		if($player->hasPermission("no.advertising.pe.bypass")){
			return;
		}

		if($this->plugin->findBlockedHost($event->getMessage()) === null){
			return;
		}
		$event->cancel();
		$this->applyChatAction($player);
	}

	public function onSignChange(SignChangeEvent $event) : void{
		if($event->isCancelled() || !$this->plugin->detectSign()){
			return;
		}
		$player = $event->getPlayer();
		if($player->hasPermission("no.advertising.pe.bypass")){
			return;
		}

		$text = $event->getNewText();
		foreach($text->getLines() as $line){
			if($this->plugin->findBlockedHost($line) !== null){
				$event->setNewText(new SignText($this->plugin->getSignLines(), $text->getBaseColor(), $text->isGlowing()));
				$player->sendMessage("§cVui Lòng Không Quảng Cáo!");
				return;
			}
		}
	}

	public function onCommand(CommandEvent $event) : void{
		$sender = $event->getSender();
		if(!$sender instanceof Player || $event->isCancelled() || $sender->hasPermission("no.advertising.pe.bypass")){
			return;
		}

		$parts = preg_split('/\s+/', trim($event->getCommand()), 2);
		if(!is_array($parts) || $parts === []){
			return;
		}
		$command = strtolower(ltrim($parts[0], "/"));
		if(!in_array($command, $this->plugin->getBlockedCmd(), true)){
			return;
		}
		$arguments = $parts[1] ?? "";
		if($this->plugin->findBlockedHost($arguments) !== null){
			$event->cancel();
			$sender->sendMessage("§cVui Lòng Không Quảng Cáo!");
		}
	}

	private function applyChatAction(Player $player) : void{
		$message = $this->plugin->formatMessage($player->getName());
		switch($this->plugin->getType()){
			case "broadcast":
				$this->plugin->broadcastMsg($message);
				return;
			case "kick":
				$player->kick($message);
				return;
			default:
				$player->sendMessage($message);
		}
	}
}
