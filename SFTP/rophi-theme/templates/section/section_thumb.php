<div class="list-films film-new">
    <div class="title-box">
        <a title="<?= $title; ?>" href="<?= $slug; ?>" class="tab active"><?= $title; ?></a>
    </div>
    <div class="card-h tab-content">
        <?php $key =0; while ($query->have_posts()) : $query->the_post();
            $xClass = 'item';
            include THEMETEMPLADE.'/section/section_thumb_item.php';
        endwhile; ?>
    </div>
</div>
