<?php

//$memo_id = intval($_GET['memo_id']);

if (isset($upload_file)) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        
        $allowed_extensions = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx');
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            // echo "Invalid file type.";
            // exit();
            $_SESSION['error'] = "Invalid file type. Allowed types: " . implode(", ", $allowed_extensions);
        } else if ($file_size > 10 * 1024 * 1024) { // 10MB limit
            // echo "File size exceeds limit.";
            // exit();
            $_SESSION['error'] = "File size exceeds limit of 10MB.";
        } else {

            $new_filename = time() . '_' . basename($file_name);
            $upload_path = '../uploads/memos/' . $new_filename;
    
            if (!is_dir('../uploads/memos/')) {
                mkdir('../uploads/memos/', 0777, true);
            }
    
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $sql = "INSERT INTO memo_attachments (memo_id, filename, file_path, uploaded_at) 
                        VALUES ('$memo_id', '$file_name', '$new_filename', NOW())";
    
                if (mysqli_query($conn, $sql)) {
                    header('Location: memo.php?id='.$memo_id.'&msg=FileUploaded');
                    exit();
                } else {
                    echo "Database error: " . mysqli_error($conn);
                }
            } else {
                // echo "Upload failed.";
                $_SESSION['error'] = "Upload failed.";
            }
        }

    } else {
        // echo "No file selected.";
        $_SESSION['error'] = "No file selected.";
    }
}
?>