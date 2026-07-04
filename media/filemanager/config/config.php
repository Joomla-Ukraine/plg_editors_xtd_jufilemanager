<?php
$version = "9.16.0-".time();

if (session_id() === '') {
    session_start();
}

error_reporting(E_ERROR | E_PARSE);

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input();
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');
date_default_timezone_set('Europe/Rome');
setlocale(LC_CTYPE, 'en_US');

define('USE_ACCESS_KEYS', true);
define('DEBUG_ERROR_MESSAGE', false);

$config = [
    'base_url' => ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on") ? "https" : "http")."://".($_SERVER['HTTP_HOST'] ?? 'localhost'),

    'upload_dir' => '/images/',
    'thumbs_upload_dir' => '/thumbs/',
    'current_path' => '../../../images/',
    'thumbs_base_path' => '../../../cache/thumbs/',
    'mime_extension_rename' => true,

    /*
    |--------------------------------------------------------------------------
    | Multiple files selection
    |--------------------------------------------------------------------------
    | The user can delete multiple files, select all files , deselect all files
    */
    'multiple_selection' => true,
    /*
    |
    | The user can have a select button that pass a json to external input or pass the first file selected to editor
    | If you use responsivefilemanager tinymce extension can copy into editor multiple object like images, videos, audios, links in the same time
    |
     */
    'multiple_selection_action_button' => true,

    'access_keys' => array(
        'akey' => hash_hmac('sha256', 'rf-access:'.floor(time() / 300), '2IT1cU9OyfHVFUmiGD6zOaTv4L7SJMRG'),
    ),

    'MaxSizeTotal' => false,

    'MaxSizeUpload' => 50,

    'filePermission' => 0644,
    'folderPermission' => 0755,

    'default_language' => "en_EN",

    'icon_theme' => "ico",

    'show_total_size' => false,
    'show_folder_size' => false,
    'show_sorting_bar' => true,
    'show_filter_buttons' => true,
    'show_language_selection' => true,
    'transliteration' => true,
    'convert_spaces' => true,
    'replace_with' => "_",
    'lower_case' => false,
    'add_time_to_img' => false,


    //*******************************************
    //Images limit and resizing configuration
    //*******************************************

    // set maximum pixel width and/or maximum pixel height for all images
    // If you set a maximum width or height, oversized images are converted to those limits. Images smaller than the limit(s) are unaffected
    // if you don't need a limit set both to 0
    'image_max_width' => 0,
    'image_max_height' => 0,
    'image_max_mode' => 'auto',
    /*
    #  $option:  0 / exact = defined size;
    #            1 / portrait = keep aspect set height;
    #            2 / landscape = keep aspect set width;
    #            3 / auto = auto;
    #            4 / crop= resize and crop;
    */

    //Automatic resizing //
    // If you set $image_resizing to TRUE the script converts all uploaded images exactly to image_resizing_width x image_resizing_height dimension
    // If you set width or height to 0 the script automatically calculates the other dimension
    // Is possible that if you upload very big images the script not work to overcome this increase the php configuration of memory and time limit
    'image_resizing' => false,
    'image_resizing_width' => 0,
    'image_resizing_height' => 0,
    'image_resizing_mode' => 'auto',
    // same as $image_max_mode
    'image_resizing_override' => false,

    'image_watermark' => false,
    'image_watermark_position' => 'br',
    'image_watermark_padding' => 10,

    'default_view' => 0,

    'ellipsis_title_after_first_row' => true,

    'delete_files' => true,
    'create_folders' => true,
    'delete_folders' => true,
    'upload_files' => true,
    'rename_files' => true,
    'rename_folders' => true,
    'duplicate_files' => true,
    'extract_files' => false,
    'copy_cut_files' => true,
    'copy_cut_dirs' => true,
    'chmod_files' => false,
    'chmod_dirs' => false,
    'preview_text_files' => false,
    'edit_text_files' => false,
    'create_text_files' => false,
    'download_files' => true,

    'previewable_text_file_exts' => array('txt', 'log', 'xml', 'css'),
    'editable_text_file_exts' => [],

    'jplayer_exts' => array(
        "mp4",
        "flv",
        "webmv",
        "webma",
        "webm",
        "m4a",
        "m4v",
        "ogv",
        "oga",
        "mp3",
        "midi",
        "mid",
        "ogg",
        "wav",
    ),

    'cad_exts' => array('dwg', 'dxf', 'hpgl', 'plt', 'spl', 'step', 'stp', 'iges', 'igs', 'sat', 'cgm', 'svg'),

    'googledoc_enabled' => true,
    'googledoc_file_exts' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'odt', 'odp', 'ods'),

    'copy_cut_max_size' => 100,
    'copy_cut_max_count' => 200,

    'ext_img' => array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'ico', 'webp'),
    'ext_file' => array('doc', 'docx', 'xls', 'xlsx', 'pdf'),
    'ext_video' => array('mov', 'mpeg', 'm4v', 'mp4', 'avi', 'mpg', 'wma', "flv", "webm"),
    'ext_music' => array('mp3', 'mpga', 'm4a', 'ac3', 'aiff', 'mid', 'ogg', 'wav'),
    'ext_misc' => array('zip', 'rar', 'gz', 'tar'),

    'ext_blacklist' => [
        'php',
        'php3',
        'php4',
        'php5',
        'php7',
        'php8',
        'phtml',
        'phar',
        'phps',
        'cgi',
        'pl',
        'py',
        'pyc',
        'pyo',
        'sh',
        'bash',
        'zsh',
        'ksh',
        'csh',
        'exe',
        'com',
        'bat',
        'cmd',
        'vbs',
        'vbe',
        'js',
        'jse',
        'wsf',
        'wsh',
        'ps1',
        'psm1',
        'msi',
        'msp',
        'mst',
        'scr',
        'pif',
        'dll',
        'sys',
        'drv',
        'asp',
        'aspx',
        'ashx',
        'asmx',
        'cshtml',
        'vbhtml',
        'jsp',
        'jspx',
        'cfm',
        'cfc',
        'shtml',
        'htaccess',
        'htpasswd',
        'ini',
        'config',
        'env',
        'rb',
        'erb',
        'go',
        'rs',
        'java',
        'class',
        'war',
        'jar',
    ],

    'empty_filename' => false,
    'files_without_extension' => false,
    'file_number_limit_js' => 500,
    'hidden_folders' => ['tmp'],
    'hidden_files' => ['config.php', 'index.html', 'index.htm'],
    'url_upload' => false,


    //************************************
    //Thumbnail for external use creation
    //************************************


    // New image resized creation with fixed path from filemanager folder after uploading (thumbnails in fixed mode)
    // If you want create images resized out of upload folder for use with external script you can choose this method,
    // You can create also more than one image at a time just simply add a value in the array
    // Remember than the image creation respect the folder hierarchy so if you are inside source/test/test1/ the new image will create at
    // path_from_filemanager/test/test1/
    // PS if there isn't write permission in your destination folder you must set it
    //
    'fixed_image_creation' => false,
    //activate or not the creation of one or more image resized with fixed path from filemanager folder
    'fixed_path_from_filemanager' => array('../test/', '../test1/'),
    //fixed path of the image folder from the current position on upload folder
    'fixed_image_creation_name_to_prepend' => array('', 'test_'),
    //name to prepend on filename
    'fixed_image_creation_to_append' => array('_test', ''),
    //name to appendon filename
    'fixed_image_creation_width' => array(300, 400),
    //width of image
    'fixed_image_creation_height' => array(200, 300),
    //height of image
    /*
    #             $option:     0 / exact = defined size;
    #                          1 / portrait = keep aspect set height;
    #                          2 / landscape = keep aspect set width;
    #                          3 / auto = auto;
    #                          4 / crop= resize and crop;
    */
    'fixed_image_creation_option' => array('crop', 'auto'),
    //set the type of the crop


    // New image resized creation with relative path inside to upload folder after uploading (thumbnails in relative mode)
    // With Responsive filemanager you can create automatically resized image inside the upload folder, also more than one at a time
    // just simply add a value in the array
    // The image creation path is always relative so if i'm inside source/test/test1 and I upload an image, the path start from here
    //
    'relative_image_creation' => false,
    //activate or not the creation of one or more image resized with relative path from upload folder
    'relative_path_from_current_pos' => array('./', './'),
    //relative path of the image folder from the current position on upload folder
    'relative_image_creation_name_to_prepend' => array('', ''),
    //name to prepend on filename
    'relative_image_creation_name_to_append' => array('_thumb', '_thumb1'),
    //name to append on filename
    'relative_image_creation_width' => array(300, 400),
    //width of image
    'relative_image_creation_height' => array(200, 300),
    //height of image
    /*
    #             $option:     0 / exact = defined size;
    #                          1 / portrait = keep aspect set height;
    #                          2 / landscape = keep aspect set width;
    #                          3 / auto = auto;
    #                          4 / crop= resize and crop;
    */
    'relative_image_creation_option' => array('crop', 'crop'),
    //set the type of the crop

    'remember_text_filter' => false,
    'tui_active' => true,
    'tui_position' => 'bottom',

    'dark_mode' => false,
    'remove_header' => true,
];

return array_merge(
    $config,
    [
        'ext' => array_merge(
            $config['ext_img'],
            $config['ext_file'],
            $config['ext_misc'],
            $config['ext_video'],
            $config['ext_music']
        ),
    ]
);
