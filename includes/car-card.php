<?php
// one car card, used on the home page and the cars page
// the $car variable must exist before including this file
?>
<div class="car-card">
    <div class="car-card-image">
        <img src="<?php echo e($car['image']); ?>" alt="<?php echo e($car['brand'] . ' ' . $car['model']); ?>">
    </div>
    <div class="car-card-body">
        <h3><?php echo e($car['brand'] . ' ' . $car['model']); ?></h3>
        <p class="muted"><?php echo e($car['year']); ?> &middot; <?php echo e($car['type']); ?></p>

        <div class="car-specs">
            <span class="chip"><?php echo e($car['seats']); ?> seats</span>
            <span class="chip"><?php echo e($car['transmission']); ?></span>
            <span class="chip"><?php echo e($car['fuel']); ?></span>
        </div>

        <div class="car-card-footer">
            <div class="price"><?php echo format_price($car['price_per_day']); ?> <small>/ day</small></div>
            <a href="car.php?id=<?php echo $car['id']; ?>" class="btn btn-outline btn-small">View details</a>
        </div>
    </div>
</div>
