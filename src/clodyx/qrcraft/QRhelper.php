<?php
/**
 *
 *QRcraft Helper
 *
 * @author: Clodyx
 *
 */

namespace clodyx\qrcraft;


include(__DIR__ . "\..\..\clodyx\phpqrcode\phpqrcode.php");

use pocketmine\math\Vector3;
use pocketmine\block\Block as BlockAPI;
use pocketmine\Player;
use pocketmine\utils\Config;
use clodyx\phpqrcode as QR;


class QRhelper
{

    public static $log;

    public static function log($msg)
    {
        QRhelper::$log->info($msg);
    }


    public static function TestQRCode($addMargin, $textToQR)
    {
        $qrTab = QR\QRcode::text($textToQR, false, QR_ECLEVEL_L, 1, 1);
        $size = count($qrTab);

        //add margins
        if ($addMargin) {
            $margin = 1;
            $qrTabWithMargins = array();
            for ($i = 0; $i < $margin; $i++) {
                $qrTabWithMargins[] = str_repeat("0", $size + 2 * $margin);
            }
            foreach ($qrTab as $line) {
                $qrTabWithMargins[] = str_repeat("0", $margin) . $line . str_repeat("0", $margin);
            }
            for ($i = 0; $i < $margin; $i++) {
                $qrTabWithMargins[] = str_repeat("0", $size + 2 * $margin);
            }
            //reassign qrtab
            $qrTab = $qrTabWithMargins;
            $size = count($qrTab);
        }
        return $size;
    }


    public static function CreateQRCode($blockTouched, $level, $addMargin, $textToQR, $build)
    {
        $genCoord = array();

        #region qrcode wool blocks generation

        $qrTab = QR\QRcode::text($textToQR, false, QR_ECLEVEL_L, 1, 1);
        $size = count($qrTab);

        //add margins
        if ($addMargin) {
            $margin = 1;
            $qrTabWithMargins = array();
            for ($i = 0; $i < $margin; $i++) {
                $qrTabWithMargins[] = str_repeat("0", $size + 2 * $margin);
            }
            foreach ($qrTab as $line) {
                $qrTabWithMargins[] = str_repeat("0", $margin) . $line . str_repeat("0", $margin);
            }
            for ($i = 0; $i < $margin; $i++) {
                $qrTabWithMargins[] = str_repeat("0", $size + 2 * $margin);
            }
            //reassign qrtab
            $qrTab = $qrTabWithMargins;
            $size = count($qrTab);
        }


        $isFirstBlock = true;
        for ($iRow = 0; $iRow < $size; $iRow++) {
            $line = $qrTab[$size - $iRow - 1];

            for ($iCol = 0; $iCol < $size; $iCol++) {
                $char = $line[$iCol];

                $bitVal = ($char == '1') ? 1 : 0;
                $sign = $build[2] == "1" ? 1 : -1;
                if ($build[0] == "v") {
                    //vertical
                    if ($build[1] == "x") {
                        $qrY = $blockTouched->getY() + 1 + $iRow;
                        $qrZ = $blockTouched->getZ();
                        $qrX = $blockTouched->getX() - $sign * $iCol;
                    } else if ($build[1] == "z") {
                        $qrY = $blockTouched->getY() + 1 + $iRow;
                        $qrZ = $blockTouched->getZ() + $sign * $iCol;
                        $qrX = $blockTouched->getX();
                    }
                } else {
                    //horizontal
                    if ($build[1] == "x") {
                        $qrY = $blockTouched->getY() + 1;
                        $qrZ = $blockTouched->getZ() + $sign * $iRow;
                        $qrX = $blockTouched->getX() - $sign * $iCol;
                    } else if ($build[1] == "z") {
                        $qrY = $blockTouched->getY() + 1;
                        $qrZ = $blockTouched->getZ() + $sign * $iCol;
                        $qrX = $blockTouched->getX() + $sign * $iRow;
                    }
                }

                QRhelper::CreateWoolBlock($level, $qrX, $qrY, $qrZ, $bitVal);

                if ($isFirstBlock) {
                    $genCoord[] = $qrX;
                    $genCoord[] = $qrY;
                    $genCoord[] = $qrZ;
                    $isFirstBlock = false;
                }
            }
        }

        //last block
        $genCoord[] = $qrX;
        $genCoord[] = $qrY;
        $genCoord[] = $qrZ;

        #endregion qrcode

        return $genCoord;
    }


    private static function CreateWoolBlock($level, $qrX, $qrY, $qrZ, $bitVal)
    {
        $id = 35;//wool
        $meta = ($bitVal === 0) ? 0 : 15;//wool color white / black
        $block = BlockAPI::get($id, $meta);
        $pos = new Vector3($qrX,
            $qrY,
            $qrZ);
        $level->setBlock($pos, $block);
    }


    public static function FillWithAir($level, $coords)
    {
        $x0 = $coords[0];
        $y0 = $coords[1];
        $z0 = $coords[2];
        $x1 = $coords[3];
        $y1 = $coords[4];
        $z1 = $coords[5];
        if($x0>$x1)
            self::swapValues($x0,$x1);
        if($y0>$y1)
            self::swapValues($y0,$y1);
        if($z0>$z1)
            self::swapValues($z0,$z1);
        for ($ix = $x0; $ix <= $x1; $ix++) {
            for ($iy = $y0; $iy <= $y1; $iy++) {
                for ($iz = $z0; $iz <= $z1; $iz++) {
                    $id = 0;//air
                    $meta = 0;
                    $block = BlockAPI::get($id, $meta);
                    $pos = new Vector3($ix, $iy, $iz);
                    $level->setBlock($pos, $block);
                }
            }
        }
    }

    private static function swapValues(&$x,&$y) {
        $tmp=$x;
        $x=$y;
        $y=$tmp;
    }


    public static function MarkPlayerCanCreateQR(QRcraftPlugIn $plugin, Player $player, $url, $orientation)
    {
        if (empty($url)) {
            $player->sendMessage("Url is empty!");
            return;
        }

        $size = QRhelper::TestQRCode(true, $url);

        $session =& $plugin->session($player);
        $session["url"] = $url;
        $session["orientation"] = $orientation;
        $session["size"] = $size;
        $session["create"] = true;


        $player->sendMessage("QR panel defined $orientation (" . $size . "x" . $size . ") for '" . $url . "'");
        $player->sendMessage("Touch a block to create it!");
    }

    public static function CreateQRAndUnmark(QRcraftPlugIn $plugin, Player $player, $blockTouched, $level, $addMargin)
    {

        $session =& $plugin->session($player);
        $toCreate = $session["create"];

        if ($toCreate) {
            $textToQR = $session["url"];
            $size = $session["size"];
            $orient = strtolower($session["orientation"]);
            $allor = array("h", "v", "a");
            if (!in_array($orient, $allor))
                $orient = "a";

            //calculate orientation
            $direction = $player->getDirectionVector();
            $orient = $orient[0] == "a" ?
                ((abs($direction->y) > .9) ? "h" : "v") :
                $orient;
            $build = $orient;
            if (abs($direction->z) > abs($direction->x)) {
                //we build blocks on X axis (east+/west-)
                $build = $build . "x" . ($direction->z > 0 ? "1" : "0");
            } else {
                //we build blocks on Z axis (south+/north-)
                $build = $build . "z" . ($direction->x > 0 ? "1" : "0");
            }

            //create qr block
            $genCoords = QRhelper::CreateQRCode($blockTouched, $level, $addMargin, $textToQR, $build);

            //save qr block in list
            $qrID = QRhelper::SaveQR($plugin, $textToQR, $size, $genCoords);

            //unmark
            $session["url"] = "";
            $session["orientation"] = "a";
            $session["create"] = false;
            $session["size"] = 0;

            $player->sendMessage("QR panel [$qrID] created OK!");
        }
    }


    public static function SaveQR(QRcraftPlugIn $plugin, $textToQR, $size, $genCoords)
    {
        if (!isset($plugin->qrlist))
            $plugin->qrlist = array();
        //calculate new ID
        $id = 0;
        foreach ($plugin->qrlist as $li) {
            $id = max($id, $li[0]);
        }
        $id++;//get next ID
        $data = array();
        //create new data item
        $data[] = $id;
        $data[] = $textToQR;
        $data[] = $size;
        $data[] = $genCoords;
        //add item to list
        $plugin->qrlist[] = $data;
        //save list
        self::SaveQRlist($plugin);
        //return the new id
        return $id;
    }

    public static function LoadQR(QRcraftPlugIn $plugin)
    {
        $path = QRhelper::GetQRfile($plugin);
        if (!file_exists($path)) {
            return null;
        } else {
            $config = new Config($path, Config::JSON);
            return $config->getAll();
        }
    }

    public static function GetQRfile(QRcraftPlugIn $plugin)
    {
        $filename = "qrlist";
        $path = $plugin->getDataFolder() . "/$filename.txt";
        return $path;
    }

    public static function SaveQRlist(QRcraftPlugIn $plugin)
    {
        //save list
        $path = QRhelper::GetQRfile($plugin);
        $config = new Config($path, Config::JSON);
        $config->setAll($plugin->qrlist);
        return $config->save();
    }
}