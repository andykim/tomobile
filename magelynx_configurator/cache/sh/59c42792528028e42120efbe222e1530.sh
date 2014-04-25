#!/bin/bash
            images=("/home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/svg/20936292202151640bebc3b925f4fe80.svg")
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
                    convert -background none MIFF:- -layers merge +repage /home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/preview/59c42792528028e42120efbe222e1530.png