/* WordPress TinyMCE: insert an editable specification table without remote dependencies. */
(function () {
  'use strict';
  tinymce.PluginManager.add('vesora_table', function (editor) {
    editor.addButton('vesora_table', {
      text: 'Chèn bảng', icon: false,
      onclick: function () {
        editor.windowManager.open({
          title: 'Chèn bảng thông số',
          body: [
            { type: 'textbox', name: 'rows', label: 'Số hàng nội dung (1–30)', value: '4' },
            { type: 'textbox', name: 'columns', label: 'Số cột (1–8)', value: '3' }
          ],
          onsubmit: function (event) {
            var rows = Math.max(1, Math.min(30, parseInt(event.data.rows, 10) || 4));
            var columns = Math.max(1, Math.min(8, parseInt(event.data.columns, 10) || 3));
            var html = '<table class="vesora-spec-table"><thead><tr>';
            for (var c = 0; c < columns; c++) html += '<th scope="col">Tiêu đề ' + (c + 1) + '</th>';
            html += '</tr></thead><tbody>';
            for (var r = 0; r < rows; r++) {
              html += '<tr>';
              for (var j = 0; j < columns; j++) html += '<td>&nbsp;</td>';
              html += '</tr>';
            }
            editor.insertContent(html + '</tbody></table><p><br></p>');
          }
        });
      }
    });
  });
})();
