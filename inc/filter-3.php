<?php
$_GET['filter'] = array_merge([
  'categories' => '',
  'genres'     => '',
  'regions'    => '',
  'years'      => '',
  'status'     => '',
  'sort'       => '',
  'lang'       => '',
], $_GET['filter'] ?? []);

// Lấy danh mục hiện tại
$term = get_queried_object();

$taxonomy_map = ['ophim_categories' => 'categories', 'ophim_genres' => 'genres', 'ophim_years' => 'years', 'ophim_regions' => 'regions'];
if ($term instanceof WP_Term && isset($taxonomy_map[$term->taxonomy]) && empty($_GET['filter'][$taxonomy_map[$term->taxonomy]])) {
  $_GET['filter'][$taxonomy_map[$term->taxonomy]] = $term->name;
}
$regions    = get_terms(['taxonomy' => 'ophim_regions',    'hide_empty' => false]);
$categories = get_terms(['taxonomy' => 'ophim_categories', 'hide_empty' => false]);
$genres     = get_terms(['taxonomy' => 'ophim_genres',     'hide_empty' => false]);
$years      = get_terms(['taxonomy' => 'ophim_years',      'hide_empty' => false, 'orderby' => 'name', 'order' => 'DESC']);
$filtered_years = array_filter($years, fn($y) => is_numeric($y->name) && $y->name >= 1995);
// Các map tùy chọn tĩnh
$statuses = ['trailer' => 'Sắp chiếu', 'ongoing' => 'Đang chiếu', 'completed' => 'Hoàn thành'];
$sort_options = ['' => 'Mặc định', 'newest' => 'Mới nhất', 'updated' => 'Mới cập nhật', 'views' => 'Lượt xem', 'rating' => 'Đánh giá'];
$languages = ['' => 'Tất cả ngôn ngữ', 'Vietsub' => 'Vietsub', 'Thuyết Minh' => 'Thuyết Minh', 'Lồng Tiếng' => 'Lồng Tiếng'];
$f = $_GET['filter']; // viết ngắn cho tiện
?>


<form id="searchform" class="form-inline" method="GET" action="/">
  <div class="box-filter" style="margin-bottom: 50px;">
    <!-- Định Dạng -->
    <div class="single-filter">
      <select name="filter[categories]" form="searchform" class="searchForm">
        <option value="" <?= $f['categories'] == '' ? 'class="active"' : '' ?>>Định Dạng</option>
        <?php foreach ($categories as $category): ?>
        <option value="<?= $category->name ?>" <?= $category->name == $f['categories'] ? 'selected class="active"' : '' ?>><?= $category->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Quốc gia -->
    <div class="single-filter">
      <select name="filter[regions]" form="searchform" class="searchForm">
        <option value="" <?= $f['regions'] == '' ? 'class="active"' : '' ?>>Quốc gia</option>
        <?php foreach ($regions as $region): ?>
        <option value="<?= $region->name ?>" <?= $region->name == $f['regions'] ? 'selected class="active"' : '' ?>><?= $region->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Thể loại -->
    <div class="single-filter">
      <select name="filter[genres]" form="searchform" class="searchForm">
        <option value="" <?= $f['genres'] == '' ? 'class="active"' : '' ?>>Thể Loại</option>
        <?php foreach ($genres as $genre): ?>
        <option value="<?= $genre->name ?>" <?= $genre->name == $f['genres'] ? 'selected class="active"' : '' ?>><?= $genre->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Năm phát hành -->
    <div class="single-filter">
      <select name="filter[years]" form="searchform" class="searchForm">
        <option value="" <?= $f['years'] == '' ? 'class="active"' : '' ?>>Năm</option>
        <?php foreach ($filtered_years as $year): ?>
        <option value="<?= $year->name ?>" <?= $year->name == $f['years'] ? 'selected class="active"' : '' ?>><?= $year->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Trạng thái -->
    <div class="single-filter">
      <select name="filter[status]" form="searchform" class="searchForm">
        <option value="" <?= $f['status'] == '' ? 'class="active"' : '' ?>>Trạng Thái</option>
        <?php foreach ($statuses as $slug => $label): ?>
        <option value="<?= $slug ?>" <?= $f['status'] === $slug ? 'selected class="active"' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Sắp xếp -->
    <div class="single-filter">
      <select name="filter[sort]" form="searchform" class="searchForm" class="searchForm">
        <option value="" <?= $f['sort'] == '' ? 'selected class="active"' : '' ?>>Sắp Xếp</option>
        <?php foreach ($sort_options as $value => $label): if ($value === '') continue; ?>
        <option value="<?= $value ?>" <?= $f['sort'] === $value ? 'selected class="active"' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Ngôn ngữ -->
    <div class="single-filter">
      <select name="filter[lang]" form="searchform" class="searchForm">
        <option value="" <?= $f['lang'] == '' ? 'class="active"' : '' ?>>Ngôn Ngữ</option>
        <?php foreach ($languages as $value => $label): if ($value === '') continue; ?>
        <option value="<?= $value ?>" <?= $f['lang'] === $value ? 'selected class="active"' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="single-filter btn-submit">
      <button type="submit" form="searchform" class="btn-filter"><span>Lọc</span></button>
      <button type="button" class="btn-reset" onclick="window.location.href=location.pathname + '?s='"><span>Reset</span></button>
    </div>

  </div>
</form>