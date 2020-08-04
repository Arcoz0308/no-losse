<?php

namespace arclegrandroi\nolosses;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\Player;

class Main extends PluginBase implements Listener {
    
    /**
     * @return Config data.json
     */
    private $data;
    
    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->data = new Config($this->getDataFolder() . "data.json", Config::JSON);
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);    
    }
    
    public function onDisable() {
        $this->saveResource("data.yml");
    }
    
    public function onJoin(PlayerJoinEvent $event) {
        if($this->hasOldAcount($event->getPlayer())) {
            $player = $event->getPlayer(); 
            $player->kick("transfer old playername data to your new account");
            
            $uuid = $player->getUniqueId()->toString();
            $old = $this->data->get($uuid);
            $new = $player->getName();
            $this->uptadeAcount($old, $new, $uuid);
            $this->data->set($uuid, strtolower($new));
            $this->data->save();
        }
    }
    
    /**
     * @param Player $player
     * 
     * @return true | false
     */
    public function hasOldAcount(Player $player): bool {
        $uuid = $player->getUniqueId()->toString();
        if($this->data->exists($uuid)) {
            if($this->data->get($uuid) == strtolower($player->getName())) {
                return false;
            } else {
                return true;
            }
            var_dump($uuid);
        } else {
            $this->data->set($uuid, strtolower($player->getName()));
            $this->data->save();
            return false;
        }
    }
    
    /**
     * @param string $old
     * @param string @new
     */
    public function uptadeAcount(string $old, string $new , $uuid) {
        $olfilename = $this->getServer()->getDataPath() . "players/" . strtolower($old) . ".dat";
        $newfilename = $this->getServer()->getDataPath() . "players/" . strtolower($new) . ".dat";
        rename($olfilename, $newfilename);
        $this->getLogger()->notice("player '$new'(uuid : '$uuid') have change usermame and data are done successfully");
    }
}
