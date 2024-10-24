<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// Fetch launcher templates
$stmt = $pdo->prepare("SELECT * FROM launcher_templates");
$stmt->execute();
$launchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missile Templates</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div class="container-fluid mt-5 mb-5">
        <a href ="../">← Back</a>
        <h1 class="mb-4">Missile Templates</h1>
        <table id="launchersTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Model</th>
                    <th>Rocket Name</th>
                    <th>Mass (kg)</th> <!-- kilograms for mass -->
                    <th>Area (m²)</th> <!-- square meters for area -->
                    <th>Speed (m/s)</th> <!-- meters per second for speed -->
                    <th>Country</th>
                    <th>Range (m)</th> <!-- meters for range -->
                    <th>Explosive Yield (kt)</th> <!-- kilotons for explosive yield -->
                    <th>Overpressure (MPa)</th> <!-- megapascals for overpressure -->
                    <th>Blast Radius (m)</th> <!-- meters for blast radius -->
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($launchers as $launcher): ?>
                <tr>
                    <td><?= $launcher['id'] ?></td>
                    <td><?= $launcher['name'] ?></td>
                    <td><?= $launcher['model'] ?></td>
                    <td><?= $launcher['rocket_name'] ?></td>
                    <td><?= $launcher['mass'] ?></td>
                    <td><?= $launcher['area'] ?></td>
                    <td><?= $launcher['speed'] ?></td>
                    <td><?= $launcher['country'] ?></td>
                    <td><?= $launcher['range'] ?></td>
                    <td><?= $launcher['explosive_yield'] ?></td>
                    <td><?= $launcher['overpressure'] ?></td>
                    <td><?= $launcher['blast_radius'] ?></td>
                    <td><?= $launcher['description'] ?></td>
                    <td><button class="btn btn-primary btn-edit" data-id="<?= $launcher['id'] ?>">Edit</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Launcher</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editLauncherForm">
                        <input type="hidden" id="launcherId" name="id">

                        <!-- Form fields for editing each attribute -->
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>

                        <div class="form-group">
                            <label for="rocket_name">Rocket Name</label>
                            <input type="text" class="form-control" id="rocket_name" name="rocket_name" required>
                        </div>

                        <div class="form-group">
                            <label for="mass">Mass (kg)</label>
                            <input type="number" class="form-control" id="mass" name="mass" required>
                        </div>

                        <div class="form-group">
                            <label for="area">Area (m²)</label>
                            <input type="number" step="0.01" class="form-control" id="area" name="area" required>
                        </div>

                        <div class="form-group">
                            <label for="speed">Speed (m/s)</label>
                            <input type="number" class="form-control" id="speed" name="speed" required>
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                        </div>

                        <div class="form-group">
                            <label for="range">Range (m)</label>
                            <input type="number" class="form-control" id="range" name="range" required>
                        </div>

                        <div class="form-group">
                            <label for="explosive_yield">Explosive Yield (kt)</label>
                            <input type="number" step="0.01" class="form-control" id="explosive_yield" name="explosive_yield" required>
                        </div>

                        <div class="form-group">
                            <label for="overpressure">Overpressure (MPa)</label>
                            <input type="number" step="0.01" class="form-control" id="overpressure" name="overpressure" required>
                        </div>

                        <div class="form-group">
                            <label for="blast_radius">Blast Radius (m)</label>
                            <input type="number" step="0.01" class="form-control" id="blast_radius" name="blast_radius" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JS Files -->
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Bootstrap 4 JS -->
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#launchersTable').DataTable();

            // Show the edit modal with launcher data
            $('.btn-edit').on('click', function() {
                var id = $(this).data('id');
                console.log(id)
                $.ajax({
                    url: '../scripts/get_launcher_template.php',
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        var launcher = JSON.parse(response);
                        $('#launcherId').val(launcher.id);
                        $('#name').val(launcher.name);
                        $('#model').val(launcher.model);
                        $('#rocket_name').val(launcher.rocket_name);
                        $('#mass').val(launcher.mass);
                        $('#area').val(launcher.area);
                        $('#speed').val(launcher.speed);
                        $('#country').val(launcher.country);
                        $('#range').val(launcher.range);
                        $('#explosive_yield').val(launcher.explosive_yield);
                        $('#overpressure').val(launcher.overpressure);
                        $('#blast_radius').val(launcher.blast_radius);
                        $('#description').val(launcher.description);
                        $('#editModal').modal('show');
                    }
                });
            });

            // Handle form submission for saving changes
            $('#editLauncherForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '../scripts/update_launcher_template.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        location.reload(); // Reload the page after saving
                    }
                });
            });
        });
    </script>
</body>
</html>
