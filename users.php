<?php
  $title = "Utilisateurs";
  include_once("inc/header.php");
  if($loggedUserRole['short_name'] == "infirmier" || $loggedUserRole['short_name'] == "pharmacien") {
    header("Location: dashboard.php");
  }
  $users = getEverythingFromTable("users", "WHERE is_deleted = 0");
  $showAdd = true;
  $showEdit = true;
  $showDelete = true;
  if($loggedUserRole['short_name'] == "medecin") {
    $showAdd = false;
    $loggedUserId = $loggedUserData->id;
    $users = getEverythingFromTable("users", "WHERE is_deleted = 0 AND manager = $loggedUserId AND (type = 3 OR type = 4)");
  }
?>
<div class="container">
 <div class="card">
   <div class="card-header">
     <nav class="navbar navbar-light">
        <h5 class="mb-0">Utilisateurs</h5>
          <ul class="nav nav-pills">
            <?php if($showAdd) { ?>
              <li class="nav-item">
                <a href="addUser.php" class="btn btn-primary">Ajouter un utilisateur</a>
              </li>
            <?php } ?>
          </ul>
      </nav>
   </div>
   <div class="card-body">
     <div class="table-responsive">
       <table class="table table-hover table-bordered">
         <thead>
           <tr>
             <th>N°</th>
             <th>Nom d'utilisateur</th>
             <th>Type d'utilisateur</th>
             <th>Nom complet</th>
             <th>Actions</th>
           </tr>
         </thead>
         <tbody>
           <?php
              $i = 1;
              foreach($users as $user) {
                $userId = $user['id'];
                $editButton = "<a href='editUser.php?id=$userId' class='btn btn-success'><i class='fa fa-pencil-circle'></i> Modifier</a>";
                $deleteButton = "<button class='btn btn-danger delUser' data-id='".$userId."' style='margin-left: 1rem;'><i class='fa fa-trash'></i> Supprimer</button>";
                $actionContent = "";
                if($showEdit) {
                  $actionContent .= $editButton;
                }
                if($showDelete) {
                  $actionContent .= $deleteButton;
                }
                $usernameText = $user['username'];
                $roleData = getUserRole($userId);
                $name = "";
                if(!empty($user["firstname"]) || !empty($user["lastname"])) {
                  $name = $user["firstname"]." ".$user["lastname"];
                }
                if($userId == $loggedUserData->id) {
                  $actionContent = $editButton;
                  $usernameText .= " <b>(YOU)</b>";
                }
                echo "<tr>";
                echo "<td>".$i."</td>";
                echo "<td>".$usernameText."</td>";
                echo "<td>".$roleData["pretty_name"]."</td>";
                echo "<td>".$name."</td>";
                echo "<td>".$actionContent."</td>";
                echo "</tr>";
                $i++;
              }
           ?>
         </tbody>
       </table>
     </div>
   </div>

 </div>
</div>
<?php include_once("inc/footer.php"); ?>
<script type="text/javascript">
  $(document).ready(function() {
    let table = $("table").DataTable({
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.11.4/i18n/fr_fr.json"
      }
    })
    $(document).on('click', 'button.delUser', function() {
      let userId = $(this).data('id')
      Swal.fire({
        title: "Confirmation",
        html: "Êtes-vous sûr de vouloir <b style='color: red;'>supprimer</b> cet utilisateur?",
        showCancelButton: true
      }).then((clicked) => {
        if(clicked.isConfirmed) {
          deleteUser(userId)
        }
      })
    })

    function deleteUser(id) {
      let form = $('<form class="" action="api.php" method="post" onComplete="defaultApiRes" before="showLoading"></form>')
      form.append('<input type="hidden" name="id" value="'+id+'" />')
      form.append('<button type="submit" name="deleteUser"></button>')
      $("body").append(form)
      form.submit()
      form.remove()
    }
  })
</script>
