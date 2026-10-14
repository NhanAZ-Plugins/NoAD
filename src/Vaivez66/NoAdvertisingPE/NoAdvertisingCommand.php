<?php

declare(strict_types=1);

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

final class NoAdvertisingCommand implements CommandExecutor{
	public function __construct(private NoAdvertising $plugin){}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(strtolower($command->getName()) !== "na"){
			return false;
		}
		if(!$sender->hasPermission("no.advertising.pe")){
			$sender->sendMessage("Bạn Không Có Thẩm Quyền!");
			return true;
		}
		if(count($args) === 1 && strtolower($args[0]) === "list"){
			return $this->plugin->listDomain($sender);
		}
		if(count($args) !== 2){
			return false;
		}

		return match(strtolower($args[0])){
			"add" => $this->plugin->addDomain($sender, $args[1]),
			"remove" => $this->plugin->removeDomain($sender, $args[1]),
			default => false
		};
	}
}
