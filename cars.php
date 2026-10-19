<?php
$page_title = "Our cars";
require_once "includes/header.php";

// read the filters from the url
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : "";
$type = isset($_GET['type']) ? trim($_GET['type']) : "";
$max_price = isset($_GET['max_price']) ? trim($_GET['max_price']) : "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : "";

// build the query step by step
$sql = "SELECT * FROM cars WHERE is_available = 1";
$param_types = "";
$params = [];

if ($brand != "") {
    $sql .= " AND brand = ?";
    $param_types .= "s";
    $params[] = $brand;
}
if ($type != "") {
    $sql .= " AND type = ?";
    $param_types .= "s";
    $params[] = $type;
}
if ($max_price != "" && is_numeric($max_price)) {
    $sql .= " AND price_per_day <= ?";
    $param_types .= "d";
    $params[] = $max_price;
}

// sorting (only allowed values, never put user text directly in sql)
if ($sort == "price_low") {
    $sql .= " ORDER BY price_per_day ASC";
} elseif ($sort == "price_high") {
    $sql .= " ORDER BY price_per_day DESC";
} else {
    $sql .= " ORDER BY id DESC";
}

$stmt = mysqli_prepare($conn, $sql);
if (count($params) > 0) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
}
mysqli_stmt_execute($stmt);
$cars = mysqli_stmt_get_result($stmt);

$brands = mysqli_query($conn, "SELECT DISTINCT brand FROM cars WHERE is_available = 1 ORDER BY brand");
$types = mysqli_query($conn, "SELECT DISTINCT type FROM cars WHERE is_available = 1 ORDER BY type");
?>

<section class="page-header">
    <div class="container">
        <h1>Our cars</h1>
        <p class="muted">Find the car that fits your trip and your budget.</p>
    </div>
</section>

<section class="container">
    <form method="get" class="filter-form">
        <div class="form-group">
            <label for="brand">Brand</label>
            <select name="brand" id="brand">
                <option value="">All brands</option>
                <?php while ($row = mysqli_fetch_assoc($brands)) { ?>
                    <option value="<?php echo e($row['brand']); ?>" <?php if ($row['brand'] == $brand) echo "selected"; ?>>
                        <?php echo e($row['brand']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type">
                <option value="">All types</option>
                <?php while ($row = mysqli_fetch_assoc($types)) { ?>
                    <option value="<?php echo e($row['type']); ?>" <?php if ($row['type'] == $type) echo "selected"; ?>>
                        <?php echo e($row['type']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="max_price">Max price / day</label>
            <input type="number" name="max_price" id="max_price" min="0" value="<?php echo e($max_price); ?>" placeholder="Any">
        </div>
        <div class="form-group">
            <label for="sort">Sort by</label>
            <select name="sort" id="sort">
                <option value="">Newest</option>
                <option value="price_low" <?php if ($sort == "price_low") echo "selected"; ?>>Price: low to high</option>
                <option value="price_high" <?php if ($sort == "price_high") echo "selected"; ?>>Price: high to low</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="cars.php" class="btn btn-outline">Reset</a>
    </form>

    <p class="results-count"><?php echo mysqli_num_rows($cars); ?> car(s) found</p>

    <?php if (mysqli_num_rows($cars) == 0) { ?>
        <div class="empty-box">
            <h3>No cars match your search</h3>
            <p class="muted">Try another brand or a higher price.</p>
            <a href="cars.php" class="btn btn-primary">Show all cars</a>
        </div>
    <?php } else { ?>
        <div class="cars-grid">
            <?php while ($car = mysqli_fetch_assoc($cars)) { ?>
                <?php include "includes/car-card.php"; ?>
            <?php } ?>
        </div>
    <?php } ?>
</section>

<?php require_once "includes/footer.php"; ?>
