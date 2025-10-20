jQuery(function ($) {
	if (typeof OPhimFilter === 'undefined') return

	const $form = $('#ophim-filter-form')
	const $container = $('#meta-rows')
	const $results = $('#ophim-results-container')
	const $spinner = $form.find('.spinner')
	const $pagedInput = $('#paged-input')

	// -----------------------------------
	// Core AJAX Logic (Phân trang và Lọc)
	// -----------------------------------
	function runFilter(page = 1) {
		// Đặt lại số trang hiện tại
		$pagedInput.val(page)

		// Thu thập dữ liệu form và thêm action AJAX
		const data = $form.serializeArray()
		data.push({ name: 'action', value: OPhimFilter.ajaxAction })

		$form.addClass('loading')
		$spinner.addClass('is-active')
		$results.html(' ') // Xóa kết quả cũ và giữ chỗ

		// Lưu trạng thái URL (không bắt buộc, nhưng giúp người dùng copy/share link)
		const newUrlParams = $form.serialize()
		history.replaceState(null, null, '?' + newUrlParams + '&page=' + OPhimFilter.ajaxAction)

		$.post(OPhimFilter.ajaxurl, data)
			.done(function (response) {
				if (response.success) {
					$results.html(response.data.html)
				} else {
					$results.html('<p style="color: red;">Lỗi server: ' + (response.data.message || 'Không thể lọc. Vui lòng kiểm tra logs.') + '</p>')
				}
			})
			.fail(function (xhr, status, error) {
				$results.html('<p style="color: red;">Lỗi mạng/AJAX: Không thể kết nối server.</p>')
			})
			.always(function () {
				$form.removeClass('loading')
				$spinner.removeClass('is-active')
			})
	}

	// -----------------------------------
	// Binding Events
	// -----------------------------------

	// 1. Form Submit (Luôn bắt đầu từ trang 1 khi thay đổi bộ lọc)
	$form.on('submit', function (e) {
		e.preventDefault()
		runFilter(1)
	})

	// 2. Thay đổi các trường chính (Taxonomy, Limit)
	$('.ophim-select, .ophim-input-small').on('change', function () {
		runFilter(1)
	})

	// 3. Thay đổi Meta Key/Value
	$container.on('change', 'input[name="meta_key[]"], input[name="meta_val[]"]', function () {
		runFilter(1)
	})

	// 4. Phân trang (Xử lý click vào link phân trang)
	$results.on('click', '.ophim-page-link', function (e) {
		e.preventDefault()
		const pageLink = $(this).attr('href')

		if (pageLink && pageLink !== '#') {
			// Trích xuất số trang từ href (ví dụ: ?paged=5)
			const match = pageLink.match(/paged=(\d+)/)
			if (match && match[1]) {
				const newPage = parseInt(match[1], 10)
				runFilter(newPage)
			}
		}
	})

	// 5. Thêm hàng điều kiện
	$('#add-row').on('click', function () {
		const $tpl = $container.find('.meta-row').first()
		const $clone = $tpl.clone()
		$clone.find('input[name="meta_key[]"]').val('').trigger('change')
		$clone.find('input[name="meta_val[]"]').val('').trigger('change')
		$clone.insertAfter($container.find('.meta-row').last())
		$clone.find('input[name="meta_key[]"]').focus()
	})

	// 6. Xóa dòng
	$container.on('click', '.remove-row', function () {
		const $rows = $container.find('.meta-row')
		if ($rows.length > 1) {
			$(this).closest('.meta-row').remove()
			runFilter(1) // Lọc lại sau khi xóa
		}
	})

	// Lọc lần đầu khi trang load
	// Lấy giá trị trang từ URL nếu có, nếu không thì dùng 1
	const urlParams = new URLSearchParams(window.location.search)
	const initialPage = parseInt(urlParams.get('paged')) || 1
	runFilter(initialPage)
})
