<?php
include_once("Class.php");
$ex = new GetMostCommonColors();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>PHP Project</title>
</head>
<body>
    <div id="wrap">
        <form method="post" enctype="multipart/form-data">
            <div>
                <p>Upload Image</p>
                <br />
                <div>
                    <label>
                        File: <input type="file" name="imgFile" />
                    </label>
                </div>

                <br />
                <br />
                
                <div>
                    <input type="submit" name="action" value="Process" />
                </div>
            </div>
        </form>
     
        <?php
        // Was any file uploaded?
        if ($_FILES['imgFile']['tmp_name'] != null && strlen($_FILES['imgFile']['tmp_name']) > 0)
        {
            //Moving file to image folder
            if (!move_uploaded_file($_FILES['imgFile']['tmp_name'], 'images/'.$_FILES['imgFile']['name']))
            {
                die("Error moving uploaded file to images directory");
            }
            $colors = $ex -> Get_Color('images/'.$_FILES['imgFile']['name']);
        ?>
        <table>
            <tr>
                <td>Color</td><td>&nbsp Color Code</td><td> &nbsp Percentage</td><td rowspan="6:22500">
                    <img src="<?='images/'.$_FILES['imgFile']['name']?>" alt="404" />
                </td>
            </tr>
            <?php
            foreach ($colors as $hex => $count)
            {
                echo "<tr><td style=\"background-color:#".$hex.";\"></td><td>".($ex -> hexToRgb($hex))."</td><td>$count</td></tr>";
            }
            ?>
        </table>
        <br />
        <?php
        }
        ?>
    </div>
</body>
</html>
