<?php
  $title = "Clients";
  include_once("inc/header.php");
  if($loggedUserRole['short_name'] == "infirmier" || $loggedUserRole['short_name'] == "pharmacien") {
    header("Location: dashboard.php");
  }
  $clients = getEverythingFromTable("clients", "WHERE is_deleted = 0");
  $showAdd = true;
  $showEdit = true;
  $showDelete = true;
/*  if($loggedUserRole['short_name'] == "medecin") {
    $showAdd = false;
    $loggedUserId = $loggedUserData->id;
    $clients = getEverythingFromTable("clients", "WHERE is_deleted = 0 AND manager = $loggedUserId AND (type = 3 OR type = 4)");
  }*/
?>
<div class="container">
 <div class="card">
   <div class="card-header">
     <nav class="navbar navbar-light">
        <h5 class="mb-0">Clients</h5>
          <ul class="nav nav-pills">
            <?php if($showAdd) { ?>
              <li class="nav-item">
                <a href="addClient.php" class="btn btn-primary">Ajouter un client</a>
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
             <th>Prénom</th>
             <th>Nom</th>
             <th>numéro de téléphone</th>
             <th>Actions</th>
           </tr>
         </thead>
         <tbody>
           <?php
              $i = 1;
              foreach($clients as $client) {
                $clientId = $client['id'];
                $editButton = "<a href='editClient.php?id=$clientId' class='btn btn-success'><i class='fa fa-pencil-circle'></i> Modifier</a>";
                $deleteButton = "<button class='btn btn-danger delClient' data-id='".$clientId."' style='margin-left: 1rem;'><i class='fa fa-trash'></i> Supprimer</button>";
                $actionContent = "";
                if($showEdit) {
                  $actionContent .= $editButton;
                }
                if($showDelete) {
                  $actionContent .= $deleteButton;
                }
                $clientID = $client['id'];
                $clientnamePhone = $client['numberphone'];
                // $roleData = getUserRole($clientId);
                $name = "";
                if(!empty($client["firstname"]) || !empty($client["lastname"])) {
                  $clientLname = $client["firstname"];
                  $clientFname = $client["lastname"];
                }
                /*$name = "";
                if(!empty($client["firstname"]) || !empty($client["lastname"])) {
                  $name = $client["firstname"]." ".$client["lastname"];
                }*/
                if($clientId == $loggedUserData->id) {
                  $actionContent = $editButton;
                  // $clientnamePhone .= " <b>(YOU)</b>";
                }
                echo "<tr>";
                echo "<td>".$clientID."</td>";
                echo "<td>".$clientFname."</td>";
                echo "<td>".$clientLname."</td>";
                echo "<td>".$clientnamePhone."</td>";
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
    $(document).on('click', 'button.delClient', function() {
      let clientId = $(this).data('id')
      Swal.fire({
        title: "Confirmation",
        html: "Êtes-vous sûr de vouloir <b style='color: red;'>supprimer</b> cet utilisateur?",
        showCancelButton: true
      }).then((clicked) => {
        if(clicked.isConfirmed) {
          deleteClient(clientId)
        }
      })
    })

    function deleteClient(id) {
      let form = $('<form class="" action="api.php" method="post" onComplete="defaultApiRes" before="showLoading"></form>')
      form.append('<input type="hidden" name="id" value="'+id+'" />')
      form.append('<button type="submit" name="deleteClient"></button>')
      $("body").append(form)
      form.submit()
      form.remove()
    }
  })
</script>
