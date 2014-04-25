#!/bin/bash
            images=("/home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/svg/20c27b9e10eb369f3528999274d1eee9.svg")
            sizes=("425x425")
            offset=("+0+0")
                for index in {0..0}
                    do
                    if [[ "${images[index]}" == *".svg" ]];
                    then
                        convert -background none MSVG:${images[index]} -resize ${sizes[index]} -gravity center -background none -extent ${sizes[index]} -repage ${sizes[index]}${offset[index]}\! miff:-
                    else
                        convert ${images[index]} -resize ${sizes[index]} -gravity center -background none -extent ${sizes[index]} -repage ${sizes[index]}${offset[index]}\! miff:-
                    fi
                    done |
                    convert -background none MIFF:- -layers merge +repage /home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/preview/b693834110cf1a50648087ba9b4de2c7.png