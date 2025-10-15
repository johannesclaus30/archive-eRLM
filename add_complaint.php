<script src="../Admin/js/jQuery.js"></script>

<style>
    img {
        height: 150px;
    }
</style>

<?php
session_start();
include("connections.php");

// Handle file uploads
$target_dir = "complaint_photos/";
$uploadErr = "";



$Complaint_Location_ID = $Complaint_Category_Name = $Complaint_SubCategory_Name = $Complaint_Description = $Complaint_TrackingNumber = $Complaint_Status = $Complaint_Region_Name = $Complaint_Province_Name = $Complaint_City_Name = $Complaint_Barangay_Name = $Complaint_Street = $Complaint_Landmark = $Complaint_ZIP = "";
$Complaint_CategoryErr = $Complaint_SubCategoryErr = $Complaint_DescriptionErr = $Complaint_RegionErr = $Complaint_ProvinceErr = $Complaint_CityErr = $Complaint_BarangayErr = $Complaint_StreetErr = "";
$success_message = $error_message = "";

$Complaint_ID = $File_Path = $File_Type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capture and sanitize inputs
    if(isset($_SESSION["User_ID"])) {
        $User_ID = $_SESSION["User_ID"];
    } else {
        // For guest users, you might want to handle differently or force login
        $User_ID = 0; // or redirect to login
    }

    $Complaint_Category_Name = $_POST["Complaint_Category_Name"] ?? '';
    $Complaint_SubCategory_Name = $_POST["Complaint_SubCategory_Name"] ?? '';
    $Complaint_Description = $_POST["Complaint_Description"] ?? '';
    $Complaint_Region_Name = $_POST["Complaint_Region_Name"] ?? '';
    $Complaint_Province_Name = $_POST["Complaint_Province_Name"] ?? '';
    $Complaint_City_Name = $_POST["Complaint_City_Name"] ?? '';
    $Complaint_Barangay_Name = $_POST["Complaint_Barangay_Name"] ?? '';
    $Complaint_Street = $_POST["Complaint_Street"] ?? '';
    $Complaint_Landmark = $_POST["Complaint_Landmark"] ?? '';
    $Complaint_ZIP = $_POST["Complaint_ZIP"] ?? '';

    // Validate required fields
    if (empty($Complaint_Category_Name)) {
        $Complaint_CategoryErr = "Category is required!";
    }
    if (empty($Complaint_SubCategory_Name)) {
        $Complaint_SubCategoryErr = "Subcategory is required!";
    }
    if (empty($Complaint_Description)) {
        $Complaint_DescriptionErr = "Description is required!";
    }
    if (empty($Complaint_Region_Name)) {
        $Complaint_RegionErr = "Region is required!";
    }
    if (empty($Complaint_Province_Name)) {
        $Complaint_ProvinceErr = "Province is required!";
    }
    if (empty($Complaint_City_Name)) {
        $Complaint_CityErr = "City/Municipality is required!";
    }
    if (empty($Complaint_Barangay_Name)) {
        $Complaint_BarangayErr = "Barangay is required!";
    }
    if (empty($Complaint_Street)) {
        $Complaint_StreetErr = "Street/Road is required!";
    }

    // Proceed if no validation errors
    if ($Complaint_Category_Name && $Complaint_SubCategory_Name && $Complaint_Description ) {

    $stmt = mysqli_prepare($connections, "INSERT INTO complaint_location (Complaint_Region, Complaint_Province, Complaint_City, Complaint_Barangay, Complaint_Street, Complaint_Landmark, Complaint_ZIP) VALUES (?, ?, ?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "sssssss", $Complaint_Region_Name, $Complaint_Province_Name, $Complaint_City_Name, $Complaint_Barangay_Name, $Complaint_Street, $Complaint_Landmark, $Complaint_ZIP);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if($Complaint_Category_Name == "Others") {
        $Complaint_SubCategory_Name = $_POST["Complaint_OtherSubcategory"] ?? '';
    } else {
        $Complaint_SubCategory_Name = $_POST["Complaint_SubCategory_Name"] ?? '';
    }

    //Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($connections, "INSERT INTO complaint (User_ID, Complaint_Location_ID, Complaint_Category, Complaint_SubCategory, Complaint_Description, Complaint_TrackingNumber, Complaint_Status, Created_At) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Get the last inserted User_ID
    $Complaint_Location_ID = mysqli_insert_id($connections);
    date_default_timezone_set ("Asia/Manila");
    $current_time = date('Y-m-d H:i:s');
    $Complaint_TrackingNumber = "ERK-" . strtoupper(bin2hex(random_bytes(5))); // Generate tracking number with ERK- prefix
    $Complaint_Status = "pending"; // Default status

    mysqli_stmt_bind_param($stmt, "iissssss", $User_ID, $Complaint_Location_ID, $Complaint_Category_Name, $Complaint_SubCategory_Name, $Complaint_Description, $Complaint_TrackingNumber, $Complaint_Status, $current_time);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Get the last inserted Complaint_ID
    $Complaint_ID = mysqli_insert_id($connections);

    // Handle file uploads
    $target_file = $target_dir . "/" . basename($_FILES["complaint_photo"]["name"]);
    $uploadOk = 1;

    if (file_exists($target_file)) {
        $target_file = $target_dir . rand(1,9) . rand(1,9) . rand(1,9) . rand(1,9) . "_" . basename($_FILES["complaint_photo"]["name"]);
        $uploadOk = 1;
    }
    $image_FileType = pathinfo($target_file, PATHINFO_EXTENSION);

    if($_FILES["complaint_photo"]["size"] > 10000000) {
        $uploadErr = "Sorry, your file is too large. Maximum size is 10MB.";
        $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["complaint_photo"]["tmp_name"], $target_file)) {

            mysqli_query($connections, "INSERT INTO complaint_media (Complaint_ID, File_Path, File_Type) VALUES ('$Complaint_ID', '$target_file', '$imageFileType')");
            $notify = "<font color='green'>Your complaint photo has been uploaded! </font>";
            echo "<script>window.location.href='MyAccount?notify=$notify';</script>";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }


    echo "<script language='javascript'>alert('New Complaint has been inserted!')</script>";
    echo "<script>window.location.href='index';</script>";


    }

}

?>


<style>
    .error{
        color:red;
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- JQuery for Address Selector -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <title>Submit Complaint - eReklamo</title>
    <link rel="stylesheet" href="add_complaint_design.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img class="ereklamo-logo" src="logos/eReklamo_White.png" />
                </div>
                <div class="header-right">
                    <span class="user-status" id="userStatus">Guest User</span>
                    <a href="index" class="btn btn-outline">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Progress Indicator -->
    <div class="progress-container">
        <div class="container">
            <div class="progress-steps">
                <div class="progress-step active">
                    <div class="step-circle">1</div>
                    <span class="step-label">Complaint Details</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <div class="step-circle">2</div>
                    <span class="step-label">Upload Evidence</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <div class="step-circle">3</div>
                    <span class="step-label">Review & Submit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Section -->
    <main class="main-content">
        <div class="container">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2 class="form-title">Submit a Complaint</h2>
                    <p class="form-description">Fill out the form below to report an issue in your community. All fields marked with * are required.</p>
                </div>

                <form id="complaintForm" class="complaint-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                    <!-- Category Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            Category Information
                        </h3>

                        <!-- Hidden input fields for text values -->
                            <input type="hidden" name="Complaint_Category_Name" id="Complaint_Category_Name">
                            <input type="hidden" name="Complaint_SubCategory_Name" id="Complaint_SubCategory_Name">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category">Category *</label>
                                <select id="category" name="Complaint_Category" value="<?php echo $Complaint_Category; ?>" required>
                                    <option value="">Select a category</option>
                                    <option value="infrastructure">Infrastructure</option>
                                    <option value="environment">Environment</option>
                                    <option value="peace_and_order">Peace and Order</option>
                                    <option value="health_and_sanitation">Health and Sanitation</option>
                                    <option value="public_safety">Public Safety</option>
                                    <option value="traffic_and_transportation">Traffic and Transportation</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="subcategory">Subcategory *</label>
                                <select id="subcategory" name="Complaint_SubCategory" value="<?php echo $Complaint_SubCategory; ?>" required disabled>
                                    <option value="">Select a subcategory</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="otherCategoryGroup" style="display: none;">
                            <label for="otherCategory">Please specify your complaint *</label>
                            <input 
                                type="text" 
                                id="otherCategory" 
                                name="Complaint_OtherSubcategory"
                                placeholder="Please describe your concern in brief"
                            >
                            <p class="field-hint">This field is required when "Others" category is selected</p>
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea 
                                id="description" 
                                name="Complaint_Description" 
                                rows="5" 
                                placeholder="Describe your complaint in detail. Include as much information as possible to help us understand and resolve the issue."
                                value="<?php echo $Complaint_Description; ?>"
                                required
                            ></textarea>
                            <div class="char-counter">
                                <span id="charCount">0</span> characters
                            </div>
                        </div>

                        <!-- Dynamic Location Dropdowns - Implement cascading logic with Fetch API -->
                        <!-- Hidden input fields for text values -->
                            <input type="hidden" name="Complaint_Region_Name" id="Complaint_Region_Name">
                            <input type="hidden" name="Complaint_Province_Name" id="Complaint_Province_Name">
                            <input type="hidden" name="Complaint_City_Name" id="Complaint_City_Name">
                            <input type="hidden" name="Complaint_Barangay_Name" id="Complaint_Barangay_Name">

                        <div class="form-group">
                            <label for="region">Region *</label>
                            <select id="region" name="Complaint_Region" value="<?php echo $Complaint_Region; ?>" required></select>
                            <p class="field-hint">Select the region</p>
                        </div>

                        <div class="form-group">
                            <label for="province">Province *</label>
                            <select id="province" name="Complaint_Province" value="<?php echo $Complaint_Province; ?>" required></select>
                            <p class="field-hint">Select the province</p>
                        </div>

                        <div class="form-group">
                            <label for="city">City/Municipality *</label>
                            <select id="city" name="Complaint_City" value="<?php echo $Complaint_City; ?>" required></select>
                            <p class="field-hint">Select the city or municipality</p>
                        </div>

                        <div class="form-group">
                            <label for="barangay">Barangay *</label>
                            <select id="barangay" name="Complaint_Barangay" value="<?php echo $Complaint_Barangay; ?>" required></select>
                            <p class="field-hint">Select the barangay</p>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="street">Street/Road *</label>
                                <input 
                                    type="text" 
                                    id="street" 
                                    name="Complaint_Street" 
                                    placeholder="e.g., Main Street"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="landmark">Landmark</label>
                                <input 
                                    type="text" 
                                    id="landmark" 
                                    name="Complaint_Landmark" 
                                    placeholder="e.g., Near Brgy. Hall"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="zipCode">ZIP Code</label>
                            <input 
                                type="text" 
                                id="zipCode" 
                                name="Complaint_ZIP" 
                                placeholder="e.g., 1000"
                                maxlength="4"
                            >
                            <p class="field-hint">Postal code (optional)</p>
                        </div>
                    </div>

                    <!-- Script for Address Selector -->
                    <script src="ph-address-selector.js"></script>

                    <!-- Upload Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Evidence & Attachments
                        </h3>

                        <div class="form-group">
                            <div class="upload-header">
                                <label>
                                    Photos 
                                    <span id="photoCount" class="upload-count">0/5</span>
                                </label>
                                
                            </div>
                            <div class="upload-area" id="photoUpload">
                                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                                <p class="upload-text">Click to upload photos or drag and drop</p>
                                <p class="upload-hint">PNG, JPG up to 10MB each</p>
                                <input type="file" name="File_Path" value="<?php echo $target_file; ?>" id="photoInput" accept="image/*" multiple hidden>
                            </div>
                            <div id="photoPreview" class="preview-grid"></div>
                            <p class="upload-info" id="photoInfo" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="info-icon">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                Click the × button on each photo to remove it
                            </p>
                        </div>

                        <div class="form-group">
                            <div class="upload-header">
                                <label>Video (Maximum 1)</label>
                                
                            </div>
                            <div class="upload-area" id="videoUpload">
                                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                </svg>
                                <p class="upload-text">Click to upload video or drag and drop</p>
                                <p class="upload-hint">MP4, MOV up to 50MB</p>
                                <input type="file" name="File_Path" id="videoInput" accept="video/*" hidden>
                            </div>
                            <div id="videoPreview"></div>
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            Notification Preferences
                        </h3>

                        <div class="notification-card" id="notificationSection">
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="emailUpdates" name="emailUpdates" disabled>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">
                                        <strong>Email Notifications</strong>
                                        <small>Receive updates via email about your complaint status</small>
                                    </span>
                                </label>

                                <label class="checkbox-label">
                                    <input type="checkbox" id="smsUpdates" name="smsUpdates" disabled>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">
                                        <strong>SMS Notifications</strong>
                                        <small>Receive text message updates on your phone</small>
                                    </span>
                                </label>
                            </div>

                            <div class="notification-notice">
                                <svg class="notice-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <p>
                                    <a href="sign_in" class="link">Sign in</a> to your account to enable email and SMS notifications
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="window.location.href='index'">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-large" id="submitBtn">
                            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            Submit Complaint
                        </button>
                        
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="add_complaint.js"></script>

    <script>
    function setText(nameSel, hiddenId){
        const opt = document.querySelector(nameSel + " option:checked");
        document.getElementById(hiddenId).value = opt ? opt.text : "";
    }
    ["#category", "#subcategory", "#region", "#province", "#city", "#barangay"].forEach((sel, i) => {
        const ids = ["Complaint_Category_Name","Complaint_SubCategory_Name","Complaint_Region_Name","Complaint_Province_Name","Complaint_City_Name","Complaint_Barangay_Name"];
        document.querySelector(sel)?.addEventListener("change", () => setText(sel, ids[i]));
        // initialize on load too
        setText(sel, ids[i]);
    });
    </script>

</body>
</html>
