var HistoryList = {
};

var markerCluster;
var map;
var markers;
var normalCharts = ['batch', 'product', 'location', 'reseller', 'language', 'status']
HistoryList.initMap = function() {
    $("#mapContainer").height($( window ).height() - 250);
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 3,
        center: {lat: 5, lng: 124}
    });

    var locations = HistoryList.getLocations();
    markers = [];
    
    for(i=0;i<locations.length;i++){
        var location = locations[i];
        if(location.lat != null ){
            location.lat = parseFloat(location.lat);
            location.lng = parseFloat(location.lng);
            var marker = new google.maps.Marker({
                position: location
            });
            markers.push(marker);
        }        
    }

    // Add a marker clusterer to manage the markers.
    markerCluster = new MarkerClusterer(map, markers,
        {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
}

HistoryList.getLocations = function(){
    var response;
    $.ajax({
        type: "GET",
        url: "/member/getHistoriesCoordinates",
        async: false,
        data:
        {
            filters: HistoryList.getFilters()            
        },
        success: function(result){
            response = result;
        }
    });
    return response;
}

HistoryList.clearMarkers = function(){
    markerCluster.clearMarkers();
}

HistoryList.addMarkders = function(){
    var locations = HistoryList.getLocations();
    markers = [];
    
    for(i=0;i<locations.length;i++){
        var location = locations[i];
        if(location.lat != null ){
            location.lat = parseFloat(location.lat);
            location.lng = parseFloat(location.lng);
            var marker = new google.maps.Marker({
                position: location
            });
            markers.push(marker);
        }        
    }

    // Add a marker clusterer to manage the markers.
    markerCluster = new MarkerClusterer(map, markers,
        {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});    
}

HistoryList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: HistoryList.getDataSource(),
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
                    url: "/member/getHistories",
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

HistoryList.filterMap = function()
{
    HistoryList.clearMarkers();
    HistoryList.addMarkders();
    map.setCenter({lat: 5, lng: 124});
    map.setZoom(3);    
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
    $( '#batch_id_filter' ).change( HistoryList.filterGrid );
    $( '#product_id_filter' ).change( HistoryList.filterGrid );
    $( '#location_id_filter' ).change( HistoryList.filterGrid );
    $( '#reseller_id_filter' ).change( HistoryList.filterGrid );
    $( '#language_filter' ).change( HistoryList.filterGrid );
    $( '#status_filter' ).change( HistoryList.filterGrid );
    $( '#refresh_map' ).click( HistoryList.filterMap );
    $( '#refresh_chart' ).click( HistoryList.filterChart );
}

HistoryList.addKendoElements = function()
{
    $( '#tabs' ).kendoTabStrip();    
    $( "#from_date_filter" ).kendoDatePicker({ format: 'dd/MM/yyyy' });
    $( "#to_date_filter" ).kendoDatePicker({ format: 'dd/MM/yyyy' });
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

HistoryList.initChart = function()
{
    HistoryList.createChartScanByDate();
    HistoryList.createChartScanByMonth();   
    HistoryList.createChartScanByNormal();
}

HistoryList.getScanTimes = function( by ){
    var response;
    $.ajax({
        url: "/member/getScanTimes",
        async: false,
        data:{
            filters: HistoryList.getFilters(),
            by: by
        },        
        success: function(result){
            response = result;
        }
    });
    return response;
}

HistoryList.createChartScanByDate = function(){
    // scans by datetime chart
    var scan_times_by_date = HistoryList.getScanTimes('date');

    var data = []; 
    var dataSeries = { type: "column", color: "#6699FF" };
    var dataPoints = [];
    var minutes = 1000 * 60;
    var hours = minutes * 60;
    var days = hours * 24;
    var years = days * 365;

    for (var i = 0; i< scan_times_by_date.length; i++) {
        dateTime = new Date(scan_times_by_date[i].date);

        // padding 0 for missing dates
        if ((i-1)>0) {
            // since 1970
            thisDateTime = Date.parse(scan_times_by_date[i].date);
            thisDateTime = Math.round(thisDateTime / days);
            // since 1970
            lastDateTime = Date.parse(scan_times_by_date[i-1].date);
            lastDateTime = Math.round(lastDateTime / days);

            diff = thisDateTime - lastDateTime;
            if(diff>1){
                firstDate = new Date(scan_times_by_date[i-1].date);
                for(j=1;j<diff;j++){
                    clone_firstDate = new Date(firstDate.getTime());
                    clone_firstDate.setDate(clone_firstDate.getDate() + 1);
                    dataPoints.push({
                        x: clone_firstDate,
                        y: 0
                    });
                    firstDate = clone_firstDate; 
                }
            }
        }

        dataPoints.push({
            x: dateTime,
            y: scan_times_by_date[i].scan_times
        });
    }

    dataSeries.dataPoints = dataPoints;
    data.push(dataSeries); 

    var chart = new CanvasJS.Chart("chartContainer_date",
        {
            animationEnabled: false, // change to true		            
            width:400,
            height: 400,

            theme: "light2",
            zoomEnabled: true,
            title: {
                text: "User Scan History by Date",
                fontSize:20,
                fontFamily:"tahoma",
                padding:15                
            },
            axisX: {
                valueFormatString: "DD-MMM-YY"
            },
            axisY: {
                includeZero: false
            },
            data: data,  // random generator below

        });

    chart.render();
    
    // end of scan by datetime chart      
}

HistoryList.createChartScanByMonth = function(){
    var data = []; 
    var dataSeries = { type: "column", color: "#6699FF" };
    var dataPoints = [];    
    var scan_times_by_month = HistoryList.getScanTimes('month');
    for (var i = 0; i< scan_times_by_month.length; i++) {
        dateTime = new Date(scan_times_by_month[i].year, scan_times_by_month[i].month -1 , 1, 0, 0, 0, 0);

        // padding 0 for missing months (no user scan in that month)
        if ((i-1)>0) {
            // since 1970
            thisMonth = (scan_times_by_month[i].year - 1970) * 12 + scan_times_by_month[i].month;
            // since 1970
            lastMonth = (scan_times_by_month[i-1].year - 1970) * 12 + scan_times_by_month[i].month;

            diff = thisMonth - lastMonth;
            if(diff>1){
                firstMonth = new Date(scan_times_by_month[i-1].year, scan_times_by_month[i-1].month -1 , 0, 0, 0, 0, 0);
                for(j=1;j<diff;j++){
                    clone_firstMonth = new Date(firstMonth.getTime());
                    clone_firstMonth.setMonth(clone_firstDate.getMonth() + 1);
                    dataPoints.push({
                        x: clone_firstDate,
                        y: 0
                    });
                    firstMonth = clone_firstMonth; 
                }
            }
        }

        dataPoints.push({
            x: dateTime,
            y: scan_times_by_month[i].scan_times
        });
    }
    dataSeries.dataPoints = dataPoints;
    data.push(dataSeries); 

    var chart = new CanvasJS.Chart("chartContainer_month",
        {
            animationEnabled: false, // change to true            
            width:400,
            height: 400,
            theme: "light2",
            zoomEnabled: true,
            title: {
                text: "User Scan History by Month",
                fontSize:20,
                fontFamily:"tahoma",
                padding:15                
            },
            axisX: {
                valueFormatString: "MMM-YY"
            },
            axisY: {
                includeZero: false
            },
            data: data,  // random generator below

        });

    chart.render();
}

HistoryList.createChartScanByNormal = function(){
    for(i=0;i<normalCharts.length;i++){
        var chartName = normalCharts[i];
        var dataPoints = [];    
        var scan_times = HistoryList.getScanTimes(chartName);
    
        for(j=0;j<scan_times.length;j++){
            if(scan_times[j][chartName]==null){
                dataPoints.push(
                    {
                        label: "no " + chartName,
                        y: scan_times[j].scan_times
                    }
                );
                continue;
            }        
            dataPoints.push(
                {
                    label: scan_times[j][chartName],
                    y: scan_times[j].scan_times
                }
            );
        }
    
        var chart = new CanvasJS.Chart("chartContainer_" + chartName, {
            width:400,
            height: 400,              
            theme: "light2", // "light2", "dark1", "dark2"
            zoomEnabled: true,
            animationEnabled: false, // change to true		
            title:{
                text: "User Scan History by " + chartName,
                fontSize:20,
                fontFamily:"tahoma",
                padding:15
            },
            axisX:{
                labelAutoFit: true
            },
            data: [
            {
                // Change type to "bar", "area", "spline", "pie",etc.
                type: "column",
                color: "#6699FF",
                indexLabel: "{y}",
                indexLabelPlacement: "outside",  
                indexLabelOrientation: "horizontal",
                dataPoints: dataPoints
            }
            ]
        });
        
        chart.render();        
    }
}
HistoryList.filterChart = function(){
    HistoryList.initChart();
}

$( document ).ready( function()
{
    HistoryList.initGrid();
    if($("#refresh_chart").length!=0){
        HistoryList.initChart();
    }
    HistoryList.addKendoElements();
    HistoryList.addListeners();
});