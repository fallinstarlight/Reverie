<?php
include '../config/connection.php';
include '../models/employee.php';
header('Content-Type: application/json');
/* 
    Employee controller, contains functions to handle requests related to employees, 
    such as getting employee information, adding new employees, 
    and updating existing employees 
*/

/* GET FUNCTIONS */
/* Get all employees (admin only) */
function getEmployee($conn)
{
    $query = 'SELECT * FROM vwEmployee';
    $rows = $conn->query($query);
    $employee = getResult($rows);
    getResponse($employee);
    $conn->close();
}

/* Get employee by ID, used for profile viewing and editing */
function getEmployeebyID($conn, $id)
{
    $query = $conn->prepare("SELECT * FROM vwEmployee WHERE ID = ?");
    if (!$query) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Error with statement"]);
    } else {
        $query->bind_param("i", $id);
        $query->execute();
        $rows = $query->get_result();
        $employee = getResult($rows);
        getResponse($employee);
    }
    $query->close();
    $conn->close();
}

/* 
    Function to get result set and create employee objects 
    Helps to keep the get functions cleaner and more focused on handling the request and response
*/
function getResult($rows): array
{
    $employees = [];
    $error = [];
    if ($rows->num_rows > 0) {
        while ($row = $rows->fetch_assoc()) {
            try {
                $e = new employee($row);
                array_push($employees, $e);
            } catch (InvalidArgumentException $e) {
                array_push($error, $e->getMessage());
                return $error;
            }
        }
    }
    return $employees;
}

/* 
    Function to send the response according to the data retrieved 
    Also added for code organization 
*/
function getResponse($employee){
    if (empty($employee)) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "No matching records found"]);
    } else {
        echo json_encode($employee);
    }
}


/* POST FUNCTIONS */
/* Function to validate input parameters for adding a new employee */
function validateParams($input): bool{
    $errors = [];
    if(!$input['Name'] || !is_string($input['Name']) || strlen($input['Name']) > 50){
        array_push($errors, "Invalid name");
    }
    if(!$input['Surname'] || !is_string($input['Surname']) || strlen($input['Surname']) > 50){
        array_push($errors, "Invalid surname");
    }
    if(!$input['Username'] || !is_string($input['Username']) || strlen($input['Username']) > 20){
        array_push($errors, "Invalid username");
    }
    if(!$input['Password']){
        array_push($errors, "Password must be provided");
    }
    if(!$input['Shift'] || !is_string($input['Shift']) || strlen($input['Shift']) > 30){
        array_push($errors, "Invalid shift");
    }
    if(!$input['Phone'] || !is_string($input['Phone']) || strlen($input['Phone']) > 14 || strlen($input['Phone']) < 10 || !preg_match('/^\+?\d+$/', $input['Phone'])){
        array_push($errors, "Invalid number format, must be 10 to 14 digits and can have a +");
    }

    /* Return errorrs or success depending on validation */
    if(!empty($errors)){
        http_response_code(400);
        echo json_encode(["status"=>"error", "errors"=>$errors]);
        exit;
    }
    return 1;
}

/* 
    Function to insert a new employee into the database 
    Takes two parameters: the database connection and the input data 
    $conn comes from the connection file and $input comes from the body of the POST request, 
    which is expected to be a JSON object with the employee information
*/
function inEmployee($conn, $input){
    /* First validate the input parameters to ensure they meet the required criteria */
    if(validateParams($input)){
        /* Prepare the SQL statement to insert a new employee */
        $query = $conn->prepare("CALL inEmployee(?, ?, ?, ?, ?, ?, ?)");
        /* Hash the password before storing it in the database for security reasons */
        $passwordHash = password_hash($input['Password'], PASSWORD_DEFAULT);
        /* Bind the input parameters to the SQL statement, using the hashed password */
        $query -> bind_param("sssssss", $input['Name'], $input['Surname'], $input['Username'], $passwordHash, $input['Shift'], $input['Phone'], $input['Photo']);
        /* Try to execute the statement and return a success response, or catch any exceptions and return an error response */
        try{
            $query -> execute();
            http_response_code(200);
            echo json_encode(["Status"=>"success", 
                            "Message"=>"Employee added correctly",
                            "Name"=>"{$input['Name']}.{$input['Surname']}",
                            "Username"=>"{$input['Username']}",
                            "Shift"=>"{$input['Shift']}"]);
        }catch(Exception $e){
            http_response_code(500);
            echo json_encode(["status"=>"error", "message"=>"Error performing request: {$e -> getMessage()}"]);
        }finally{
           $query->close();
           $conn->close();
       }
    }
}

/* PUT FUNCTIONS */

/* 
    Validate input parameters for updating an employee 
    We don't validate the password here as we don't have any sets of rules for passwords
    Validation (like lenght, use of special characters, uppercase, numbers, etc.) could be implemented in the frontend
*/
function validateParamsUpdateOnly($input): bool{
    $errors = [];
    if(isset($input['Name']) && (!is_string($input['Name']) || strlen($input['Name']) > 50)){
        array_push($errors, "Invalid name");
    }
    if(isset($input['Surname']) && (!is_string($input['Surname']) || strlen($input['Surname']) > 50)){
        array_push($errors, "Invalid surname");
    }
    if(isset($input['Username']) && (!is_string($input['Username']) || strlen($input['Username']) > 20)){
        array_push($errors, "Invalid username");
    }
    if(isset($input['Shift']) && (!is_string($input['Shift']) || strlen($input['Shift']) > 30)){
        array_push($errors, "Invalid shift");
    }
    if(isset($input['Phone']) && (!is_string($input['Phone']) || strlen($input['Phone']) > 14 || strlen($input['Phone']) < 10 || !preg_match('/^\+?\d+$/', $input['Phone']))){
        array_push($errors, "Invalid number format, must be 10 to 14 digits and can have a +");
    }

    if(!empty($errors)){
        http_response_code(400);
        echo json_encode(["status"=>"error", "errors"=>$errors]);
        exit;
    }
    return 1;
}

/* 
    Function to update an existing employee in the database 
    Takes three parameters: the database connection, the input data, and the employee ID 
    $conn comes from the connection file, $input comes from the body of the PUT request, 
    which is expected to be a JSON object with the employee information to be updated, and $id is the ID of the employee to be updated 
*/
function updateEmployee($conn, $input, $id){
    $newValues = [];
    /* 
        Check which parameters are being updated and validate them, then prepare the new values for the update query 
        Note that we allow values to be null because we only want to update the fields that are provided 
        But if all parameters are null, we return an error because there is nothing to update
    */
    $expectedParams = ['Name', 'Surname', 'Username', 'Password', 'Shift', 'Phone', 'Photo'];
    foreach($expectedParams as $param){
        if(array_key_exists($param, $input)){
            array_push($newValues, $param);
        }
    }
    if(empty($newValues)){
        http_response_code(400);
        echo json_encode(["status"=>"error", "error"=>"no valid new parameters to send"]);
        exit();
    }
    /* Validate the provided parameters and return an error if any of them are invalid, otherwise proceed with the update */
    if(validateParamsUpdateOnly($input)){
        $new_name = $input['Name'] ?? null;
        $new_surname = $input['Surname'] ?? null;
        $new_username = $input['Username'] ?? null;
        /* Hash the new password if it is provided, otherwise set it to null to indicate that it should not be updated */
        $new_password = (isset($input['Password'])) ? password_hash($input['Password'], PASSWORD_DEFAULT) :  null;
        $new_shift = $input['Shift'] ?? null;
        $new_phone = $input['Phone'] ?? null;
        $new_photo = $input['Photo'] ?? null;
        /* Prepare the SQL statement to update the employee, using a stored procedure that handles the update logic and validations, plus extra database logic for information integrity */
        $query = $conn -> prepare('CALL updateEmployee(?, ?, ?, ?, ?, ?, ?, ?)');
        $query -> bind_param("isssssss", $id, $new_name, $new_surname, $new_username, $new_password, $new_shift, $new_phone, $new_photo);
        /* Try to execute the update and return a success response, or catch any exceptions and return an error response */
        try{
            $query->execute();
            http_response_code(200);
            echo json_encode(["status"=>"success", "message"=>"employee updated correctly"]);
        }catch(Exception $e){
            http_response_code(500);
            echo json_encode(["status"=>"error", "message"=>"{$e -> getMessage()}"]);
        }finally{
           $query->close();
           $conn->close();
       }
    }
}

/* 
============================================================================================================
============================================================================================================
Code made by Francisco Emmanuel Luna Hidalgo Last checked 25/04/2026 
============================================================================================================
============================================================================================================
Instituto Tecnológico de Pachuca, Ingeniería en Sistemas Computacionales, Programación Web, proyecto final
============================================================================================================
============================================================================================================
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%%%%%%%%%##%%%%%%%%%%@@@@@@@@@@@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%%#*++++++++++++++++++++++++++++*#%%%%%%@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#*+++++++++++++++++++++++++++++++++++++++++++*##%%%@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%+++++++++++++++++++++++++++++++++++++++++++++++++++++*#%%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%@@@@@#+++++++++++++++++++++++++++++++++++++++++++++++++++++++%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@%%#+#%@@@@%*++++##+++++++++++++++++++++++++++++++++++++++++++++++%%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@%%*+++++%%@@@@%*+++%@@@%#*+++++++++++++++++++++++++++++++++++++++++#%@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@%#++++++++*%@@@@@%*++%@@@@@@@%#+++++++++++++++++++++++++++++++++++++*%@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@%#++++++++++=#%@@@@@@#+%@@@@@@@@@@%#++++++++++++++++++++++++++++++++++%@@@@@@@@@@
    @@@@@@@@@@@@@@@@@%#++++++++++++++%@@@@@@@%%@@@@@@@@@@@@%%*++++++++++++++++++++++++++++++#%@@@@@@@@@@
    @@@@@@@@@@@@@@@%#++++++++++++++++*%@@@@@@@@@@@@@@@@@@@@@@@%#*++++++++++++++++++++++++++*%@@@@@@@@@@@
    @@@@@@@@@@@@@%%*++++++++++++++++++#%@@@@@@@@@@@@@@@@@@@@@@@@@%#+++++++++++++++++++++++*%@@@@@@@@@@@@
    @@@@@@@@@@@@%#+++++++++++++++++++++%%@@@@@@@@@@@@@@@@@@@@@@@@@@%%*++++++++++++++++++++#%@@@@@@@@@@@@
    @@@@@@@@@@@%*+++++++++++++++++++++++%@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%#+++++++++++++++++#%@@@@@@@@@@@@@
    @@@@@@@@@@%+++++++++++++++++++++++++*%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#++++++++++++++*%@@@@@@@@@@@@@@
    @@@@@@@@%#+++++++++++++++++++++++++++#%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#++++++++++++%@@@@@@@@@@@@@@@
    @@@@@@@%%+++++++++++++++++++++++++++++%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#*++++++++#%@@@@@@@@@@@@@@@
    @@@@@@%%++++++++++++++++++++++++++++++*%@@@@@@@@@@@@@@%%%%%%%%%%%%%%%%@@@@%%##+--*%%@@@@@@@@@@@@@@@@
    @@@@@@%+++++++++++++++++++++++++++++++#%++*#%@@@@%%##*++++++++++++++++*#%%%%=...-=.=%@@@@@@@@@@@@@@@
    @@@@@%*+++++++++++++++++++++++++++++**:-+...-#%#*+++++++++++++++++++++++++##...:*...#@@@@@@@@@@@@@@@
    @@@@%*++++++++++++++++++++++++++++++#-..:+...=%+++++++++++++++++++++++++++*%:..*...:%@@@@@@@@@@@@@@@
    @@@%#+++++++++++++++++++++++++++++++#=...-=..+#++++++++++++++++++++++++++++#%++-..+%@@@@@@@@@@@@@@@@
    @@@%+++++++++++++++++++++++++++++**#%%+:..-**#+++++++++++++++++++++++++++++++*####**#%@@@@@@@@@@@@@@
    @@%#+++++++++++++++++++++++++*#%%@@@%#*#%#%#++++++++++++++++++++++++++++++++++++++++++#%@@@@@@@@@@@@
    @@%++++++++++++++++++++++*#%%@@@@@@%++++++++++++++++++++++++++++++++++++++=+===========*%@@@@@@@@@@@
    @%#+++++++++++++++++++*%%@@@@@@@@%+-=++++++++++++++++++++++++++++++++++++++=:...........:#@@@@@@@@@@
    @%*+++++++++++++++*#%@@@@@@@@@@@%+....-=++++++++++++++++++++=--==++++++++++++=-..........:*%@@@@@@@@
    @%++++++++++++++#%@@@@@@@@@@@@@%+........:=+++++++++++++++++++=.....:-==++++++++=..........#%@@@@@@@
    %#+++++++++++*%@@@@@@@@@@@@@@@%*.............:-===++++++++++++++-.................:-++=:....%@@@@@@@
    %#+++++++++#@@@@@@@@@@@@@@@@@@#:............:-::...::--===+++++++=-....................-*:..-%@@@@@@
    %#+++++=*%@@@@@@@@@@@@@@@@@@@%=..  ......:*=....................................+%@@%+...-:..+@@@@@@
    %#++++++++****#%@@@@@@@@@@@@@#:.     ....+.....:=*#*=:....  .... .....      ..+@@@#.:#@-.....-%@@@@@
    %*+++++++++*#%@@@@@@@@@@@@@@%+.. .   ...::...=@@@@=:-+%*:.                  .*@@@@@+..*@:....:#@@@@@
    %*=+++*##%%@@@@@@@@@@@@@@@@@%=..      ......#@@@@@#....-%+...   .        ...+@@@@@@%..:@#.....*%@@@@
    %%%%%@@@@@@@@@@@@@@@@@@@@@@@%=..      .....#@@@@@@@:.....#*..            ..-@@@@@*:*...*%.....+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@%=..         .-@@@@@@%*=.....:#*.           ...%@#=.:=#*...=@:. ..+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@%=...        .*@@@#-.:*=......:@+...         .++.:*@@@@-...-@:. ..+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@%+...  .     .#%:.:#@@@=...  ..+@:..         .#@@@@@@@%....=@:....+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@*..         :#+#@@@@@@:...  ...%*..        .-%@@@@@@@=.  .+%.....*@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@#-..        :#@@@@@@@#....  ...=#:.       ..=@@@@@@@#.. ..*+....:#@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@+.         .#@@@@@@@=.     ....%-.      ...+@@@@@@%..  ..%:....-%@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%:.......  .*@@@@@@#:.     ....*=.      ...*@@@@@%......-*.....*@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@#.......  .+@@@@@@:..      . .==. .     ..*@@@@+... ...+:....-%@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#......  .:@@@@@....   .    .-=.     . ..#@@+........:=.....%%@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:..... ..#@%+.....       ..:=.       ..=:..:::::::-=:....==--#%@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@#:.......-+::---===++==+++++-..........:--:::....... ......:*%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%=............................ ...................   ....-%@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:......     ..-*+-:....................     .   ....:#%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:.......  ...:+-:=+*#%%%###***++++..............:+%@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:............=#-............:*-.............:*%@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%=............=#*:......:+#-.............-#%@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#=:...........=+****+-............:=#%@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#+-:......................-+#%@@@@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%%%#*+=-::::::-=+#%%%%@@@@@@@@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%+**##%%%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
============================================================================================================
============================================================================================================
*/
?>