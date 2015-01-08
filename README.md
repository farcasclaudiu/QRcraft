#QRcraft

Plugin for PocketMine-MP for creating QR code panels (made from white and black wool blocks)

     Copyright (C) 2014 Clodyx <https://github.com/farcasclaudiu/QRcraft>

     This program is free software: you can redistribute it and/or modify
     it under the terms of the GNU Lesser General Public License as published by
     the Free Software Foundation, either version 3 of the License, or
     (at your option) any later version.

     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.


## Commands

ONLY OPs during gameplay (creative preferred)
* `/qr` - shows help page
* `/qrt <url>` - test QR code text/url to count number of needed blocks (NxN)
* `/qrc <url> [Auto|horizontal|vertical]` - create QR code panel
* `/qrl` - list QR code panels IDs
* `/qrd <ID>` - delete QR code panel by ID (fills panel space with air)
* `/qrp <ID>` - teleports the player nearby QR code panel with specified ID

Example:
* `/qrt http://google.com`
```
"QR panel for 'http://google.com' will need 27x27 blocks"
```
* `/qrc http://google.com` - begins the creation for specified url in auto mode
```
"QR panel defined a (27x27) for 'http://google.com'"
"Touch a block to create it!"
    - NOW the player has to touch another block to create the QR code panel.
    - the new panel will be created above touched block and to the player right side.
    - in auto mode, is player is inclined forward pointing downwards, 
        the QR code panel will be generated horizontally,
        otherwise it will be generated vertically.
"QR panel [1] created OK!"
```
* `/qrc http://google.com h` - begins the creation for specified url in horizontal mode
```
"QR panel defined h (27x27) for 'http://google.com'"
"Touch a block to create it!"
    - now the player touches the block to build above.
"QR panel [2] created OK!"
```
* `/qrl` - list current defined QR code panels
```
 "--------------"
 "QR LIST"
 "--------------"
 "[1] 'http://google.com' (27) x:10-y:20-z:30"
 "[2] 'http://google.com' (27) x:30-y:20-z:30"
 "--------------"
```
* `/qrd 2` - deletes the QR panel with ID 2
```
"QR panel [2] deleted."
```
* `/qrp 2` - teleports the player nearby QR panel with ID 2
```
"You've been teleported nearby QR panel [2]."
```

## Configuration
No configuration needed.

## Permissions

| Permission | Default | Description |
| :---: | :---: | :--- |
| clodyx.plugin.qrcraft | op | Allows using the QRcraft plugin functionality |


## Requirements
PocketMine-MP version min 1.0.0

## Releases
```
[1.0.0] - initial release
[1.0.1] - improvements
[1.0.2] - implemented teleport player nearby QR code panel
```
