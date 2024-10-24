<?php
require_once('../libraries/lib.php');
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// Fetch air defense templates
$stmt = $pdo->prepare("SELECT * FROM airdefense_templates");
$stmt->execute();
$airdefenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Defense Templates</title>
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-5 mb-5">
        <a href ="../">← Back</a>
        <h1 class="mb-4">Air Defense Templates</h1>
        <table id="airDefensesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Model</th>
                    <th>Country</th>
                    <th>Num Rockets</th>
                    <th>Reaction Time (s)</th> <!-- seconds for reaction time -->
                    <th>Interception Range (m)</th> <!-- meters for interception range -->
                    <th>Detection Range (m)</th> <!-- meters for detection range -->
                    <th>Accuracy (%)</th> <!-- percentage for accuracy -->
                    <th>Reload Time (s)</th> <!-- seconds for reload time -->
                    <th>Max Simultaneous Targets</th>
                    <th>Interception Speed (m/s)</th> <!-- meters per second for interception speed -->
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($airdefenses as $airdefense): ?>
                <tr>
                    <td><?= $airdefense['id'] ?></td>
                    <td><?= $airdefense['name'] ?></td>
                    <td><?= $airdefense['model'] ?></td>
                    <td><?= $airdefense['country'] ?></td>
                    <td><?= $airdefense['num_rockets'] ?></td>
                    <td><?= $airdefense['reaction_time'] ?></td>
                    <td><?= $airdefense['interception_range'] ?></td>
                    <td><?= $airdefense['detection_range'] ?></td>
                    <td><?= $airdefense['accuracy'] ?></td>
                    <td><?= $airdefense['reload_time'] ?></td>
                    <td><?= $airdefense['max_simultaneous_targets'] ?></td>
                    <td><?= $airdefense['interception_speed'] ?></td>
                    <td><?= $airdefense['description'] ?></td>
                    <td><button class="btn btn-primary btn-edit" data-id="<?= $airdefense['id'] ?>">Edit</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editAirDefenseModal" tabindex="-1" aria-labelledby="editAirDefenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAirDefenseModalLabel">Edit Air Defense</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAirDefenseForm">
                        <input type="hidden" id="airDefenseId" name="id">

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
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                        </div>

                        <div class="form-group">
                            <label for="num_rockets">Number of Rockets</label>
                            <input type="number" class="form-control" id="num_rockets" name="num_rockets" required>
                        </div>

                        <div class="form-group">
                            <label for="reaction_time">Reaction Time (s)</label>
                            <input type="number" step="0.1" class="form-control" id="reaction_time" name="reaction_time" required>
                        </div>

                        <div class="form-group">
                            <label for="interception_range">Interception Range (m)</label>
                            <input type="number" class="form-control" id="interception_range" name="interception_range" required>
                        </div>

                        <div class="form-group">
                            <label for="detection_range">Detection Range (m)</label>
                            <input type="number" class="form-control" id="detection_range" name="detection_range" required>
                        </div>

                        <div class="form-group">
                            <label for="accuracy">Accuracy (%)</label>
                            <input type="number" step="0.01" class="form-control" id="accuracy" name="accuracy" required>
                        </div>

                        <div class="form-group">
                            <label for="reload_time">Reload Time (s)</label>
                            <input type="number" class="form-control" id="reload_time" name="reload_time" required>
                        </div>

                        <div class="form-group">
                            <label for="max_simultaneous_targets">Max Simultaneous Targets</label>
                            <input type="number" class="form-control" id="max_simultaneous_targets" name="max_simultaneous_targets" required>
                        </div>

                        <div class="form-group">
                            <label for="interception_speed">Interception Speed (m/s)</label>
                            <input type="number" class="form-control" id="interception_speed" name="interception_speed" required>
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
            $('#airDefensesTable').DataTable();

            // Show the edit modal with air defense data
            $('.btn-edit').on('click', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '../scripts/get_airdefense_template.php',
                    type: 'POST',
                    data: { templateId: id },
                    success: function(response) {
                        var airdefense = JSON.parse(response);
                        $('#airDefenseId').val(airdefense.id);
                        $('#name').val(airdefense.name);
                        $('#model').val(airdefense.model);
                        $('#country').val(airdefense.country);
                        $('#num_rockets').val(airdefense.num_rockets);
                        $('#reaction_time').val(airdefense.reaction_time);
                        $('#interception_range').val(airdefense.interception_range);
                        $('#detection_range').val(airdefense.detection_range);
                        $('#accuracy').val(airdefense.accuracy);
                        $('#reload_time').val(airdefense.reload_time);
                        $('#max_simultaneous_targets').val(airdefense.max_simultaneous_targets);
                        $('#interception_speed').val(airdefense.interception_speed);
                        $('#description').val(airdefense.description);
                        $('#editAirDefenseModal').modal('show');
                    }
                });
            });

            // Handle form submission for saving changes
            $('#editAirDefenseForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '../scripts/update_airdefense_template.php',
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
