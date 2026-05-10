<?php
include 'db_connect.php';

$user_id = 1;

$sql = "
    SELECT stories.*
    FROM user_stories
    JOIN stories ON user_stories.story_id = stories.id
    WHERE user_stories.user_id = $user_id
";

$result = mysqli_query($con, $sql);
?>

<h2>Tủ sách</h2>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
    <div>
        <img src="<?php echo $row['sách1.webp']; ?>" width="100">
        <p><?php echo $row['name']; ?></p>
    </div>
<?php } ?>