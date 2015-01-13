<?php
/**
 *
 *QRcraft plugin
 *
 * @author: Clodyx
 *
 */

namespace clodyx\qrcraft;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use clodyx\qrcraft\QRcraftCommand;
use pocketmine\Player;


class QRcraftPlugIn extends PluginBase implements CommandExecutor
{

    public $cmd;
    private $sessions;
    public $config;
    public $qrlist = array();

    public function onLoad()
    {
        QRhelper::$log = $this->getLogger();
        $this->cmd = new QRcraftCommand ($this);
        $this->log(TextFormat::GREEN . "QRcraft - Loaded");
    }

    public function onEnable()
    {
        $this->setEnabled(true);
        $this->loadConfiguration();
        $this->qrlist = QRhelper::LoadQR($this);
        $this->getServer()->getPluginManager()->registerEvents(new QRcraftPlugInListener($this), $this);
        $this->log(TextFormat::GREEN . "QRcraft - Enabled");
    }


    public function loadConfiguration()
    {
        $this->saveDefaultConfig();
    }

    public function onDisable()
    {
        $this->setEnabled(false);
        $this->log(TextFormat::RED . "QRcraft - Disabled");
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        $this->cmd->onCommand($sender, $command, $label, $args);
    }


    public function &session(Player $player)
    {
        $userName = $player->getName();
        if (!isset($this->sessions[$userName])) {
            $this->sessions[$userName] = array(
                "create" => false,
                "url" => "",
                "size" => 0,
                "orientation" => "a",
            );
        }
        return $this->sessions[$userName];
    }

    public function log($msg)
    {
        $this->getLogger()->info($msg);
    }


}
