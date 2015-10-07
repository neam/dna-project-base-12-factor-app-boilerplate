<?php

$config['modules']['p3media']['params']['presets'] = array_merge($config['modules']['p3media']['params']['presets'], array(
    'related-thumb' => array(
        'name' => 'Related Panel Thumbnail',
        'commands' => array(
            'resize' => array(200, 200, 2),
            'quality' => '100',
        ),
        'type' => 'jpg',
    ),
    'item-list-thumbnail' => array(
        'name' => 'Item Thumbnail',
        'commands' => array(
            'resize' => array(110, 70, 2),
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    'wide-profile-info-picture' => array(
        'name' => 'Wide Profile Info Picture',
        'commands' => array(
            'resize' => array(110, 110, 7),
            'quality' => 85,
        ),
    ),
    'user-profile-picture' => array(
        'name' => 'User Profile Picture',
        'commands' => array(
            'resize' => array(160, 160, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    'user-profile-picture-large' => array(
        'name' => 'User Profile Picture Large',
        'commands' => array(
            'resize' => array(262, 262, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    'user-profile-picture-small' => array(
        'name' => 'User Profile Picture Small',
        'commands' => array(
            'resize' => array(70, 70, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    'user-profile-picture-mini' => array(
        'name' => 'User Profile Picture Mini',
        'commands' => array(
            'resize' => array(25, 25, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    'original-public-webm' => array(
        //'name'         => 'Original File Public',
        'originalFile' => true,
        'savePublic' => true,
        'type' => 'webm',
    ),
    'original-public-mp4' => array(
        //'name'         => 'Original File Public',
        'originalFile' => true,
        'savePublic' => true,
        'type' => 'mp4',
    ),
    'sir-trevor-image-block' => array(
        'name' => 'Sir Trevor Image Block',
        'commands' => array(
            'resize' => array(600, 600, 2), // Image::AUTO
            'quality' => '85',
        ),
        'savePublic' => true,
        'type' => 'jpg',
    ),
    /*
    'large' => array(
        'name' => 'Large JPG 1600px',
        'commands' => array(
            'resize' => array(1600, 1600, 2), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    'medium' => array(
        'name' => 'Medium PNG 800px',
        'commands' => array(
            'resize' => array(800, 800, 2), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'png',
    ),
    'medium-crop' => array(
        'name' => 'Medium PNG cropped 800x600px',
        'commands' => array(
            'resize' => array(800, 600, 7), // crop
            'quality' => '85',
        ),
        'type' => 'png',
    ),
    'small' => array(
        'name' => 'Small PNG 400px',
        'commands' => array(
            'resize' => array(400, 400, 2), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'png',
    ),
    'icon-32' => array(
        'name' => 'Icon PNG 32x32',
        'commands' => array(
            'resize' => array(32, 32),
        ),
        'type' => 'png'
    ),
    'download' => array(
        'name' => 'Download File',
        'originalFile' => true,
        'attachment' => true,
    ),
    */
    'original' => array(
        //'name'         => 'Original File', // uncomment name to enable preset in dropdowns
        'originalFile' => true,
    ),
    'original-public' => array(
        //'name'         => 'Original File Public',
        'originalFile' => true,
        'savePublic' => true,
    ),
    /*
    'p3media-ckbrowse' => array(
        'commands' => array(
            'resize' => array(150, 120), // use third parameter for master setting, see Image constants
            #'quality' => 80, // for jpegs
        ),
        'type' => 'png'
    ),
    'p3media-manager' => array(
        'commands' => array(
            'resize' => array(300, 200), // use third parameter for master setting, see Image constants
            #'quality' => 80, // for jpegs
        ),
        'type' => 'png'
    ),
    'p3media-upload' => array(
        'commands' => array(
            'resize' => array(60, 30), // use third parameter for master setting, see Image constants
            #'quality' => 80, // for jpegs
        ),
        'type' => 'png'
    ),
     */
    '735x444' => array(
        'name' => 'Item Thumbnail 735x444',
        'commands' => array(
            'resize' => array(735, 444, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '735x444-retina' => array(
        'name' => 'Item Thumbnail 735x444 (Retina)',
        'commands' => array(
            'resize' => array(1470, 888, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '160x96' => array(
        'name' => 'Item Thumbnail 160x96',
        'commands' => array(
            'resize' => array(160, 96, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '160x96-retina' => array(
        'name' => 'Item Thumbnail 160x96 (Retina)',
        'commands' => array(
            'resize' => array(320, 192, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '110x66' => array(
        'name' => 'Item Thumbnail 110x66',
        'commands' => array(
            'resize' => array(110, 66, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '110x66-retina' => array(
        'name' => 'Item Thumbnail 110x66 (Retina)',
        'commands' => array(
            'resize' => array(220, 132, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '130x77' => array(
        'name' => 'Item Thumbnail 130x77',
        'commands' => array(
            'resize' => array(130, 77, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '130x77-retina' => array(
        'name' => 'Item Thumbnail 130x77 (Retina)',
        'commands' => array(
            'resize' => array(260, 154, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '180x108' => array(
        'name' => 'Item Thumbnail 180x108',
        'commands' => array(
            'resize' => array(180, 108, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    '180x108-retina' => array(
        'name' => 'Item Thumbnail 180x108 (Retina)',
        'commands' => array(
            'resize' => array(360, 216, 7), // Image::AUTO
            'quality' => '85',
        ),
        'type' => 'jpg',
    ),
    'icon-80' => array(
        'name' => 'Icon PNG 80x80',
        'commands' => array(
            'resize' => array(80, 80, 3), // Image::HEIGHT
        ),
        'type' => 'png'
    ),
    'navtree-icon' => array(
        //'name'         => 'Original File Public',
        'originalFile' => true,
        'savePublic' => true,
    ),
));
