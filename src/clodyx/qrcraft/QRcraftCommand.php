<?php
/**
 *
 *QRcraft Command
 *
 * @author: Clodyx
 *
 */

namespace clodyx\qrcraft;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\Player;


class QRcraftCommand
{

    private $pgin;

    public function __construct(QRcraftPlugIn $pg)
    {
        $this->pgin = $pg;
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        $cmd = strtolower($command->getName());
        if ($sender instanceof Player) {
            $player = $sender->getPlayer();

            switch ($cmd) {
                case "qr":
                    $this->showusage($sender);
                    break;
                case "qrt":
                    if (isset ($args [0])) {
                        $url = $args[0];
                        $size = QRhelper::TestQRCode(true, $url);
                        $sender->sendMessage("QR panel for '$url' will need " . $size . "x" . $size . " blocks");
                    } else {
                        $this->showusage($sender);
                    }
                    break;
                case "qrc":
                    if (isset ($args [0])) {
                        $url = $args[0];
                        $orientation = isset ($args [1]) ? strtolower($args[1]) : "a";//a - auto
                        $allor = array("h", "v", "a");
                        if (!in_array($orientation, $allor)) {
                            $this->showusage($sender);
                        } else {
                            QRhelper::MarkPlayerCanCreateQR($this->pgin, $player, $url, $orientation);
                        }
                    } else {
                        $this->showusage($sender);
                    }
                    break;
                case "qrl":
                    if (!isset($this->pgin->qrlist) || count($this->pgin->qrlist) == 0) {
                        $player->sendMessage("QR panels list is empty!");
                    } else {
                        $player->sendMessage("--------------");
                        $player->sendMessage("QR LIST");
                        $player->sendMessage("--------------");
                        foreach ($this->pgin->qrlist as $li) {
                            $liId = $li[0];
                            $liText = $li[1];
                            $liSize = $li[2];
                            $coord = $li[3];
                            $liCoord = "x:" . $coord[0] . "-y:" . $coord[1] . "-z:" . $coord[2];//implode("-",$li[3]);
                            $player->sendMessage("[$liId] '$liText' ($liSize) $liCoord");
                        }
                        $player->sendMessage("--------------");
                    }
                    break;
                case "qrd":
                    if (!isset($this->pgin->qrlist) || count($this->pgin->qrlist) == 0) {
                        $player->sendMessage("QR panel list is empty!");
                    } else {
                        if (isset ($args [0])) {
                            $idToDelete = $args[0];
                            $wasDeleted = false;
                            foreach ($this->pgin->qrlist as $li) {
                                if ($idToDelete == $li[0]) {
                                    //delete in world (fill with air)
                                    $coord = $li[3];
                                    QRhelper::FillWithAir($player->level, $coord);
                                    //delete from file
                                    unset($this->pgin->qrlist[array_search($li, $this->pgin->qrlist)]);
                                    QRhelper::SaveQRlist($this->pgin);
                                    $wasDeleted = true;
                                    $player->sendMessage("QR panel [$idToDelete] deleted.");
                                    break;
                                }
                            }
                            if (!$wasDeleted)
                                $player->sendMessage("QR panel [$idToDelete] not found.");
                        } else {
                            $this->showusage($sender);
                        }

                    }
                    break;
                case "qrp":
                    //todo
                    break;
                default:
                    $this->showusage($sender);
                    break;
            }

            return true;
        } else {
            $this->showusage($sender);
        }
    }

    public function showusage(CommandSender $player)
    {
        //usage
        $player->sendMessage("QRcraft usage:");
        $player->sendMessage("/qr - help page");
        $player->sendMessage("/qrt <url> - test QR text size");
        $player->sendMessage("/qrc <url> [Auto|horizontal|vertical] - create qr panel");
        $player->sendMessage("/qrl - list QR panels IDs");
        $player->sendMessage("/qrd <ID> - detele QR panel by ID");
        $player->sendMessage("/qrp <ID> - teleport nearby QR panel by ID");
    }
}