<?php

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class NoAdvertising extends PluginBase implements Listener{

    public $cfg;
    private $format;

    public function onEnable(){
		$this->saveDefaultConfig();
		$this->format = new NoAdvertisingFormat($this);
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
		$this->getServer()->getLogger()->info("§aNoAD Đã Hoạt Động!");
		$this->getServer()->getPluginManager()->registerEvents(new NoAdvertisingListener($this), $this);
		$this->getCommand("na")->setExecutor(new NoAdvertisingCommand($this));
    }
    
    public function getDomain(){
		$domain = (array) $this->cfg->get("domain");
		return $domain;
    }
    
    public function getAllowedDomain(){
		$allowed = (array) $this->cfg->get("allowed.domain");
		return $allowed;
    }
    
    public function getType(){
		return $this->cfg->get("type");
    }

    public function getMsg(){
		return $this->cfg->get("message");
    }

	public function detectSign(){
		return $this->cfg->get('detect.sign') === true;
	}

	public function getSignLines(){
		return (array) $this->cfg->get('lines');
	}

	public function getBlockedCmd(){
		return (array) $this->cfg->get('blocked.cmd');
	}

    public function addDomain($p, $name){
		$domain = $this->getDomain();
		if(in_array($name, $domain)){
	    	$p->sendMessage("§cTên Miền Đã Tồn Tại!");
	    	return false;
		}
		$domain[] = $name;
		$this->cfg->set("domain", $domain);
		$this->cfg->save();
		$p->sendMessage("§aĐã Thêm Thành Công " . $name . " Vào Cấu Hình!");
		return true;
    }

    public function removeDomain($p, $name){
    	$domain = $this->getDomain();
    	$key = array_search($name, $domain);
    	if($key === false){
    	    $p->sendMessage("§cTên Miền Không Tồn Tại!");
    	    return false;
    	}
    	unset($domain[$key]);
    	$this->cfg->set("domain", array_values($domain));
    	$this->cfg->save();
    	$p->sendMessage("§aĐã Xóa Thành Công " . $name . " Khỏi Cấu Hình!");
    	return true;
    }

    public function listDomain($p){
		$domain = implode("\n§e- ", $this->getDomain());
		$p->sendMessage("§eCác Tên Miền Bị Chặn:");
		$p->sendMessage("§e- " . $domain);
		return true;
    }

    public function broadcastMsg($m){
		foreach($this->getServer()->getOnlinePlayers() as $p){
	    	$p->sendMessage($m);
		}
    }

    public function getFormat(){
		return $this->format;
    }
	
    public function onDisable(){
		$this->getServer()->getLogger()->info("§cNoAD Đã Ngừng Hoạt Động!");
    }

}
