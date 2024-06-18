var DatatableBasic = function() {

  // Basic Datatable examples
  var _componentDatatableBasic = function() {
    if (!$().DataTable) {
      console.warn('Warning - datatables.min.js is not loaded.');
      return;
    }

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
      autoWidth: false,
      columnDefs: [{ 
        orderable: false,
        width: 100,
        targets: [ 5 ]
      }],
      dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
      language: {
        search: '<span>Filter:</span> _INPUT_',
        searchPlaceholder: 'Type to filter...',
        lengthMenu: '<span>Show:</span> _MENU_',
        paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
      }
    });

    $('.datatable-basic').DataTable({
      "order": [[ 2, "asc" ]],
      "columns":[
        { "sortable": true },
        { "sortable": true },
        { "sortable": true },
        { "sortable": true },
        { "sortable": true },
        { "sortable": true },
      ]
    });

    // Alternative pagination
    $('.datatable-pagination').DataTable({
      pagingType: "simple",
      language: {
        paginate: {'next': $('html').attr('dir') == 'rtl' ? 'Next &larr;' : 'Next &rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr; Prev' : '&larr; Prev'}
      }
    });

  };

  // Select2 for length menu styling
  var _componentSelect2 = function() {
    if (!$().select2) {
      console.warn('Warning - select2.min.js is not loaded.');
      return;
    }

    // Initialize
    $('.dataTables_length select').select2({
      minimumResultsForSearch: Infinity,
      dropdownAutoWidth: true,
      width: 'auto'
    });
  };


  //
  // Return objects assigned to module
  //

  return {
    init: function() {
      _componentDatatableBasic();
      _componentSelect2();
    }
  }
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
  DatatableBasic.init();
});