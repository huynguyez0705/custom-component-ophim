<?php include( get_template_directory() . '/filter.php' ); ?>
<?php
if (!isset($_GET['filter'])){
    $_GET['filter']['categories'] ='';
    $_GET['filter']['genres'] ='';
    $_GET['filter']['regions'] ='';
    $_GET['filter']['years'] ='';
    $_GET['filter']['status']     = ''; // thêm dòng này
}
?>
<div class="div_filter">
    <form id="form-search" class="form-inline" method="GET" action="/">
        <div class="div_filter-main">
            <div class="">
                <select class="form-control" id="type" name="filter[categories]" form="form-search">
                    <option value="">Mọi định dạng</option>
                    <?php $categories = get_terms(array('taxonomy' => 'ophim_categories', 'hide_empty' => false,));?>
                    <?php foreach($categories as $category) { ?>
                    <option value='<?php echo $category->name; ?>'
                        <?php if ($category->name == $_GET['filter']['categories']) echo 'selected="selected"'; ?>>
                        <?php echo $category->name ; ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="">
                <select class="form-control" id="category" name="filter[genres]" form="form-search">
                    <option value="">Tất cả thể loại</option>
                    <?php $genres = get_terms(array('taxonomy' => 'ophim_genres', 'hide_empty' => false,));?>
                    <?php foreach($genres as $genre) { ?>
                    <option value='<?php echo $genre->name; ?>'
                        <?php if ($genre->name == $_GET['filter']['genres']) echo 'selected="selected"'; ?>>
                        <?php echo $genre->name ; ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="">
                <select class="form-control" name="filter[regions]" form="form-search">
                    <option value="">Tất cả quốc gia</option>
                    <?php $regions = get_terms(array('taxonomy' => 'ophim_regions', 'hide_empty' => false,));?>
                    <?php foreach($regions as $region) { ?>
                    <option value='<?php echo $region->name; ?>'
                        <?php if ($region->name == $_GET['filter']['regions']) echo 'selected="selected"'; ?>>
                        <?php echo $region->name ; ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="">
                <select class="form-control" name="filter[years]" form="form-search">
                    <option value="">Tất cả năm</option>
                    <?php $years = get_terms(array('taxonomy' => 'ophim_years', 'hide_empty' => false,));?>
                    <?php foreach($years as $year) { ?>
                    <option value='<?php echo $year->name; ?>'
                        <?php if ($year->name == $_GET['filter']['years']) echo 'selected="selected"'; ?>>
                        <?php echo $year->name ; ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="">
                <?php $statuses = ['trailer'   => 'Sắp chiếu','ongoing'   => 'Đang chiếu','completed' => 'Hoàn thành',]; ?>
                <select class="form-control" name="filter[status]" form="form-search">
                    <option value="">Tất cả trạng thái</option>
                    <?php foreach($statuses as $slug => $label): ?>
                    <option value="<?php echo $slug; ?>"
                        <?php if ($_GET['filter']['status'] === $slug) echo 'selected'; ?>>
                        <?php echo $label; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="">
                <button class="button-filter bg-red" form="form-search" type="submit"> <span
                        class="material-icons-round">filter_alt</span> Lọc Phim</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>