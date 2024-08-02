<div class="container">
    <h1>Update User Profile</h1>
    <form method="post" action="">
        <input type="hidden" name="user_id" value="<?php echo $userID; ?>">
        <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>"> <!-- Added hidden input for table name -->

        <label for="user_name"> User Name:</label>
        <input type="text" name="user_name" value="<?php echo $userDetails[$_GET['table'] . '_name']; ?>" required><br>

        <label for="user_dob"> User DOB:</label>
        <input type="date" name="user_dob" value="<?php echo $userDetails[$_GET['table'] . '_dob']; ?>" required><br>

        <label for="user_phone"> User Phone:</label>
        <input type="tel" name="user_phone" pattern="[0-9]{10}" oninput="validatePhoneNumber(this)" value="<?php echo $userDetails[$_GET['table'] . '_phone_number']; ?>" required><br>

        <label for="user_email"> User Email:</label>
        <input type="email" name="user_email" value="<?php echo $userDetails[$_GET['table'] . '_email']; ?>" required><br>

        <label for="user_pwd"> User Password:</label>
        <input type="password" name="user_pwd" id="user_pwd" value="<?php echo $userDetails[$_GET['table'] . '_password']; ?>" required>
        <i class="fas fa-eye toggle-password"></i>

        <label for="user_place">User Place:</label>
        <input type="text" name="user_place" value="<?php echo $userDetails[$_GET['table'] . '_place']; ?>" required><br>

        <label for="user_address">User Address:</label>
        <input type="text" name="user_address" value="<?php echo $userDetails[$_GET['table'] . '_address']; ?>" required><br>

        <label for="user_main_area">User Main Area:</label>
        <input type="text" name="user_main_area" value="<?php echo $userDetails[$_GET['table'] . '_main_area']; ?>" required><br>

        <label for="user_acc_status">User Account Status:</label>
        <input type="text" name="user_acc_status" value="<?php echo $userDetails[$_GET['table'] . '_acc_status']; ?>" required><br>

        <label for="user_mpin">User MPIN:</label>
        <input type="text" name="user_mpin" value="<?php echo $userDetails[$_GET['table'] . '_mpin']; ?>" required><br>

        <input type="submit" value="Update User Profile">
    </form>
</div>

the above container code is producing below mentioned and similar error for all fields:

<br /><b>Warning</b>: Undefined array key C:\xampp\htdocs\districtcare\admin\admin_userprof_update_sep.php on line
316" required>