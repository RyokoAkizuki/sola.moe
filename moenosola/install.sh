#!/bin/sh

case "$1" in
depend)
    apt-get install php5-dev

    git clone https://github.com/CopernicaMarketingSoftware/PHP-CPP.git
    cd PHP-CPP
    make -j8
    make test
    make install
    cd ..
    ;;

install)
    make clean
    make -j8
    make install
    service php5-fpm restart
    ;;

*)
    echo "Usage: install.sh {depend|install}"
    exit 1
    ;;
esac

exit 0
