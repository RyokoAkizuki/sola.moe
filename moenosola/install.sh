#!/bin/sh

apt-get install php5-dev

git clone https://github.com/CopernicaMarketingSoftware/PHP-CPP.git
cd PHP-CPP
make -j8
make test
make install

cd ..
make -j8
make install
service php5-fpm restart
