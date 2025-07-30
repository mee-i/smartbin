<?php

$relative_path = "../";
require '../usermanager.php';

if (!$_SESSION["IsAdmin"]) {
    header("Location: /");
}

// var_dump($fileContents);

if (getData($_SESSION['Username'], DATA_LEVEL) != "DEVELOPER_ADMIN") {
    header("Location: /admin/");
}

function getlist($dir) {
    // if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    //     return @scandir($dir);
    // }
    // else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
    //     @exec("ls -F " . escapeshellarg($dir), $output);
    //     return $output;
    // }
    return @scandir($dir);
}
$GLOBALS['newdir'] = '';
function show_list($dir) {
    // echo "newdir:";
    // var_dump($GLOBALS['newdir']);
    // echo "<br>";
    $icon_array = array(
        'image' => 'bxs-image-alt'
    );
    $files = array();
    $folders = array();
    $list = @getlist($dir);
    if (!$list) {
        echo "Cannot access " . $dir;
        return;
    }
    foreach ($list as $childdir) {
        if (is_dir($dir . $childdir))
            array_push($folders, $childdir . '/');
        else
            array_push($files, $childdir);
    }
    $table = "";

    $table .= 
        "<tr>
            <th>Name</th>
            <th>Size</th>
        </tr>";
    if ($dir != "/") {
        $table .= "<tr onclick=\"openroot()\">";
        $table .= "<td><i class='bx bxs-arrow-from-bottom'></i>/</td>";
        $table .= "<td>-</td>";
        $table .= "<td></td>";
        $table .= "</tr>";
    }
    foreach ($folders as $folder) {
        if ($folder == '../') {
            $table .= 
                "<tr onclick='updir()'>
                    <td><i class='bx bxs-up-arrow-alt' style='font-weight: bold;'></i>. .</td>
                    <td>-</td>
                    <td></td>
                </tr>";
            continue;
        }
        else if ($folder == './') continue;
        if ($GLOBALS['mode'] == 'mkdir')
            $table .= "<tr" . ((basename($GLOBALS['newdir']) == basename($folder))? " newdir" : "") . " dir=\"" . str_replace("'", "\\'", $folder) . "\">";
        else
            $table .= "<tr dir=\"" . str_replace("'", "\\'", $folder) . "\">";
        // echo $folder . " ";
        // @var_dump(basename($GLOBALS['newdir']) == basename($folder));
        // echo "<br>";
        $table .= "<td onclick='dirclick(this)'><i class='bx bxs-folder'></i><span class='dirname'>" . str_replace("/", "", $folder) . "</span></td>";
        $table .= "<td>-</td>";
        $table .= "<td onclick='openmore(this)'><i class='bx bx-dots-horizontal-rounded'></i></td>";
        $table .= "</tr>";
    }
    foreach ($files as $file) {
        $table .= "<tr dir=\"" . str_replace("'", "\\'", $file) . "\">";
        $table .= "<td onclick='fileclick(this)'><i class='bx bxs-file-blank'>";
        $table .= "</i><span class='dirname'>" . $file . "</span></td>";
        $table .= "<td>" . @number_format(ceil(filesize($dir.$file) / 1024)) . " KB</td>";
        $table .= "<td onclick='openmore(this)'><i class='bx bx-dots-horizontal-rounded'></i></td>";
        $table .= "</tr>";
    }
    echo $table;
}


function mode_rename($dir) {
    $newname = $_GET['newname'];
    $parentdir = dirname($dir);
    // echo $parentdir . "<br>";
    // echo $parentdir.'/'.$newname;
    @rename($dir, $parentdir.'/'.$newname);
    show_list($parentdir . '/');
}

function mode_delete($dir) {
    if (is_file($dir)) {
        unlink($dir);
    }
    else if (is_dir($dir)) {
        $output = array();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("rmdir /s /q " . preg_replace("/\//", "\\",  escapeshellarg($dir)), $output);
        }
        else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
            exec("rm -r " . escapeshellarg($dir), $output);
        }
        echo implode("\n", $output);
    }
    show_list(dirname($dir) . '/');
}

function mode_newfolder($dir) {
    $newfolder_dir = $dir . "New Folder";
    $folder_number = 0;
    while (file_exists($newfolder_dir) || is_dir($newfolder_dir)) {
        $folder_number++;
        $newfolder_dir = $dir . "New Folder (" . $folder_number . ")";
    }
    $GLOBALS['newdir'] = $newfolder_dir . '/';
    mkdir($newfolder_dir);
    chmod($newfolder_dir, 0777);
    show_list($dir);
}

// $dir = "/";
// if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
//     $dir = "C:" . $dir;
$mode = '';
if (isset($_GET['mode']))
    $mode = $_GET['mode'];
$dir = "/home/smartbin/";
if (isset($_GET['dir'])) {
    $dir = $_GET['dir'];
}


// var_dump($dir);
// var_dump(is_file($dir));
if (is_file($dir) && $mode == 'open') {
    header("Content-type: " . mime_content_type($dir));
    header("Content-Disposition: inline; filename=".basename($dir));
    readfile($dir);
    exit;
}

// chown($dir, 'www-data:www-data');
// chmod($dir, 0755);


if ($mode != 'open' && $mode != '') {
    if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
        // exec("chown -R www-data:www-data " . escapeshellarg($dir));
        // exec("chmod 766 " . escapeshellarg($dir));
    }
    if ($mode == 'rename')
        mode_rename($dir);
    else if ($mode == 'delete')
        mode_delete($dir);
    else if ($mode == 'mkdir')
        mode_newfolder($dir);
    else if ($mode == 'upload') {
        $tmp_name = $_FILES["file"]["tmp_name"];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
        $name = basename($_FILES["file"]["name"]);
        move_uploaded_file($tmp_name, $dir.$name);
        chmod($dir.$name, 0777);
        show_list($dir);
        // var_dump($_FILES);
    }
    exit;
}


if (isset($_GET['tableonly'])) {
    show_list($dir);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SmartBin - File Manager</title>
        <link rel="stylesheet" href="../getfile.php?file=filemanager/style.css">
        <link href='/icon/icon.css' rel='stylesheet'>
    </head>
    <body>
        <header>
            <h3 class="logo">SmartBin</h3>
            <h3>File Manager</h3>
        </header>
        <div class="container">
            <span id="directory">/</span>
            <div id="loader"><div hidden></div></div>
            <table id="item-list" class="item-list">
                <?php
                show_list($dir);
                ?> 
            </table>
        </div>
        <div class="button-bottom">
            <div style="padding: 0px; display: inline-block;" class="input-container input">
                <label id="file-input" for="uploadfile">
                <i class="bx bx-upload" class="select-file"></i>
                </label>
                <input multiple hidden id="uploadfile" type="file" placeholder="Select file" />
            </div>
            <i onclick='newFolder()' class="bx bx-folder-plus"></i>
        </div>

        <!-- More Menu -->
        <div style="display: none;" id="more-menu">
            <div onclick="renamedir()"><i class="bx bx-rename"></i>Rename</div>
            <div onclick="deletedir()" class="delete-btn"><i class="bx bx-trash"></i>Delete</div>
        </div>

        <!-- Drop hint -->
        <div hidden id="drop-hint">
            <div class="flexbox">
                <div>
                    Drop file(s) here
                </div>
            </div>
        </div>

        <script src="../getfile.php?file=filemanager/script2.js"></script>
        <script>
            <?php
            echo "FileManager.dir = '" . $dir . "';";
            ?>
            updateDir();
            history.replaceState({ dir: FileManager.dir }, '', '');
        </script>
    </body>
</html>