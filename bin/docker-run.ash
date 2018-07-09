#!/bin/ash

cp /bindmount/config.local.php /data

/usr/bin/php7 change-key.php
