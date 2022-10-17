<?php

namespace roi611\blockprotection;

use pocketmine\plugin\PluginBase;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\Server;

use pocketmine\utils\Config;

use pocketmine\item\VanillaItems;
use pocketmine\item\Stick;

use pocketmine\inventory\Inventory;

class Main extends PluginBase implements Listener {

    public function onEnable():void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder()."Protection.yml", Config::YAML);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{

        if (!($sender instanceof Player)) {
            $sender->sendMessage("ゲーム内で実行してください");
            return true;
        }

        $item = VanillaItems::STICK();
        $item->setCustomName("§3保護スティック");
        $sender->getInventory()->addItem($item);
        $sender->sendMessage("§e保護スティックを付与しました");
        return true;

    }



    public function onBreak(BlockBreakEvent $event){

        $player = $event->getPlayer();

        $block = $event->getBlock();
        $p = $block->getPosition();
        $pos = $p->getFloorX().",".$p->getFloorY().",".$p->getFloorZ().",".$p->getWorld()->getDisplayName();

        if(!(Server::getInstance()->isOp($player->getName()))){

            if($this->config->exists($pos)){
                $player->sendMessage("そのブロックは破壊することができません");
                $event->cancel();
            }

        } else {

            $item = $event->getItem();
            $name = $item->getName();        

            if($item instanceof Stick && $name === "§3保護スティック"){

                if($this->config->exists($pos)){

                    $this->config->remove($pos);
                    $this->config->save();
                    $player->sendMessage("ブロックの保護を解除しました");
                    $event->cancel();

                } else {

                    $this->config->set($pos,true);
                    $this->config->save();
                    $player->sendMessage("ブロックを保護しました");
                    $event->cancel();

                }

            } else {

                if($this->config->exists($pos)){

                    $this->config->remove($pos);
                    $this->config->save();
                    $player->sendMessage("ブロック保護を解除しました");

                }

            }

        }

    }



}
