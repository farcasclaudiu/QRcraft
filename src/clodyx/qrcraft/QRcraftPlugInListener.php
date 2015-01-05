<?php
/**
 *
 *QRcraft Listener
 *
 * @author: Clodyx
 *
 */

namespace clodyx\qrcraft;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;


class QRcraftPlugInListener implements Listener
{

    public $pgin;

    public function __construct(QRcraftPlugIn $pg)
    {
        $this->pgin = $pg;
    }

    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $level = $event->getPlayer()->level;
        $player = $event->getPlayer();
        $blockTouched = $event->getBlock();

        if (\pocketmine\DEBUG > 1) {
            $direction = $player->getDirectionVector();
            $msg = "Player direction is X:" . round($direction->x, 2) . " Y:" . round($direction->y, 2) . " Z:" . round($direction->z, 2);
            $this->pgin->log($msg);
            $player->sendMessage($msg);
        }

        QRhelper::CreateQRAndUnmark($this->pgin, $player, $blockTouched, $level, true);
    }
}