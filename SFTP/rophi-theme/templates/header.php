<style>
	#result {
		margin-top: 10px;
		background-color: #333333;
		list-style-type: none;
		width: 400px;
		position: absolute;
		top: 32px;
		z-index: 100;
		padding-left: 0
	}

	#result a {
		color: #fff
	}

	.column {
		float: left;
		padding: 5px
	}

	.lefts {
		text-align: center;
		width: 20%
	}

	.rights {
		width: 80%
	}

	.rowsearch:after {
		content: "";
		display: table;
		clear: both
	}

	.rowsearch:hover {
		background-color: #1f1e1e
	}

	#result .rowsearch {
		display: flex;
		justify-content: center;
		align-items: center
	}

	@media all and (min-width:800px) {
		.deskContent {
			display: block
		}

		.phoneContent {
			display: none
		}
	}

	@media all and (max-width:799px) {
		.deskContent {
			display: none
		}

		.phoneContent {
			display: block
		}
	}
</style>
<header id="header" class="fly">
	<div class="header-elements">
		<div class="for-mobile menu-toggle">
			<div class="icon-menu">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
		<div class="for-mobile search-toggle">
			<div class="icon-search">
				<span></span>
				<span></span>
				<i class="fa-solid fa-magnifying-glass"></i>
			</div>
		</div>
	</div>
</header>
<script>
	var headerEle = document.querySelector(".header-elements");
	console.log(headerEle);
	var btnMenu = document.querySelector(".menu-toggle");
	var btnSearch = document.querySelector(".search-toggle");
	var elGroup = document.querySelector(".el-group");
	var searchBox = document.querySelector("#search");

	if (btnMenu) {
		btnMenu.addEventListener("click", function() {
			btnMenu.classList.toggle("toggled");
			elGroup.classList.toggle("toggled");
		});
	}

	if (btnSearch) {
		btnSearch.addEventListener("click", function() {
			btnSearch.classList.toggle("toggled");
			headerEle.classList.toggle("simple");
			searchBox.classList.toggle("toggled");
		});
	}
</script>








<!-- <div id="header" class="deskContent">
	<div class="container">
		<div class="top">
			<div class="logo" style="box-shadow: 0px 1px 3px rgba(0,0,0,0.2);">
				<a href="/" title="">
					<?php op_the_logo() ?>
				</a>
			</div>
			<div class="right-header">
				<div class="search-container relative">
					<form id="form-filter" method="GET" action="/">
						<input type="text" id="keyword" name="s" autocomplete="off" onkeyup="fetch()" placeholder="Nhập tên phim bạn muốn tìm kiếm..." value="<?php echo "$s"; ?>" />
						<i class="fa fa-search" onclick="$('#form_search').submit();"></i>
					</form>
					<div class="" id="result"></div>
				</div>
				<div id="box-user-menu" class="actions-user">
					<ul>
						<li>
							<i class="fa fa-sign-in"></i>
							<a onclick="alert('Chức năng này đang cập nhật');return false;">Đăng nhập</a>
						</li>
						<li>
							<i class="fa fa-users"></i>
							<a onclick="alert('Chức năng này đang cập nhật');return false;">Đăng ký</a>
						</li>
						<li>
							<i class="fa fa-bookmark-o"></i>
							<a onclick="alert('Chức năng này đang cập nhật');return false;">Bookmark</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="main-menu">
		<ul class="container">
			<?php
			$menu_items = wp_get_menu_array('primary-menu');
			global $wp;
			$current_url = home_url($wp->request);


			foreach ($menu_items as $key => $item): ?>
				<?php if (count($item['children'])) {
					// Kiểm tra nếu menu cha hoặc con active
					$is_active = ($item['url'] === $current_url);
					foreach ($item['children'] as $child) {
						if ($child['url'] === $current_url) {
							$is_active = true;
							break;
						}
					}
				?>
					<li class="menu-item <?= $is_active ? 'active' : '' ?>">
						<a title="<?= $item['title'] ?>" href="<?= $item['url'] ?>">
							<?= $item['title'] ?>
						</a>
						<ul class="sub-menu span-4 absolute">
							<?php foreach ($item['children'] as $k => $child): ?>
								<li class="sub-menu-item <?= ($child['url'] === $current_url) ? 'active' : '' ?>">
									<a title="<?= $child['title'] ?>" href="<?= $child['url'] ?>"><?= $child['title'] ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php } else { ?>
					<li class="menu-item <?= ($item['url'] === $current_url) ? 'active' : '' ?>">
						<a title="<?= $item['title'] ?>" href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
					</li>
				<?php } ?>
			<?php endforeach; ?>
		</ul>
	</div>

</div> -->
<!-- <div id="header" class="phoneContent">
	<div class="container">
		<div class="btn-humber">
			<p></p>
			<p></p>
			<p></p>
		</div>
		<a href="/" title="" class="logo">
			<?php op_the_logo('max-width:240px') ?>
		</a>
		<i class="fa fa-search mobile"></i>
		<form id="mform_search" method="GET" action="/">
			<input type="text" id="mkeyword" name="s" autocomplete="off" placeholder="Nhập tên phim bạn muốn tìm kiếm..." value="<?php echo "$s"; ?>">
			<i class="fa fa-arrow-circle-right" onclick="$('#mform_search').submit();"></i>
			<div style="display: block;" id="msuggestions" class="top-search-box"></div>
		</form>
		<script>
			$(document).ready(function() {
				$(".btn-humber").click(function() {
					var $menu = $(".main-menu");
					var overlay = '<div id="overlay_menu" onclick="$(\'.btn-humber\').click()"></div>';
					$this = $(this);
					var hw = $(window).height();
					if ($menu.hasClass('expanded')) {
						$menu.removeClass('expanded');
						$this.removeClass('active');
						$('#overlay_menu').remove();
					} else {
						$('.main-menu').height(hw);
						$menu.addClass('expanded');
						$this.addClass('active');
						$('body').append(overlay);
						setTimeout(function() {
							$('#overlay_menu').addClass('slide');
						}, 300)

					}
				});


				$(".menu-item>a").click(function() {
					var $this = $(this);
					var $sub = $this.next();

					if (!$sub.hasClass('sub-menu')) {
						var link = $this.attr('href');
						window.location.href = link;
					} else {
						if ($sub.hasClass('expanded')) {
							$sub.removeClass('expanded');
							$this.removeClass('active');

						} else {
							$('.sub-menu').removeClass('expanded');
							$sub.addClass('expanded');
							$this.addClass('active');
						}
						return false;
					}
				});

				//                 $(window).resize(function() {
				//                     hw = $(window).height();
				//                     $('.main-menu').height(hw);
				//                 });


				$(".fa-search.mobile").click(function() {
					var $this = $(this);
					var $formsearch = $("#mform_search");
					var overlay = '<div id="overlay_search" onclick="$(\'.fa-search.mobile\').click()"></div>';
					if ($formsearch.hasClass('expanded')) {
						$formsearch.removeClass('expanded');
						$('#overlay_search').remove();
					} else {
						$formsearch.addClass('expanded');
						$('body').append(overlay);
						$("#mkeyword").focus();
					}
				});
			})
		</script>

	</div>
	<div class="main-menu">
		<ul class="container">
			<?php
			global $wp;
			$current_url = home_url($wp->request);

			$menu_items = wp_get_menu_array('primary-menu');
			foreach ($menu_items as $key => $item): ?>
				<?php if (count($item['children'])) {
					// Kiểm tra active cho menu cha
					$is_active = ($item['url'] === $current_url);
					foreach ($item['children'] as $child) {
						if ($child['url'] === $current_url) {
							$is_active = true;
							break;
						}
					}
				?>
					<li class="menu-item <?= $is_active ? 'active' : '' ?>">
						<a title="<?= $item['title'] ?>" href="<?= $item['url'] ?>">
							<?= $item['title'] ?>
						</a>
						<ul class="sub-menu span absolute">
							<?php foreach ($item['children'] as $child): ?>
								<li class="sub-menu-item <?= ($child['url'] === $current_url) ? 'active' : '' ?>">
									<a title="<?= $child['title'] ?>" href="<?= $child['url'] ?>">
										<?= $child['title'] ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php } else { ?>
					<li class="menu-item <?= ($item['url'] === $current_url) ? 'active' : '' ?>">
						<a title="<?= $item['title'] ?>" href="<?= $item['url'] ?>">
							<?= $item['title'] ?>
						</a>
					</li>
				<?php } ?>
			<?php endforeach; ?>
		</ul>
	</div>

</div> -->