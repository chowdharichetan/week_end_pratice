<?php
// Include necessary files
include('style.php');
include('connect.php');

// Get user ID from GET parameter
$user_id = $_GET['editid'] ?? null;

// Validate and sanitize the input
if ($user_id === null || !is_numeric($user_id)) {
    echo "Invalid user ID.";
    exit;
}

// Prepare and execute SQL query to fetch user details
$sql = "SELECT * FROM users INNER JOIN user_meta ON users.user_id = user_meta.user_id WHERE users.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $mobile = $row['phone_number'];
    $email = $row['email'];
    // Password and re_type_psw should not be pre-populated for security reasons
    $name = $row['firstname'];
    $middlename = $row['middlename'];
    $lastname = $row['lastname'];
    $course = $row['course'];
    $gender = $row['gender'];
    $vehicles = explode(",", $row['vehicle']);
    $country_code = $row['country_code'];
    $current_address = $row['current_address'];
} else {
    echo "User not found.";
    exit;
}

// Close the statement after fetching user details
$stmt->close();

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get form data
    $username = $_POST['username'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    // Ensure to properly validate and sanitize user inputs for security
    $name = $_POST['name'] ?? '';
    $middlename = $_POST['middlename'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $course = $_POST['course'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $vehicles = isset($_POST['vehicles']) ? implode(",", $_POST['vehicles']) : '';
    $country_code = $_POST['country_code'] ?? '';
    $current_address = $_POST['current_address'] ?? '';

    // Hash the password before updating (not shown here)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Assuming $password is set

    // Update user_meta table
    $sql_user_meta = "UPDATE `user_meta` SET ";
    $sql_user_meta .= "`firstname`=?, ";
    $sql_user_meta .= "`middlename`=?, ";
    $sql_user_meta .= "`lastname`=?, ";
    $sql_user_meta .= "`course`=?, ";
    $sql_user_meta .= "`gender`=?, ";
    $sql_user_meta .= "`vehicle`=?, ";
    $sql_user_meta .= "`country_code`=?, ";
    $sql_user_meta .= "`current_address`=? ";
    $sql_user_meta .= "WHERE `user_id`=?";

    // Prepare and bind parameters for user_meta table
    $stmt_user_meta = $con->prepare($sql_user_meta);
    $stmt_user_meta->bind_param("sssssssi", $name, $middlename, $lastname, $course, $gender, $vehicles, $country_code, $current_address, $user_id);

    // Update users table
    $sql_users = "UPDATE `users` SET ";
    $sql_users .= "`username`=?, ";
    $sql_users .= "`email`=?, ";
    $sql_users .= "`phone_number`=? ";
    $sql_users .= "WHERE `user_id`=?";

    // Prepare and bind parameters for users table
    $stmt_users = $con->prepare($sql_users);
    $stmt_users->bind_param("sssi", $username, $email, $mobile, $user_id);

    // Execute the queries
    $update_user_meta = $stmt_user_meta->execute();
    $update_users = $stmt_users->execute();

    // Check if updates were successful
    if ($update_user_meta && $update_users) {
        echo "Records updated successfully";
    } else {
        echo "Error updating user meta: " . $stmt_user_meta->error . "<br>";
        echo "Error updating users: " . $stmt_users->error;
    }

    // Close the prepared statements
    $stmt_user_meta->close();
    $stmt_users->close();
}

// Close the database connection
$con->close();
?>


<form action="" method="POST">
    <div class="container">
        <center>
            <h1>Student Registration Form</h1>
        </center>
        <label>Firstname:</label>
        <input type="text" name="name" value="<?php echo $name; ?>" size="15"/>

        <label> Middlename: </label>
        <input type="text" name="middlename" placeholder="Middlename"  value="<?php echo $middlename; ?>" size="15"/>

        <label> Lastname: </label>
        <input type="text" name="lastname" value="<?php echo $lastname; ?>" size="15"/>   

        <label> Course : </label>
        <select name="course">
            <option value="">Course</option>
            <option value="BCA"<?php if ($course === "BCA") echo " selected"; ?>>BCA</option>
            <option value="BBA"<?php if ($course === "BBA") echo " selected"; ?>>BBA</option>
            <option value="B.Tech"<?php if ($course === "B.Tech") echo " selected"; ?>>B.Tech</option>
            <option value="MBA"<?php if ($course === "MBA") echo " selected"; ?>>MBA</option>
            <option value="MCA"<?php if ($course === "MCA") echo " selected"; ?>>MCA</option>
            <option value="M.Tech"<?php if ($course === "M.Tech") echo " selected"; ?>>M.Tech</option>
        </select>
       <div>
            <label> Gender : </label><br>
            <input type="radio" value="Male" name="gender"<?php if ($gender === "Male") echo " checked"; ?>> Male
            <input type="radio" value="Female" name="gender"<?php if ($gender === "Female") echo " checked"; ?>> Female
            <input type="radio" value="Other" name="gender"<?php if ($gender === "Other") echo " checked"; ?>> Other
        </div>

        <div>
            <label> Vehicle: </label><br>
            <input type="checkbox" id="vehicle1" name="vehicle[]" value="Bike"<?php if (in_array("Bike", $vehicles)) echo " checked"; ?>>
            <label for="vehicle1"> I have a bike</label><br>
            <input type="checkbox" id="vehicle2" name="vehicle[]" value="Car"<?php if (in_array("Car", $vehicles)) echo " checked"; ?>>
            <label for="vehicle2"> I have a car</label><br>
            <input type="checkbox" id="vehicle3" name="vehicle[]" value="Boat"<?php if (in_array("Boat", $vehicles)) echo " checked"; ?>>
            <label for="vehicle3"> I have a boat</label><br>
        </div>


        <label> Country Code : </label>
        <input type="text" name="country_code" placeholder="Country Code" value="+91" size="2" />

         <label> Current Address :</label>
         <textarea cols="80" rows="5" name="current_address"  value="<?php echo  $current_address; ?>" >
         </textarea>

        <!-- Add your other input fields here -->

        <label>Phone:</label>
        <input type="text" name="mobile" value="<?php echo $mobile; ?>" size="10"/>

        <label for="email"><b>Email</b></label>
        <input type="text" value="<?php echo $email; ?>" name="email" disabled>
        <span class="error"><?php echo isset($email_err) ? $email_err : ''; ?></span>

        <label> Username: </label>
         <input type="text" name="username" value="<?php echo $username; ?>" size="15"  />

       
    <label for="password"><b>Password</b></label>
    <input type="password" value="<?php echo  $password; ?>" name="password" id="passwordInput">
    <span class="error"><?php echo isset($password_err) ? $password_err : ''; ?></span>
    <input type="checkbox" onclick="togglePasswordVisibility('passwordInput')"> Show Password

    <br><br>

    <label for="psw-repeat"><b>Re-type Password</b></label>
    <input type="password" value="<?php echo  $re_type_psw; ?>" name="psw-repeat" id="retypePasswordInput">
    <input type="checkbox" onclick="togglePasswordVisibility('retypePasswordInput')"> Show Password

        <!-- Removed unnecessary commented out fields -->

       <button type="submit" name="submit" value="Update">Update</button>
    </div>
</form>



 <script>
        function togglePasswordVisibility(inputId) {
            var x = document.getElementById(inputId);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
