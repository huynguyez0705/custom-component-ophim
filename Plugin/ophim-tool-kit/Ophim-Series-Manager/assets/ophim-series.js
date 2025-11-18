/**
 * @format
 * @type {{ajaxurl:string}}
 */

var osm_ajax;

jQuery(function ($) {
  let selected = {},
    temp = {};

  $("#osm-search").on("input", function () {
    clearTimeout(window.st);
    let q = this.value.trim();
    if (q.length < 2) {
      $("#osm-suggestions").hide();
      temp = {};
      updateCount();
      return;
    }
    window.st = setTimeout(() => {
      $.post(
        osm_ajax.ajaxurl,
        { action: "ophim_search_film_pro", q },
        (res) => {
          if (!res.success || !res.data.length) {
            $("#osm-suggestions")
              .html('<div class="no-result">Không tìm thấy phim nào</div>')
              .show();
            return;
          }
          let html = '<div class="suggest-title">Kết quả tìm kiếm:</div>';
          res.data.forEach((f) => {
            let chk = temp[f.id] ? "checked" : "";
            let badge = f.in_series
              ? '<span class="badge-in-series">Đã trong series</span>'
              : "";
            html += `<label class="suggest-item ${
              f.in_series ? "in-series" : ""
            }">
                        <input type="checkbox" class="osm-check" data-id="${
                          f.id
                        }" data-title="${f.title}" ${chk}>
                        <span><strong>${f.title}</strong> <small>(ID: ${
              f.id
            })</small></span>
                        ${badge}
                    </label>`;
          });
          $("#osm-suggestions").html(html).show();
          updateCount();
        }
      );
    }, 400);
  });

  $(document).on("change", ".osm-check", function () {
    let id = this.dataset.id,
      title = this.dataset.title;
    if (this.checked && $(this).closest(".in-series").length) {
      if (
        !confirm(
          `⚠️ "${title}" đã thuộc một series khác.\nBạn vẫn muốn thêm vào series mới này chứ?`
        )
      ) {
        this.checked = false;
        return;
      }
    }
    this.checked ? (temp[id] = { id, title }) : delete temp[id];
    updateCount();
  });

  $("#osm-select-all").on("click", () =>
    $(".osm-check:not(:checked)").prop("checked", true).trigger("change")
  );

  function updateCount() {
    let c = Object.keys(temp).length;
    $("#osm-count").text(c);
    $("#osm-add-selected").toggle(c > 0);
  }

  $("#osm-add-selected").on("click", () => {
    Object.assign(selected, temp);
    temp = {};
    $("#osm-suggestions").hide();
    $("#osm-search").val("").focus();
    renderSelected();
    updateCount();
  });

  $(document).on("click", ".osm-remove", function () {
    delete selected[this.dataset.id];
    renderSelected();
  });

  function renderSelected() {
    let html = `<div class="selected-header"><h2>Danh sách chờ tạo series (${
      Object.keys(selected).length
    } phim)</h2></div><div class="film-tags-grid">`;
    if (!Object.keys(selected).length) {
      html += '<p class="text-muted text-center">Chưa chọn phim nào...</p>';
    } else {
      for (let id in selected) {
        html += `<div class="film-tag">
                    <span class="film-title">${selected[id].title}</span>
                    <span class="film-id">ID: ${id}</span>
                    <button type="button" class="remove-tag osm-remove" data-id="${id}" title="Xóa"><i class="fa-solid fa-xmark"></i></button>
                </div>`;
      }
    }
    html += `</div><div class="mt-4 text-center">
            <button id="osm-create-group" class="button button-primary button-large"><i class="fa-solid fa-plus-circle"></i> Tạo Series Từ Danh Sách Này</button>
        </div>`;
    $("#osm-selected-films").html(html);
  }

  $(document).on("click", "#osm-create-group", () => {
    if (Object.keys(selected).length < 2) return alert("Cần ít nhất 2 phim!");
    createGroup(Object.values(selected));
    selected = {};
    renderSelected();
  });

  let gid = 0;
  function createGroup(films) {
    gid++;
    let id = "group" + gid;
    films.sort((a, b) => {
      let pa = a.title.match(/Phần\s*(\d+)/i);
      let pb = b.title.match(/Phần\s*(\d+)/i);
      return (pa ? +pa[1] : 999) - (pb ? +pb[1] : 999);
    });
    let html = `<div class="osm-group"><h3><i class="fa-solid fa-film"></i> Series #${gid} – ${films.length} phần</h3><div class="osm-parts" id="${id}">`;
    films.forEach((f, i) => {
      let pn = f.title.match(/Phần\s*(\d+)/i);
      let dp = pn ? "Phần " + pn[1] : "Phần " + (i + 1);
      html += `<div class="osm-part" data-id="${f.id}">
                <span class="osm-drag"><i class="fa-solid fa-grip-vertical"></i></span>
                <div class="film-info"><strong>${f.title}</strong><small>ID: ${f.id}</small></div>
                <input type="text" value="${dp}" class="part-input">
                <button type="button" id ="btn-del" class="button button-delete remove-part"><i class="fa-solid fa-trash"></i> Xóa</button>
            </div>`;
    });
    html += `</div><div class="group-actions">
            <button type="button" class="button button-primary save-group"><i class="fa-solid fa-save"></i> Lưu Series Này</button>
            <button type="button" class="button button-secondary remove-group"><i class="fa-solid fa-trash-alt"></i> Xóa Nhóm</button>
        </div></div>`;
    $("#osm-groups").append(html);
    $("#" + id).sortable({
      handle: ".osm-drag",
      update: () =>
        $("#" + id + " .part-input").each(
          (i, e) => (e.value = "Phần " + (i + 1))
        ),
    });
  }

  $(document).on("click", ".save-group", function () {
    let ids = $(this)
      .closest(".osm-group")
      .find(".osm-part")
      .map((_, e) => $(e).data("id"))
      .get();
    if (ids.length < 2) return alert("Cần ít nhất 2 phim!");
    $.post(
      osm_ajax.ajaxurl,
      { action: "ophim_save_series_group", ids: ids.join(",") },
      (r) => {
        if (r.success) alert(`ĐÃ LƯU SERIES – ${ids.length} phần!`);
      }
    );
  });

  $(document).on("click", ".remove-group", (e) =>
    $(e.target).closest(".osm-group").remove()
  );
  $(document).on("click", ".remove-part", (e) =>
    $(e.target).closest(".osm-part").remove()
  );
});
