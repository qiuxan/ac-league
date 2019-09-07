var ResponseList = {
};

ResponseList.initGrid = function()
{
    $("#responseContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "600px",
        height: "400px",
        title: "Response Detail",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#responseGrid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#responsesToolbarTemplate' ).html().replace(/#/g, "\\#") ),
            dataSource: ResponseList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'full_code', title: 'Serial #' },
                { field: 'batch_code', title: 'Batch #' },
                { field: 'name', title: 'Product' },
                { field: 'created_at', title: 'Time' },
                { field: 'location', title: 'Location' },
                { field: 'lat', title: 'Latitude' },
                { field: 'lng', title: 'Longitude' },
                { field: 'language', title: 'Language' },
                { field: 'production_partner', title: 'Production Partner' }],
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            excel: {
                fileName: "ResponseReport.xlsx",
                allPages: true
            }
        });
    $( '#exportButton' ).click( ResponseList.exportToExcel );
}

ResponseList.getDataSource = function()
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
                    url: "/member/getResponses",
                    dataType: 'json',
                    data:
                    {
                        filters: ResponseList.getFilters()
                    }
                }
            },
            schema:
            {
                model: ResponseList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

ResponseList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

ResponseList.getFilters = function()
{
    var filters =
    {
        option_filters: function()
        {
            var values = [];
            $('select.option_filter').each(function() {
                values.push(this.value);
            });
            return values;
        },
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
        production_partner_id: function()
        {
            return $( '#production_partner_id_filter' ).val();
        },
        language: function()
        {
            return $( '#language_filter' ).val();
        }
    }

    return filters;
}

ResponseList.filterGrid = function()
{
    ResponseList.getGrid().dataSource.filter({});
    QuestionAnalysis.filterAllGrids();
}

ResponseList.getGrid = function()
{
    return $( '#responseGrid' ).data( 'kendoGrid' );
}

ResponseList.addListeners = function()
{
    $( '#search_filter' ).keyup( ResponseList.filterGrid );
    $( '#search_filter' ).click( ResponseList.filterGrid );
    $( '#from_date_filter' ).change( ResponseList.filterGrid );
    $( '#to_date_filter' ).change( ResponseList.filterGrid );
    $( '#batch_id_filter' ).change( ResponseList.filterGrid );
    $( '#product_id_filter' ).change( ResponseList.filterGrid );
    $( '#location_id_filter' ).change( ResponseList.filterGrid );
    $( '#production_partner_id_filter' ).change( ResponseList.filterGrid );
    $( '#language_filter' ).change( ResponseList.filterGrid );
    $( '#responseGrid table' ).dblclick( ResponseList.showResponse );
}

ResponseList.addKendoElements = function()
{
    $( "#from_date_filter" ).kendoDatePicker({ format: 'dd/MM/yyyy' });
    $( "#to_date_filter" ).kendoDatePicker({ format: 'dd/MM/yyyy' });
    $( "#batch_id_filter" ).kendoDropDownList();
    $( "#product_id_filter" ).kendoDropDownList();
    $( "#location_id_filter" ).kendoDropDownList();
    $( "#production_partner_id_filter" ).kendoDropDownList();
    $( "#language_filter" ).kendoDropDownList();
}

ResponseList.exportToExcel = function()
{
    ResponseList.getGrid().saveAsExcel();
}

ResponseList.showResponse = function()
{
    var uid = ( ResponseList.getGrid().select().data() ) ? ResponseList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = ResponseList.getGrid().dataSource.getByUid( uid );
        _response_id = selected.id;

        $("#responseContainer").data("kendoWindow").center();
        $("#responseContainer").data("kendoWindow").open();
        console.log(_response_id);
        $("#responseContainer").load( "/member/getResponseAnswers/" + _response_id);
    }
}

$( document ).ready( function()
{
    ResponseList.initGrid();
    ResponseList.addKendoElements();
    ResponseList.addListeners();
});