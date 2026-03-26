<?php
session_start();
require_once '../db.php';

$college_id = isset($_SESSION['college_id']) ? (int)$_SESSION['college_id'] : 1;

$result = mysqli_query($db, "
    SELECT * FROM mum_teacher 
    WHERE college_id = $college_id 
    ORDER BY id DESC
");

$total = mysqli_num_rows($result);

require_once(__DIR__ . '/header3.php');
?>

<div class="container py-5">

    <h2 class="text-center mb-4">
        Our Teachers (<?php echo $total; ?>)
    </h2>

    <div class="row">

    <?php if($total > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100">

                <?php
                $image = !empty($row['image_path']) ? $row['image_path'] : '';
                $fullPath = "../" . $image;
                ?>

                <!-- Image Section -->
                <?php if(!empty($image) && file_exists($fullPath)): ?>
                    <img src="<?php echo '../' . htmlspecialchars($image); ?>" 
                         class="card-img-top"
                         style="height:150px;object-fit:cover;">
                <?php else: ?>
                    <div class="bg-secondary text-white text-center d-flex align-items-center justify-content-center"
                         style="height:150px;font-size:40px;">
                        <?php echo strtoupper(substr($row['name'],0,1)); ?>
                    </div>
                <?php endif; ?>

                <!-- Card Body -->
                <div class="card-body">

                    <h5 class="card-title">
                        <?php echo htmlspecialchars($row['name']." ".$row['surname']); ?>
                    </h5>

                

                    <p class="mb-1">
                        <strong>Qualification:</strong><br>
                        <?php echo htmlspecialchars($row['highest_qualification']); ?>
                    </p>

              

               

                </div>

            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center">
            <h5>No Teachers Found</h5>
        </div>
    <?php endif; ?>

    </div>
</div>

<?php require_once(__DIR__ . '/footer3.php'); ?>
