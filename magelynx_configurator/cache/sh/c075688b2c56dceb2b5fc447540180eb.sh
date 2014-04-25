#!/bin/bash
            images=("/home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/svg/ffbba9316c1bce0b8712e9904f5b24ee.svg")
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
                    convert -background none MIFF:- -layers merge +repage /home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/preview/c075688b2c56dceb2b5fc447540180eb.png