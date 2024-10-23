<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "form_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$submittedData = [];
$successMessage = ""; 
$result = $conn->query("SELECT * FROM submissions");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
}


if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Information submitted successfully!";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $deleteStmt = $conn->prepare("DELETE FROM submissions WHERE id = ?");
        if ($deleteStmt) {
            $deleteStmt->bind_param("i", $delete_id);
            if ($deleteStmt->execute()) {
               header("Location: " . $_SERVER['PHP_SELF'] . "?success=2");
                exit(); 
            } else {
                $errors[] = "Error: " . $deleteStmt->error;
            }
            $deleteStmt->close();
        } else {
            $errors[] = "Failed to prepare the deletion statement.";
        }
    } else {
        
        $name = trim($_POST['name']);
        $class = trim($_POST['class']);
        $subject = trim($_POST['subject']);
        $email = trim($_POST['email']);
        $mobile = trim($_POST['mobile']);

        
        if (empty($name)) {
            $errors['name'] = "Name is required.";
        }

        if (empty($class)) {
            $errors['class'] = "Class is required.";
        }

        if (empty($subject)) {
            $errors['subject'] = "Subject is required.";
        }

        if (empty($email)) {
            $errors['email'] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format.";
        }

        if (empty($mobile)) {
            $errors['mobile'] = "Mobile number is required.";
        } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
            $errors['mobile'] = "Mobile number must be 10 digits.";
        }

        
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO submissions (name, class, subject, email, mobile) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $name, $class, $subject, $email, $mobile);

                if ($stmt->execute()) {
                    
                    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                    exit(); 
                } else {
                    $errors[] = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errors[] = "Failed to prepare the statement.";
            }
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        

        
        form, .output {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
            width: 100%;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
            position: absolute;
            bottom: -20px;
            left: 0;
        }

        .success {
            color: green;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 2px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .text-center {
            text-align: initial;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
            cursor: pointer;
            border-radius: 3px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
        .btn-outline-success {
        --bs-btn-color: #e3110b;
        --bs-btn-border-color: #eb0909;
        }
        .nav-item{
            font-weight: bold;
            text-align: center;
        }
        img{
            align-items: center;
        }
        
        .navbar-nav{
            margin-left: 272px;
        }
        p{
            color: #fff;
            text-align: center;
            font-weight:600;
            
            
        }
        .section-card{
            padding:50px 0px;
        }
        h3{
            font-size:x-large;
            font-weight: 600;
            color:#3E93B9;
            text-align: left;
        }
        .web-server{
            background-color: #C5070C;
            padding: 70px 0px;
        }
        .card-text{
            color:black;
            text-align: left;
        }
        .form-section{
            padding:50px 0px;
        }
    </style>
</head>
<body>
<Header>
    <div class="container">
        <div class="row">
            <div class="col">
                <img src="logo.PNG">
            </div>
            <div class="col-sm-3">
                <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
</Header>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      
    </div>
  </div>
</nav> 
<div class="image-section"></div>
<img src="light.jpg" width="100%" height="550px">
</div>
<br>
<div class="container">
    <div class="section-card">
      <div class="row">
            <div class="col">
                <div class="card" style="">
                    <img src="imgg.avif" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h3>TITLE</h3>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" style="">
                    <img src="imgg.avif" class="card-img-top" alt="...">
                    <div class="card-body">
                    <h3>TITLE</h3>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" style="">
                    <img src="imgg.avif" class="card-img-top" alt="...">
                    <div class="card-body">
                    <h3>TITLE</h3>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>
<div class="web-server">
<div class="container">
<p>
       A web server is a computer hosting one or more websites. "Hosting" means that all the web pages and their supporting files are available on that computer.
    The web server will send any web page from the website it is hosting to any user's browser, per user request.
    Don't confuse websites and web servers. For example, if you hear someone say, "My website is not responding", 
    it actually means that the web server is not responding and therefore the website is not available. More importantly, since a web server can host multiple websites, 
    the term web server is never used to designate a website, as it could cause great confusion. In our previous example, if we said,
    "My web server is not responding", it means that multiple websites on that web server are not available.
 </p>
  </div>
    </div>
<div class="form-section">
    <div class="container">
        <div class="form-section"></div>
        <div class="row">
            <div class="col">
                <?php if (!empty($successMessage)): ?>
                    <div class="success">
                        <?php
                        if ($_GET['success'] == 1) {
                            echo $successMessage;
                        } elseif ($_GET['success'] == 2) {
                            echo "Data deleted successfully!";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            
                <form method="post" action="">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
                        <span class="error"><?php echo $errors['name'] ?? ''; ?></span><br>
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="class">Class</label>
                        <input type="text" id="class" name="class" value="<?php echo htmlspecialchars($class ?? ''); ?>">
                        <span class="error"><?php echo $errors['class'] ?? ''; ?></span><br>
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>">
                        <span class="error"><?php echo $errors['subject'] ?? ''; ?></span><br>
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        <span class="error"><?php echo $errors['email'] ?? ''; ?></span><br>
                    </div>
                    <br>

                    <div class="form-group">
                        <label for="mobile">Mobile</label>
                        <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile ?? ''); ?>">
                        <span class="error"><?php echo $errors['mobile'] ?? ''; ?></span><br>
                    </div>
                    <br>

                    <input type="submit" name="submit" value="Submit Here"/>
                </form>
            </div>
            <div class="col">
                <div class="output">
                    <h3>All Submitted Data:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entries as $entry): ?>
                                <tr id="row-<?php echo htmlspecialchars($entry['id']); ?>">
                                    <td><?php echo htmlspecialchars($entry['id']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['class']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['mobile']); ?></td>
                                    <td class="text-center">
                                        <form method="post" action="">
                                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($entry['id']); ?>">
                                            <button type="submit" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>        
</body>
</html>
