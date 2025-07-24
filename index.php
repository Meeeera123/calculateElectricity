<?php
// Calculate electricity (Fix using function)
// Function: calculateElectricity
// Inputs: voltage (V), current (A), rate (sen per kWh)

function calculateElectricity($voltage, $current, $current_rate) {
    $results = [];

    // Formula reference:
    // Power (W)   = Voltage (V) × Current (A)
    // Energy (kWh) = (Power (W) × Hour) ÷ 1000
    // Total (RM)  = Energy (kWh) × (Rate (sen) ÷ 100)

    // Calculate power in watts
    $power_watt = $voltage * $current;

    // Convert to kilowatts
    $power_kw = $power_watt / 1000;

    // Convert sen to RM
    $rate_rm = $current_rate / 100;

    // Calculate for each hour from 1 to 24
    for ($hour = 1; $hour <= 24; $hour++) {
        $energy = $power_kw * $hour;    // kWh
        $total  = $energy * $rate_rm;   // RM

        $results[] = [
            'hour'   => $hour,
            'energy' => number_format($energy, 5),
            'total'  => number_format($total, 2)
        ];
    }

    return [
        'power_kw' => $power_kw,
        'rate_rm'  => $rate_rm,
        'rows'     => $results
    ];
}

// Main script starts here
$error = "";
$power_kw = 0;
$rate_rm = 0;
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voltage = $_POST['voltage'];
    $current = $_POST['current'];
    $current_rate = $_POST['rate'];

    // Validate inputs
    if (!is_numeric($voltage) || !is_numeric($current) || !is_numeric($current_rate)) {
        $error = "Please enter numeric values only.";
    } elseif ($voltage < 0 || $current < 0 || $current_rate < 0) {
        $error = "Values cannot be negative. Please enter positive numbers.";
    } else {
        // Convert to float
        $voltage = floatval($voltage);
        $current = floatval($current);
        $current_rate = floatval($current_rate);

        // Perform calculation
        $calc = calculateElectricity($voltage, $current, $current_rate);
        $power_kw = $calc['power_kw'];
        $rate_rm  = $calc['rate_rm'];
        $results  = $calc['rows'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Electricity Rate Calculator</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    body {
      background: linear-gradient(135deg, #005BAC 0%, #007BFF 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 15px;
    }
    .card {
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      border-radius: 15px;
    }
    h1 { font-weight: 700; color: #333; }
    .table thead th { background-color: #343a40; color: #fff; }
    footer {
      margin-top: 20px;
      color: #fff;
      text-align: center;
      font-size: 0.9rem;
      opacity: 0.8;
    }
  </style>
</head>

<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card p-4">
        <h1 class="text-center mb-4">Electricity Calculator</h1>

        <?php if($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Input form -->
        <form method="post" class="mb-4" onsubmit="return validateNumbers();">
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="voltage">Voltage (V)</label>
              <input type="number" step="0.01" min="0" class="form-control" id="voltage" name="voltage"
                     required value="<?=isset($_POST['voltage'])?$_POST['voltage']:''?>">
            </div>
            <div class="form-group col-md-4">
              <label for="current">Current (A)</label>
              <input type="number" step="0.01" min="0" class="form-control" id="current" name="current"
                     required value="<?=isset($_POST['current'])?$_POST['current']:''?>">
            </div>
            <div class="form-group col-md-4">
              <label for="rate">Rate (sen/kWh)</label>
              <input type="number" step="0.01" min="0" class="form-control" id="rate" name="rate"
                     required value="<?=isset($_POST['rate'])?$_POST['rate']:''?>">
            </div>
          </div>
          <button type="submit"
                  class="btn btn-primary btn-block btn-lg"
                  data-toggle="tooltip"
                  data-placement="top"
                  title="Click to calculate electricity usage and cost">🔍 Calculate
          </button>
        </form>

        <?php if(!empty($results) && !$error): ?>
          <div class="alert alert-info">
            <strong>Power:</strong> <?=number_format($power_kw,5)?> kW &nbsp; | &nbsp;
            <strong>Rate:</strong> <?=number_format($rate_rm,3)?> RM/kWh
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
              <thead>
                <tr>
                  <th>Hour</th>
                  <th>Energy (kWh)</th>
                  <th>Total (RM)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($results as $row): ?>
                  <tr>
                    <td><?= $row['hour'] ?></td>
                    <td><?= $row['energy'] ?></td>
                    <td><?= $row['total'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    
    </div>
  </div>
</div>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  $(function () { $('[data-toggle="tooltip"]').tooltip() });

  function validateNumbers() {
    let v = document.getElementById('voltage').value;
    let c = document.getElementById('current').value;
    let r = document.getElementById('rate').value;

    if (isNaN(v) || isNaN(c) || isNaN(r)) {
      alert("Please enter only numeric values.");
      return false;
    }
    if (v < 0 || c < 0 || r < 0) {
      alert("Values cannot be negative. Please enter positive numbers.");
      return false;
    }
    return true;
  }
</script>
</body>
</html>
