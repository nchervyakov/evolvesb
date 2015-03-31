<?php
#include config file
require_once 'config.php';

$action = (isset($_GET['action']))? $_GET['action']:'';

switch($action)
{
	default:
		echo json_encode(array('message'=>'Invalid parameter.'));	
	break;
	case 'upload':
		#here we can make folders...etc
		$targetFolder = __DIR__.'/../../../../upload/media_files/';
		$verifyToken = md5('unique_salt' . $_POST['timestamp']);
		if ($_SERVER['REQUEST_METHOD']=='POST' && !empty($_FILES) && $_POST['token'] == $verifyToken) 
		{
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $targetFolder;
			$targetFile = $targetFolder . $_FILES['Filedata']['name'];
			
			// Validate the file type
			$imageTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			
			if(in_array($fileParts['extension'], $imageTypes)) {
                $type = 'image';
            } else {
                $type = 'file';
            }
            $res = move_uploaded_file($tempFile,$targetFile);
            $targetFile = str_replace('\\', '/', realpath($targetFile));
            $targetFile = str_replace(str_replace('\\', '/', realpath(__DIR__.'/../../../../upload') . '/'), '', $targetFile);

            $data = array();
            if($res)
            {
                //make somehting with these files
                $data = array('type'=>$type,
                              'path'=>$targetFile,
                              'thumbnailPath'=>$targetFile,
                              'fileName'=>$_FILES['Filedata']['name']
                             );
                $q = "INSERT INTO `tbl_w3bdeveloper_media` (`type`, `path`, `thumbnailPath`, `fileName`) VALUES ('".$data['type']."', '".$data['path']."', '".$data['thumbnailPath']."', '".$data['fileName']."')";
                $result = $db->query($q);
                $insertId = $db->lastInsertId();
                $data['id'] = $insertId;
            }
            echo json_encode($data);exit;

		  }	
	break;
	case 'delete':
		$fileId = (isset($_POST['file_id']))? intval($_POST['file_id']) : 0;
		if($fileId)
		{
			$mediaItems = $db->query("SELECT * FROM `tbl_w3bdeveloper_media` WHERE `id`=".intval($fileId));
			$mediaItem = $mediaItems->fetch(PDO::FETCH_ASSOC);
			#delete from database
			$q = "DELETE FROM tbl_w3bdeveloper_media WHERE id = ".$fileId;
			$result = $db->query($q);
			if($result)
			{
				#delete from disk
				if(file_exists($mediaItem['path']))
				{
					unlink($mediaItem['path']);
				}
				echo json_encode(array('message'=>'File deleted','id'=>$mediaItem['id'],'path'=>$mediaItem['path']));
			}
			else
			{
				echo json_encode(array('message'=>'File cannot be deleted.'));	
			}
		}
		exit;
	break;
}


?>