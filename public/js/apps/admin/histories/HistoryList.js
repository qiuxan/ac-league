var HistoryList = {
};

HistoryList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: HistoryList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            resizable: true,
            selectable: 'multiple',
            columns: [
                { field: 'company_en', title: 'Company' },
                { field: 'full_code', title: 'Serial #', width: 129},
                { field: 'batch_code', title: 'Batch #' },
                { field: 'name', title: 'Product' },
                { field: 'created_at', title: 'Time' },
                { field: 'location', title: 'Location' },
                { field: 'lat', title: 'Latitude' },
                { field: 'lng', title: 'Longitude' },
                { field: 'language', title: 'Language' },
                { field: 'status', title: 'Status' },
                { field: 'reseller', title: 'Reseller' }],
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            excel: {
                fileName: "Report.xlsx",
                allPages: true
            }
        });
    $( '#exportButton' ).click( HistoryList.exportToExcel );
}

HistoryList.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/admin/getHistories",
                    dataType: 'json',
                    data:
                    {
                        filters: HistoryList.getFilters()
                    }
                }
            },
            schema:
            {
                model: HistoryList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

HistoryList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}


HistoryList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#search_filter' ).val();
        },
        from_date: function()
        {
            return $( '#from_date_filter' ).val();
        },
        to_date: function()
        {
            return $( '#to_date_filter' ).val();
        },
        member_id: function(){
            return $( '#member_id_filter' ).val();
        },
        batch_id: function()
        {
            return $( '#batch_id_filter' ).val();
        },
        product_id: function()
        {
            return $( '#product_id_filter' ).val();
        },
        location_id: function()
        {
            return $( '#location_id_filter' ).val();
        },
        reseller_id: function()
        {
            return $( '#reseller_id_filter' ).val();
        },
        language: function()
        {
            return $( '#language_filter' ).val();
        },
        status: function()
        {
            return $( '#status_filter' ).val();
        }
    }

    return filters;
}

HistoryList.filterGrid = function()
{
    HistoryList.getGrid().dataSource.filter({});
}

HistoryList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

HistoryList.addListeners = function()
{
    $( '#search_filter' ).keyup( HistoryList.filterGrid );
    $( '#search_filter' ).click( HistoryList.filterGrid );
    $( '#from_date_filter' ).change( HistoryList.filterGrid );
    $( '#to_date_filter' ).change( HistoryList.filterGrid );
    $( '#member_id_filter' ).change( HistoryList.filterGrid );
    $( '#batch_id_filter' ).change( HistoryList.filterGrid );
    $( '#product_id_filter' ).change( HistoryList.filterGrid );
    $( '#location_id_filter' ).change( HistoryList.filterGrid );
    $( '#reseller_id_filter' ).change( HistoryList.filterGrid );
    $( '#language_filter' ).change( HistoryList.filterGrid );
    $( '#status_filter' ).change( HistoryList.filterGrid );
}

HistoryList.addKendoElements = function()
{
    $( "#from_date_filter" ).kendoDatePicker({ format: 'dd/MM/yyyy' });
    $( "#to_date_filter" ).kendoDatePicker({ format: 'dd/MM/yyyy' });
    $( "#member_id_filter" ).kendoDropDownList();
    $( "#batch_id_filter" ).kendoDropDownList();
    $( "#product_id_filter" ).kendoDropDownList();
    $( "#location_id_filter" ).kendoDropDownList();
    $( "#reseller_id_filter" ).kendoDropDownList();
    $( "#language_filter" ).kendoDropDownList();
    $( "#status_filter" ).kendoDropDownList();
}

HistoryList.exportToExcel = function()
{
    HistoryList.getGrid().saveAsExcel();
}


$( document ).ready( function()
{
    HistoryList.initGrid();
    HistoryList.addKendoElements();
    HistoryList.addListeners();
});