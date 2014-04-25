#!/bin/bash
            images=("/home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/svg/62c20c772bd0bddc7065ebb42fdb843f.svg")
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
                    convert -background none MIFF:- -layers merge +repage /home/admin/domains/tomobile.nl/public_html/magelynx_configurator/cache/preview/87810907aafd30bdf3a892c9e36d2e6c.png