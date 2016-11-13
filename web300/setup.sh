#!/bin/bash

# place this file here: /root/setup.sh
# and don't forget to :  chmod +x /root/setup.sh

set -e

echo "Sleeping 5 seconds to sure apache is up"
sleep 5

echo "Caching flag.php"
curl -sq 'http://127.0.0.1/flag.php' -o /dev/null

echo "Overwriting flag.php"
cat << EOF > /var/www/flag.php
<?php

function flag() {
    \$flag = 'DCTF{OMG_OMG_OMG_THIS_ISNT_THE_FLAG}';
    return strlen(\$flag);
}

echo flag();
EOF

echo "Removing htaccess"
rm /var/www/.htaccess
