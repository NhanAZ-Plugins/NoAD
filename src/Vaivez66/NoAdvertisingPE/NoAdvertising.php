<?php

declare(strict_types=1);

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

final class NoAdvertising extends PluginBase implements Listener{
	private Config $cfg;
	private NoAdvertisingFormat $format;

	protected function onEnable() : void{
		$this->saveDefaultConfig();
		$this->cfg = $this->getConfig();
		$this->format = new NoAdvertisingFormat();

		foreach(array_merge($this->getDomain(), $this->getAllowedDomain()) as $rule){
			if(DomainMatcher::normalizeRule($rule) === null){
				$this->getLogger()->warning("Ignoring invalid configured domain rule: " . $rule);
			}
		}

		$command = $this->getCommand("na");
		if(!$command instanceof PluginCommand){
			$this->getLogger()->critical("The /na command is missing from plugin.yml; disabling NoAD.");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}
		$command->setExecutor(new NoAdvertisingCommand($this));
		$this->getServer()->getPluginManager()->registerEvents(new NoAdvertisingListener($this), $this);
		$this->getLogger()->info("NoAD is enabled.");
	}

	protected function onDisable() : void{
		$this->getLogger()->info("NoAD is disabled.");
	}

	/** @return list<string> */
	public function getDomain() : array{
		return $this->getStringList("domain");
	}

	/** @return list<string> */
	public function getAllowedDomain() : array{
		return $this->getStringList("allowed.domain");
	}

	public function getType() : string{
		$type = $this->cfg->get("type", "block");
		return is_string($type) && in_array(strtolower($type), ["broadcast", "block", "kick"], true) ? strtolower($type) : "block";
	}

	public function getMsg() : string{
		$message = $this->cfg->get("message", "§cAdvertising is not allowed.");
		return is_string($message) ? $message : "§cAdvertising is not allowed.";
	}

	public function detectSign() : bool{
		return $this->cfg->get("detect.sign", false) === true;
	}

	/** @return list<string> */
	public function getSignLines() : array{
		$lines = $this->getStringList("lines");
		if(count($lines) !== 4){
			return ["", "§cAdvertising is not allowed.", "", ""];
		}
		return $lines;
	}

	/** @return list<string> */
	public function getBlockedCmd() : array{
		$commands = [];
		foreach($this->getStringList("blocked.cmd") as $command){
			$command = strtolower(ltrim(trim($command), "/"));
			if($command !== ""){
				$commands[$command] = $command;
			}
		}
		return array_values($commands);
	}

	public function findBlockedHost(string $text) : ?string{
		return DomainMatcher::findBlockedHost($text, $this->getValidRules("domain"), $this->getValidRules("allowed.domain"));
	}

	public function formatMessage(string $playerName) : string{
		return $this->format->translate(str_replace("{player}", $playerName, $this->getMsg()));
	}

	public function addDomain(CommandSender $sender, string $name) : bool{
		$normalized = DomainMatcher::normalizeRule($name);
		if($normalized === null){
			$sender->sendMessage("§cInvalid domain rule. Use a host such as example.com or a suffix such as .com.");
			return false;
		}

		$current = $this->getDomain();
		foreach($current as $domain){
			if(DomainMatcher::normalizeRule($domain) === $normalized){
				$sender->sendMessage("§cThat domain rule already exists.");
				return false;
			}
		}
		$current[] = $normalized;
		$this->cfg->set("domain", $current);
		$this->cfg->save();
		$sender->sendMessage("§aAdded " . $normalized . " to the blocked domain list.");
		return true;
	}

	public function removeDomain(CommandSender $sender, string $name) : bool{
		$normalized = DomainMatcher::normalizeRule($name);
		if($normalized === null){
			$sender->sendMessage("§cInvalid domain rule.");
			return false;
		}

		$current = $this->getDomain();
		foreach($current as $key => $domain){
			if(DomainMatcher::normalizeRule($domain) === $normalized){
				unset($current[$key]);
				$this->cfg->set("domain", array_values($current));
				$this->cfg->save();
				$sender->sendMessage("§aRemoved " . $normalized . " from the blocked domain list.");
				return true;
			}
		}
		$sender->sendMessage("§cThat domain rule does not exist.");
		return false;
	}

	public function listDomain(CommandSender $sender) : bool{
		$domains = $this->getDomain();
		$sender->sendMessage("§eBlocked domain rules:");
		$sender->sendMessage($domains === [] ? "§e- (none)" : "§e- " . implode("\n§e- ", $domains));
		return true;
	}

	public function broadcastMsg(string $message) : void{
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$player->sendMessage($message);
		}
	}

	/** @return list<string> */
	private function getStringList(string $key) : array{
		$value = $this->cfg->get($key, []);
		if(!is_array($value)){
			return [];
		}
		return array_values(array_filter($value, static fn(mixed $entry) : bool => is_string($entry)));
	}

	/** @return list<string> */
	private function getValidRules(string $key) : array{
		$rules = [];
		foreach($this->getStringList($key) as $rule){
			$normalized = DomainMatcher::normalizeRule($rule);
			if($normalized !== null){
				$rules[] = $normalized;
			}
		}
		return $rules;
	}
}
