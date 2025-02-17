<?php
if (!isset($_GET['filter'])){
    $_GET['filter']['categories'] ='';
    $_GET['filter']['genres'] ='';
    $_GET['filter']['regions'] ='';
    $_GET['filter']['years'] ='';
}
?>
<div class="list-movie-filter SearchMovies" style="margin-bottom: 50px;">
    <div class="list-movie-filter-main">
        <form id="form-filter" class="form-inline" method="GET">
            <div class="list-movie-filter-item Form-Group">
                <label class="AAIco-widgets" for="filter-type">Định dạng</label>
                <div class="">
                    <select class="" id="type" name="filter[categories]" form="form-search">
                        <option value="">Mọi định dạng</option>
                        <?php $categories = get_terms(array('taxonomy' => 'ophim_categories', 'hide_empty' => false,));?>
                        <?php foreach($categories as $category) { ?>
                            <option value='<?php echo $category->name; ?>' <?php if ($category->name == $_GET['filter']['categories']) echo 'selected="selected"'; ?>><?php echo $category->name ; ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="list-movie-filter-item Form-Group">
                <label for="filter-cat_id" class="AAIco-movie_creation">Thể loại</label>
                <div class="">
                    <select class="" id="category" name="filter[genres]" form="form-search">
                        <option value="">Tất cả thể loại</option>
                        <?php $genres = get_terms(array('taxonomy' => 'ophim_genres', 'hide_empty' => false,));?>
                        <?php foreach($genres as $genre) { ?>
                            <option value='<?php echo $genre->name; ?>' <?php if ($genre->name == $_GET['filter']['genres']) echo 'selected="selected"'; ?>><?php echo $genre->name ; ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="list-movie-filter-item Form-Group">
                <label for="filter-country" class="AAIco-public">Quốc gia</label>
                <div class="">
                    <select class="" name="filter[regions]" form="form-search">
                        <option value="">Tất cả quốc gia</option>
                        <?php $regions = get_terms(array('taxonomy' => 'ophim_regions', 'hide_empty' => false,));?>
                        <?php foreach($regions as $region) { ?>
                            <option value='<?php echo $region->name; ?>' <?php if ($region->name == $_GET['filter']['regions']) echo 'selected="selected"'; ?>><?php echo $region->name ; ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="list-movie-filter-item Form-Group">
                <label for="filter-year" class="AAIco-date_range">Năm phát hành</label>
                <div class="">
                    <select class="" name="filter[years]" form="form-search">
                        <option value="">Tất cả năm</option>
                        <?php $years = get_terms(array('taxonomy' => 'ophim_years', 'hide_empty' => false,));?>
                        <?php foreach($years as $year) { ?>
                            <option value='<?php echo $year->name; ?>' <?php if ($year->name == $_GET['filter']['years']) echo 'selected="selected"'; ?>><?php echo $year->name ; ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <button type="submit" form="form-search" class="btn btn-red btn-filter-movie"><span>Lọc</span></button>
            <div class="clear"></div>
        </form>
    </div>
</div>