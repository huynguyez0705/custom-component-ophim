<div class="desc ah-frame-bg">
    <div>
        <h2 class="heading"> Nội dung </h2>
    </div>
    <div>
        <?php if (op_get_showtime_movies()) { ?>
        <p><strong><span style="color:#FFA500"><?= op_get_showtime_movies() ?> <span></strong></p>
        <?php } ?>

        <?php if (op_get_notify()) { ?>
        <p><strong><span style="color:#FFA500"><?= op_get_notify() ?> <span></strong></p>
        <?php } ?>
        <p class="Director">
            <strong>Đạo diễn:</strong>
            <?= op_get_directors(10,', ') ?>
        </p>
        <p class="Cast">
            <strong>Diễn viên:</strong>
            <?= op_get_actors(10,', ') ?>
        </p>
        <p class="heading"></p>
        <div>
            <p><?php the_content();?></p>
        </div>
    </div>
</div>