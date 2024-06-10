<?php
include "db.php";

// Requête SQL pour récupérer les informations de l'utilisateur
$sql = "SELECT * FROM `crud`";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Utilisateur</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <div class="container mt-5">
    <h1>Profil Utilisateur</h1>
    <div class="row">
    <div class="col-md-4">
    <img src="static/person.jpeg"/>
            <div class="mt-5">
              <a href="editUser.php?id=<?php echo $row["id"] ?>" class="btn btn-primary">Modifier</a>
            </div>
        </div>
      <div class="col-md-8">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-1">
              <p><h6>First Name: </h6><span><?php echo $row['first_name']; ?></span></p>
            </div>
            <div class="mb-1">
              <p><h6>Lastname: </h6><span><?php echo $row['last_name']; ?></span></p>
            </div>
            <div class="mb-1">
            <p><h6>Email: </h6><span><?php echo $row['email']; ?></span></p>
            </div>
            <div class="mb-1`">
              <p><h6>Gender: </h6><span><?php echo $row['gender']; ?></span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
