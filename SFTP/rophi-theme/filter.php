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


<form id="form-filter" class="form-inline" method="GET" action="/">
  <div class="box-filter">
    <!-- Định Dạng -->
    <div class=" single-filter">
      <select name="filter[categories]" form="form-filter" class="form-select">
        <option value="" <?= $f['categories'] == '' ? 'class="active"' : '' ?>>Định Dạng</option>
        <?php foreach ($categories as $category): ?>
        <option value="<?= $category->name ?>" <?= $category->name == $f['categories'] ? 'selected class="active"' : '' ?>><?= $category->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Quốc gia -->
    <div class="single-filter">
      <select name="filter[regions]" form="form-filter" class="form-select">
        <option value="" <?= $f['regions'] == '' ? 'class="active"' : '' ?>>Quốc gia</option>
        <?php foreach ($regions as $region): ?>
        <option value="<?= $region->name ?>" <?= $region->name == $f['regions'] ? 'selected class="active"' : '' ?>><?= $region->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Thể loại -->
    <div class="single-filter">
      <select name="filter[genres]" form="form-filter" class="form-select">
        <option value="" <?= $f['genres'] == '' ? 'class="active"' : '' ?>>Thể Loại</option>
        <?php foreach ($genres as $genre): ?>
        <option value="<?= $genre->name ?>" <?= $genre->name == $f['genres'] ? 'selected class="active"' : '' ?>><?= $genre->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Năm phát hành -->
    <div class="single-filter">
      <select name="filter[years]" form="form-filter" class="form-select">
        <option value="" <?= $f['years'] == '' ? 'class="active"' : '' ?>>Năm</option>
        <?php foreach ($filtered_years as $year): ?>
        <option value="<?= $year->name ?>" <?= $year->name == $f['years'] ? 'selected class="active"' : '' ?>><?= $year->name ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Trạng thái -->
    <div class="single-filter">
      <select name="filter[status]" form="form-filter" class="form-select">
        <option value="" <?= $f['status'] == '' ? 'class="active"' : '' ?>>Trạng Thái</option>
        <?php foreach ($statuses as $slug => $label): ?>
        <option value="<?= $slug ?>" <?= $f['status'] === $slug ? 'selected class="active"' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Sắp xếp -->
    <div class="single-filter">
      <select name="filter[sort]" form="form-filter" class="form-select" class="form-select">
        <option value="" <?= $f['sort'] == '' ? 'selected class="active"' : '' ?>>Sắp Xếp</option>
        <?php foreach ($sort_options as $value => $label): if ($value === '') continue; ?>
        <option value="<?= $value ?>" <?= $f['sort'] === $value ? 'selected class="active"' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Ngôn ngữ -->
    <div class="single-filter">
      <select name="filter[lang]" form="form-filter" class="form-select">
        <option value="" <?= $f['lang'] == '' ? 'class="active"' : '' ?>>Ngôn Ngữ</option>
        <?php foreach ($languages as $value => $label): if ($value === '') continue; ?>
        <option value="<?= $value ?>" <?= $f['lang'] === $value ? 'selected class="active"' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="single-filter btn-submit">
      <button type="submit" form="form-filter" class="btn-filter"><span>Lọc</span></button>
      <button type="button" class="btn-reset" onclick="window.location.href=location.pathname + '?s='"><span>Reset</span></button>
    </div>

  </div>
</form>
<style>
:root {
  --color: #da966e;
  --color-hover: #b37a58;
  --color-focus: #e46d2b;
  --bs-body-font-system-ui: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', 'Noto Sans',
    'Liberation Sans', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
  --bs-body-font-nunito: 'Nunito';
  --text-base: #abb7c4;
}

.box-filter {
  padding: .5rem 0 1.5rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.form-select {
  color: #fff;
  font-size: 14px;
  border-color: #383838;
  border-radius: 0;
  padding: .5rem 2.25rem .5rem .75rem;
  background-color: #212529;
  display: block;
  width: 100%;
  font-weight: 400;
  line-height: 1.5;
  appearance: none;
  transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

.form-select:focus,
.form-select:focus-visible,
.form-select:active {
  border-color: var(--color) !important;
  box-shadow: 0 0 0 .25rem rgba(196, 133, 96, .1);
}


.box-filter .btn-submit {
  display: flex;
  gap: 1rem;
  line-height: 39px;
}

.box-filter .btn-submit button {
  background-color: var(--color);
  padding: 0 16px;
  border: none;
  color: #fff;
  border-radius: 0;
  cursor: pointer;
  font-size: 12px;
  width: 100%;
}

.box-filter .single-filter.btn-submit button.btn-reset {
  background-color: #666666;
}
</style>