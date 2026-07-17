#!/bin/bash
cd d:\laragon\www\iuran-rt001
php artisan reseed:kas-rt << EOF
yes
EOF
