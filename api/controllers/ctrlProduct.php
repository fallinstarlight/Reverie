<?php
include '../config/connection.php';
include '../models/product.php';
header('Content-Type: application/json');

/* 
    Product controller, contains functions to handle requests related to products, 
    such as getting product information, adding new products, updating existing products, and managing stock levels
*/

/* GET FUNCTIONS */
/* Get all products, used for product listing and browsing */
function getProduct($conn)
{
    $query = 'SELECT * FROM vwProduct';
    $rows = $conn->query($query);
    $product = getResult($rows);
    getResponse($product);
    $conn->close();
}

/* Get product by ID, used for product details viewing and editing */   
function getProductbyID($conn, $id)
{
    $query = $conn->prepare("SELECT * FROM vwProduct WHERE Code = ?");
    if (!$query) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Error with statement"]);
    } else {
        $query->bind_param("s", $id);
        $query->execute();
        $rows = $query->get_result();
        $product = getResult($rows);
        getResponse($product);
    }
    $query->close();
    $conn->close();
}

/* 
    Same set of functions as in Employee controller to keep code clean and maintainable 
*/
function getResult($rows): array
{
    $products = [];
    $error = [];
    if ($rows->num_rows > 0) {
        while ($row = $rows->fetch_assoc()) {
            try {
                $p = new product($row);
                array_push($products, $p);
            } catch (InvalidArgumentException $e) {
                array_push($error, $e->getMessage());
                return $error;
            }
        }
    }
    return $products;
}

function getResponse($product){
    if (empty($product)) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "No matching records found"]);
    } else {
        echo json_encode($product);
    }
}

/* POST FUNCTIONS */
/* Function to validate input parameters for adding a new product, checks for required fields and correct data types */
function validateParams($input): bool{
    $errors = [];
    if(!$input['Code'] || !is_string($input['Code']) || strlen($input['Code']) > 10){
        array_push($errors, "Invalid code");
    }
    if(!$input['Name'] || !is_string($input['Name']) || strlen($input['Name']) > 50){
        array_push($errors, "Invalid product name");
    }
    if(!$input['Description'] || !is_string($input['Description']) || strlen($input['Description']) > 255){
        array_push($errors, "Must provide description and must be < 255 characters");
    }
    if(!$input['Price'] || !is_numeric($input['Price']) || ($input['Price']) <= 0){
        array_push($errors, "Price must be set and more than 0");
    }
    if(!$input['Amount'] || !is_numeric($input['Amount']) || $input['Amount'] < 0){
        array_push($errors, "Must provide amount and cannot be less than 0");
    }
    if(!$input['Category'] || !is_int($input['Category']) || $input['Category'] <= 0){
        array_push($errors, "Not a valid category");
    }

    if(!empty($errors)){
        http_response_code(400);
        echo json_encode(["status"=>"error", "errors"=>$errors]);
        exit;
    }
    return 1;
}

/* 
    Function to insert a new product into the database 
    Takes two parameters: the database connection and the input data 
    $conn comes from the connection file and $input comes from the body of the POST request, 
    which is expected to be a JSON object with the product information
*/
function inProduct($conn, $input){
    if(validateParams($input)){
        /*
            Prepare the SQL statement to insert a new product,
            Instead of using a direct INSERT statement, we use a stored procedure 
            that handles the insertion logic and some extra validations 
        */
        $query = $conn->prepare("CALL inProduct(?, ?, ?, ?, ?, ?)");
        $query -> bind_param("sssdii", $input['Code'], $input['Name'], $input['Description'], $input['Price'], $input['Amount'], $input['Category']);
        try{
            $query -> execute();
            http_response_code(200);
            echo json_encode(["Status"=>"success", 
                            "Message"=>"Product added correctly",
                            "Name"=>"{$input['Name']}",
                            "Description"=>"{$input['Description']}",
                            "Price"=>"{$input['Price']}"]);
        }catch(Exception $e){
            http_response_code(500);
            echo json_encode(["status"=>"error", "message"=>"Error performing request: {$e -> getMessage()}"]);
        }
        $query -> close();
    }
}

/* PUT FUNCTIONS */
/* Function to validate input parameters for updating a product, checks for correct data types and value ranges */
function validateParamsUpdateOnly($input): bool{
    $errors = [];
    if(isset($input['Name']) && (!is_string($input['Name']) || strlen($input['Name']) > 50)){
        array_push($errors, "Invalid name");
    }
    if(isset($input['Description']) && (!is_string($input['Description']) || strlen($input['Description']) > 255)){
        array_push($errors, "Invalid description");
    }
    if(isset($input['Price']) && (!is_numeric($input['Price']) || $input['Price'] < 0)){ //validate that price is a number and is greater than 0
        array_push($errors, "Price must be a number and must be greater than 0");
    }
    if(isset($input['Category']) && (!is_int($input['Category']) || $input['Category'] <= 0)){
        array_push($errors, "The provided category must be a positive integer value");
    }
    if(isset($input['Photo']) && (!is_string($input['Photo']) || strlen($input['Photo']) > 255)){
        array_push($errors, "The path to the photo shall not excede 255 characters");
    }
    if(isset($input['Discontinued']) && !is_bool($input['Discontinued'])){ //validate that discontinued is a boolean value
        array_push($errors, "Discontinued must be a boolean value");
    }

    if(!empty($errors)){
        http_response_code(400);
        echo json_encode(["status"=>"error", "errors"=>$errors]);
        exit;
    }
    return 1;
}

/* 
    Function to update an existing product in the database 
    Takes three parameters: the database connection, the input data, and the product ID 
    $conn comes from the connection file, $input comes from the body of the PUT request, 
    which is expected to be a JSON object with the new product information, and $id is the code of the product to be updated
*/
function updateProduct($conn, $input, $id){
    $newValues = [];
    /* 
        Check which parameters are being updated and validate them, then prepare the new values for the update query 
        Note that we allow values to be null because we only want to update the fields that are provided 
        But if all parameters are null, we return an error because there is nothing to update
    */
    $expectedParams = ['Name', 'Description', 'Price', 'Category', 'Photo', 'Discontinued'];
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
        /* Initialize values to be updated, null if they are not provided */
        $new_name = $input['Name'] ?? null;
        $new_description = $input['Description'] ?? null;
        $new_price = $input['Price'] ?? null;
        $new_category = $input['Category'] ?? null;
        $new_photo = $input['Photo'] ?? null;
        $new_discontinued = $input['Discontinued'] ?? null; // This can be true, false, or null if not provided
        $new_state = ($new_discontinued === true) ? 'discontinued' : 'available'; // Set state based on discontinued value, if discontinued is true, state is 'discontinued', if false or null, state is 'available'
        /* Prepare the SQL statement to update the product, using a stored procedure that handles the update logic and validations, plus extra database logic for information integrity */
        $query = $conn -> prepare('CALL updateProduct(?, ?, ?, ?, ?, ?, ?)');
        /* Bind the parameters for the stored procedure */
        $query -> bind_param("ssssiss", $id, $new_name, $new_description, $new_price, $new_category, $new_photo, $new_state);
        /* Try to execute the update and return a success response, or catch any exceptions and return an error response */
        try{
            $query->execute();
            http_response_code(200);
            echo json_encode(["status"=>"success", "message"=>"product updated correctly"]);
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
    Function to decrease the stock of a product, used when a sale is made 
    Takes three parameters: the database connection, the amount to decrease, and the product ID 
    $conn comes from the connection file, $amount is the quantity to decrease, and $id is the code of the product to be updated
    This function is already used in the sales recording process by the database itself, but it can also be used for manual stock adjustments if needed
*/
function decProduct($conn, $amount, $id){
    /* Validate that the amount is a number and is greater than 0, otherwise return an error response */
    if(!is_numeric($amount) || $amount <= 0){
        http_response_code(400);
        echo json_encode(["status"=>"error", "message"=>"Amount must be a positive number"]);
        exit();
    }
    
    /* Try to execute the stock decrease and return a success response, or catch any exceptions and return an error response */
    try{
        /* Prepare the SQL statement to decrease the product stock, using a stored procedure that handles the logic and validations for stock management */
        $query = $conn -> prepare("CALL decProduct(?, ?)");
        if(!$query){
            http_response_code(400);
            echo json_encode(["status"=>"error", "message"=>"Error preparing request"]);
        }
        $query -> bind_param("si", $id, $amount);
        $query -> execute();
        http_response_code(200);
        echo json_encode(["status"=>"success", "message"=>"Product {$id} has {$amount} less stock"]);
    }catch(Exception $e){
        http_response_code(500);
        echo json_encode(["status"=>"error", "message"=>"{$e -> getMessage()}"]);
    }finally{
           $query->close();
           $conn->close();
    }
}

/* 
    Function to increase the stock of a product, used for restocking or correcting stock levels 
    Takes two parameters: the database connection and the product ID 
    $conn comes from the connection file and $id is the code of the product to be updated
    This function can be used for manual stock adjustments when new inventory is added or to correct stock levels if there were any discrepancies
*/
function incProduct($conn, $id){
    try{
        $query = $conn -> prepare("CALL incProduct(?)");
        if(!$query){
            http_response_code(400);
            echo json_encode(["status"=>"error", "message"=>"Error preparing request"]);
            exit();
        }
        $query -> bind_param("s", $id);
        $query -> execute();
        http_response_code(200);
        echo json_encode(["status"=>"success", "message"=>"Product {$id} has increased its amount by 1"]);
    }catch(Exception $e){
        http_response_code(500);
        echo json_encode(["status"=>"error", "message"=>"{$e -> getMessage()}"]);
    }finally{
           $query->close();
           $conn->close();
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