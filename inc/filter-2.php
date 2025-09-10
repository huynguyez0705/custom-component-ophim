
<?php
if (!isset($_GET['filter'])) {
    $_GET['filter']['categories'] = '';
    $_GET['filter']['genres'] = '';
    $_GET['filter']['regions'] = '';
    $_GET['filter']['years'] = '';
    $_GET['filter']['status'] = '';
    $_GET['filter']['sort'] = '';
    $_GET['filter']['lang'] = '';
}
?>
<form id="searchform" class="form-inline" method="GET" action="/">
    <div class="list-movie-filter SearchMovies" style="margin-bottom: 50px;">
        <div class="list-movie-filter-main">
                <!-- Quốc gia -->
                <div class="list-movie-filter-item Form-Group">
                    <label class="AAIco-public <?php echo ($_GET['filter']['regions'] ? 'active' : ''); ?>">Quốc gia</label>
                    <div class="">
                        <select class="" name="filter[regions]" form="searchform">
                            <option value="">Tất cả quốc gia</option>
                            <?php $regions = get_terms(array('taxonomy' => 'ophim_regions', 'hide_empty' => false,)); ?>
                            <?php foreach($regions as $region) { ?>
                                <option value='<?php echo $region->name; ?>' <?php if ($region->name == $_GET['filter']['regions']) echo 'selected="selected"'; ?>>
                                    <?php echo $region->name ; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Loại phim -->
                <div class="list-movie-filter-item Form-Group">
                    <label class="AAIco-widgets <?php echo ($_GET['filter']['categories'] ? 'active' : ''); ?>">Loại phim</label>
                    <div class="">
                        <select class="" name="filter[categories]" form="searchform">
                            <option value="">Mọi định dạng</option>
                            <?php $categories = get_terms(array('taxonomy' => 'ophim_categories', 'hide_empty' => false,)); ?>
                            <?php foreach($categories as $category) { ?>
                                <option value='<?php echo $category->name; ?>' <?php if ($category->name == $_GET['filter']['categories']) echo 'selected="selected"'; ?>>
                                    <?php echo $category->name ; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Thể loại -->
                <div class="list-movie-filter-item Form-Group">
                    <label class="AAIco-movie_creation <?php echo ($_GET['filter']['genres'] ? 'active' : ''); ?>">Thể loại</label>
                    <div class="">
                        <select class="" name="filter[genres]" form="searchform">
                            <option value="">Tất cả thể loại</option>
                            <?php $genres = get_terms(array('taxonomy' => 'ophim_genres', 'hide_empty' => false,)); ?>
                            <?php foreach($genres as $genre) { ?>
                                <option value='<?php echo $genre->name; ?>' <?php if ($genre->name == $_GET['filter']['genres']) echo 'selected="selected"'; ?>>
                                    <?php echo $genre->name ; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Năm phát hành -->
                <div class="list-movie-filter-item Form-Group">
                    <label class="AAIco-date_range <?php echo ($_GET['filter']['years'] ? 'active' : ''); ?>">Năm phát hành</label>
                    <div class="">
                        <select class="" name="filter[years]" form="searchform">
                            <option value="">Tất cả năm</option>
                            <?php
                            $years = get_terms(array(
                                'taxonomy' => 'ophim_years',
                                'hide_empty' => false,
                                'orderby' => 'name',
                                'order' => 'DESC',
                            ));
                            $filtered_years = array_filter($years, function($year) {
                                return is_numeric($year->name) && $year->name >= 1995;
                            });
                            ?>
                            <?php foreach($filtered_years as $year) { ?>
                                <option value='<?php echo $year->name; ?>' <?php if ($year->name == $_GET['filter']['years']) echo 'selected="selected"'; ?>>
                                    <?php echo $year->name ; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="list-movie-filter-item Form-Group">
                    <label class="AAIco-check_circle <?php echo ($_GET['filter']['status'] ? 'active' : ''); ?>">Trạng thái</label>
                    <div class="">
                        <?php $statuses = [
                            'trailer' => 'Sắp chiếu',
                            'ongoing' => 'Đang chiếu',
                            'completed' => 'Hoàn thành',
                        ]; ?>
                        <select class="" name="filter[status]" form="searchform">
                            <option value="">Tất cả trạng thái</option>
                            <?php foreach($statuses as $slug => $label): ?>
                                <option value="<?php echo $slug; ?>" <?php if ($_GET['filter']['status'] === $slug) echo 'selected'; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Sắp xếp -->
                <div class="list-movie-filter-item Form-Group">
                    <label class="AAIco-sort <?php echo ($_GET['filter']['sort'] ? 'active' : ''); ?>">Sắp xếp</label>
                    <div class="">
                        <?php $sort_options = [
                            '' => 'Mặc định',
                            'newest' => 'Mới nhất',
                            'updated' => 'Mới cập nhật',
                            'views' => 'Lượt xem',
                            'rating' => 'Đánh giá',
                        ]; ?>
                        <select class="" name="filter[sort]" form="searchform">
                            <?php foreach($sort_options as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php if ($_GET['filter']['sort'] === $value) echo 'selected'; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Ngôn ngữ -->
                <div class="list-movie-filter-item Form-Group">
                    <label class="AAIco-language <?php echo ($_GET['filter']['lang'] ? 'active' : ''); ?>">Ngôn ngữ</label>
                    <div class="">
                        <?php $languages = [
                            '' => 'Tất cả ngôn ngữ',
                            'Vietsub' => 'Vietsub',
                            'Thuyết Minh' => 'Thuyết Minh',
                            'Lồng Tiếng' => 'Lồng Tiếng',
                        ]; ?>
                        <select class="" name="filter[lang]" form="searchform">
                            <?php foreach($languages as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php if ($_GET['filter']['lang'] === $value) echo 'selected'; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" form="searchform" class="btn btn-red btn-filter-movie"><span>Lọc</span></button>
                <div class="clear"></div>

        </div>
    </div>
</form>