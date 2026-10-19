<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";

if (!is_admin()) {
    redirect("../login.php");
}

// if there is an id in the url we edit a car, if not we add a new one
$car_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$is_edit = $car_id > 0;

// default empty values
$car = [
    'brand' => '', 'model' => '', 'year' => date("Y"), 'type' => 'Sedan',
    'transmission' => 'Automatic', 'seats' => 5, 'fuel' => 'Petrol',
    'price_per_day' => '', 'image' => '', 'description' => '', 'is_available' => 1
];

if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM cars WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    mysqli_stmt_execute($stmt);
    $car = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$car) {
        set_message("Car not found.", "error");
        redirect("cars.php");
    }
}

$car_types = ["Sedan", "Hatchback", "SUV", "Sports", "Van"];
$fuels = ["Petrol", "Diesel", "Hybrid", "Electric"];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $car['brand'] = trim($_POST['brand']);
    $car['model'] = trim($_POST['model']);
    $car['year'] = (int) $_POST['year'];
    $car['type'] = $_POST['type'];
    $car['transmission'] = $_POST['transmission'];
    $car['seats'] = (int) $_POST['seats'];
    $car['fuel'] = $_POST['fuel'];
    $car['price_per_day'] = $_POST['price_per_day'];
    $car['description'] = trim($_POST['description']);
    $car['is_available'] = isset($_POST['is_available']) ? 1 : 0;

    // validation
    if ($car['brand'] == "" || $car['model'] == "") {
        $errors[] = "Brand and model are required.";
    }
    if ($car['year'] < 1990 || $car['year'] > date("Y") + 1) {
        $errors[] = "Please enter a valid year.";
    }
    if (!in_array($car['type'], $car_types) || !in_array($car['fuel'], $fuels)) {
        $errors[] = "Please choose a type and a fuel from the list.";
    }
    if ($car['seats'] < 1 || $car['seats'] > 20) {
        $errors[] = "Please enter a valid number of seats.";
    }
    if (!is_numeric($car['price_per_day']) || $car['price_per_day'] <= 0) {
        $errors[] = "Price must be a positive number.";
    }

    // image upload
    $has_new_image = isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK;

    if ($has_new_image) {
        $allowed = ["jpg", "jpeg", "png", "webp"];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed)) {
            $errors[] = "Image must be a JPG, PNG or WEBP file.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image is too big (max 2 MB).";
        } elseif (getimagesize($_FILES['image']['tmp_name']) === false) {
            $errors[] = "This file is not a real image.";
        }
    } elseif (!$is_edit) {
        $errors[] = "Please upload an image of the car.";
    }

    if (count($errors) == 0) {
        if ($has_new_image) {
            // give the file a unique name so two cars never have the same file
            $new_name = "car_" . time() . "_" . rand(1000, 9999) . "." . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/cars/" . $new_name);

            // delete the old uploaded image
            if ($is_edit && strpos($car['image'], "uploads/") === 0 && file_exists("../" . $car['image'])) {
                unlink("../" . $car['image']);
            }
            $car['image'] = "uploads/cars/" . $new_name;
        }

        if ($is_edit) {
            $sql = "UPDATE cars SET brand = ?, model = ?, year = ?, type = ?, transmission = ?, seats = ?,
                    fuel = ?, price_per_day = ?, image = ?, description = ?, is_available = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssissisdssii", $car['brand'], $car['model'], $car['year'], $car['type'],
                $car['transmission'], $car['seats'], $car['fuel'], $car['price_per_day'], $car['image'],
                $car['description'], $car['is_available'], $car_id);
            mysqli_stmt_execute($stmt);
            set_message("The car was updated.");
        } else {
            $sql = "INSERT INTO cars (brand, model, year, type, transmission, seats, fuel, price_per_day, image, description, is_available)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssissisdssi", $car['brand'], $car['model'], $car['year'], $car['type'],
                $car['transmission'], $car['seats'], $car['fuel'], $car['price_per_day'], $car['image'],
                $car['description'], $car['is_available']);
            mysqli_stmt_execute($stmt);
            set_message("The car was added.");
        }
        redirect("cars.php");
    }
}

$page_title = $is_edit ? "Edit car" : "Add a car";
require_once "includes/admin-header.php";
?>

<div class="admin-top">
    <div>
        <h1><?php echo $is_edit ? "Edit " . e($car['brand'] . " " . $car['model']) : "Add a new car"; ?></h1>
        <p class="muted">Fill the information below.</p>
    </div>
    <a href="cars.php" class="btn btn-outline">&larr; Back to cars</a>
</div>

<?php foreach ($errors as $error) { ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php } ?>

<div class="box" style="max-width: 820px;">
    <form method="post" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" name="brand" id="brand" value="<?php echo e($car['brand']); ?>" required>
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" value="<?php echo e($car['model']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" name="year" id="year" value="<?php echo e($car['year']); ?>" required>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type">
                    <?php foreach ($car_types as $type) { ?>
                        <option <?php if ($car['type'] == $type) echo "selected"; ?>><?php echo $type; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="transmission">Gearbox</label>
                <select name="transmission" id="transmission">
                    <option <?php if ($car['transmission'] == "Automatic") echo "selected"; ?>>Automatic</option>
                    <option <?php if ($car['transmission'] == "Manual") echo "selected"; ?>>Manual</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fuel">Fuel</label>
                <select name="fuel" id="fuel">
                    <?php foreach ($fuels as $fuel) { ?>
                        <option <?php if ($car['fuel'] == $fuel) echo "selected"; ?>><?php echo $fuel; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="seats">Seats</label>
                <input type="number" name="seats" id="seats" min="1" max="20" value="<?php echo e($car['seats']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price_per_day">Price per day (DT)</label>
                <input type="number" step="0.01" name="price_per_day" id="price_per_day" value="<?php echo e($car['price_per_day']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description"><?php echo e($car['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Car image (JPG, PNG or WEBP, max 2 MB)</label>
            <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.webp">
            <?php if ($is_edit && $car['image'] != "") { ?>
                <img src="../<?php echo e($car['image']); ?>" alt="" class="current-image">
                <p class="muted" style="font-size: 14px;">Leave empty to keep the current image.</p>
            <?php } ?>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_available" <?php if ($car['is_available']) echo "checked"; ?>>Available for booking</label>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo $is_edit ? "Save changes" : "Add car"; ?></button>
    </form>
</div>

<?php require_once "includes/admin-footer.php"; ?>
