var QuestionAnalysis = {
};

QuestionAnalysis.initGrids = function()
{
    $('div[id^="openQuestionGrid_"]').each(
        function(){
            var id_attr = $(this).attr('id');
            var id_arr = id_attr.split("_");
            var question_id = id_arr[1];
            $("#" + id_attr).kendoGrid(
                {
                    dataSource: QuestionAnalysis.getOpenQuestionDataSource(question_id),
                    height: 200,
                    sortable: true,
                    selectable: 'multiple',
                    columns: [
                        { field: 'order_number', title: '#', width: 100 },
                        { field: 'value', title: 'Answer' }],
                    pageable: {
                        refresh: true,
                        pageSizes: true,
                        buttonCount: 5
                    }
            });
        }
    )

    $('div[id^="multipleChoicesQuestionGrid_"]').each(
        function(){
            var id_attr = $(this).attr('id');
            var id_arr = id_attr.split("_");
            var question_id = id_arr[1];
            $("#" + id_attr).kendoGrid(
                {
                    dataSource: QuestionAnalysis.getMultipleChoicesQuestionDataSource(question_id),
                    height: 300,
                    sortable: true,
                    selectable: 'multiple',
                    columns: [
                        { field: 'option_en', title: 'Answer Choice' },
                        { field: 'answer_count', title: 'Responses' }],
                    dataBound:function(e){
                        response_data = $("#" + id_attr).data().kendoGrid.dataSource.data();
                        var labels = [];
                        var data = [];                        
                        for(i=0; i<response_data.length; i++) {
                            labels.push(response_data[i].option_en);
                            data.push(response_data[i].answer_count);
                        }     
                        // bar chart
                        var f = document.getElementById("barChart_"+question_id);
                        if (f) {
                            $("#barChartContainer_"+question_id).empty();
                            $("#barChartContainer_"+question_id).append("<canvas id=\"barChart_" + question_id + "\"></canvas>");
                            f = document.getElementById("barChart_"+question_id);    
                            f.height = 300;
                            f.width = 700;
                            chart = new Chart(f, {
                                type: "bar",
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: "# of answers",
                                        backgroundColor: "#3498DB",
                                        data: data
                                    }]
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: !0,
                                            }
                                        }],
                                        xAxes: [{
                                            ticks: {
                                                autoSkip: false
                                            }
                                        }]
                                    },
                                    responsive: false
                                }
                            });                        
                        } else {
                            var f = document.getElementById("pieChart_"+question_id);

                            if (f) {                                
                                f.remove();

                                $("#pieChartContainer_"+question_id).empty();
                                $("#pieChartContainer_"+question_id).append("<canvas id=\"pieChart_" + question_id + "\"></canvas>");
                                f = document.getElementById("pieChart_"+question_id);
                                // pie chart
                                backgroundColor = ["#4d0000", "#990000", "#ff0000", "#ff6666", "#ffcccc", "white", "#e6ffe6", "#99ff99", "#00ff00", "#009900", "#003300"];
                                if(data.length<=11){
                                    backgroundColor = backgroundColor.slice(0,data.length);
                                }
                                f.height = 105;

                                chart = new Chart(f, {
                                    data: {
                                        datasets: [{
                                            data: data,
                                            backgroundColor: backgroundColor,
                                            label: "Pie Chart"
                                        }],
                                        labels: labels
                                    },
                                    type: "pie",
                                    otpions: {
                                        legend: !1,       
                                        animation: {
                                            animateRotate: false
                                        }
                                    },                                
                                });                                   
                            }                                                              
                        }
                    }                    
                });
        }
    )

    $('div[id^="npsGrid_"]').each(
        function(){
            var id_attr = $(this).attr('id');
            var id_arr = id_attr.split("_");
            var question_id = id_arr[1];
            $("#" + id_attr).kendoGrid(
                {
                    dataSource: QuestionAnalysis.getNPSAnalysisDataSource(question_id),
                    height: 300,
                    sortable: true,
                    selectable: 'multiple',
                    columns: [
                        { field: 'name_en', title: 'Product' },
                        { field: 'answer_0_6', title: 'Detractors' },
                        { field: 'answer_7_8', title: 'Passives' },
                        { field: 'answer_9_10', title: 'Promoters' },
                        { field: 'nps', title: 'NPS' }]
                });
        }
    )
}

QuestionAnalysis.getOpenQuestionDataSource = function(question_id)
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
                    url: "/member/getQuestionAnalysis",
                    dataType: 'json',
                    data:
                    {
                        question_id: question_id,
                        filters: QuestionAnalysis.getFilters()
                    }
                }
            },
            schema:
            {
                model: QuestionAnalysis.getQuestionModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

QuestionAnalysis.getMultipleChoicesQuestionDataSource = function(question_id)
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
                    url: "/member/getQuestionAnalysis",
                    dataType: 'json',
                    data:
                    {
                        question_id: question_id,
                        filters: QuestionAnalysis.getFilters()
                    }
                }
            },
            schema:
            {
                model: QuestionAnalysis.getQuestionModel()
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

QuestionAnalysis.getNPSAnalysisDataSource = function(question_id)
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
                    url: "/member/getNPSAnalysis",
                    dataType: 'json',
                    data:
                    {
                        question_id: question_id,
                        filters: QuestionAnalysis.getFilters()
                    }
                }
            },
            schema:
            {
                model: QuestionAnalysis.getQuestionModel()
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

QuestionAnalysis.getQuestionModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

QuestionAnalysis.getFilters = function()
{
    var filters =
    {
        option_filters: function()
        {
            var values = [];
            $("select[class^='option_filter_']").each(function() {
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

QuestionAnalysis.filterOpenGrid = function()
{
    QuestionAnalysis.getOpenGrid().dataSource.filter({});
}

QuestionAnalysis.getOpenGrid = function(question_id)
{
    return $( '#openQuestionGrid_' + question_id ).data( 'kendoGrid' );
}

QuestionAnalysis.filterMultipleChoicesGrid = function()
{
    QuestionAnalysis.getMultipleChoicesGrid().dataSource.filter({});
}

QuestionAnalysis.getMultipleChoicesGrid = function(question_id)
{
    return $( '#multipleChoicesQuestionGrid_' + question_id ).data( 'kendoGrid' );
}

QuestionAnalysis.exportOpenQuestionToExcel = function(question_id)
{
    QuestionAnalysis.getOpenGrid(question_id).saveAsExcel();
}

QuestionAnalysis.exportMultipleChoicesQuestionToExcel = function(question_id)
{
    QuestionAnalysis.getMultipleChoicesGrid(question_id).saveAsExcel();
}

QuestionAnalysis.addListeners = function()
{
    $( "select[class^='option_filter_']" ).change( QuestionAnalysis.filterAllGrids );    
}

QuestionAnalysis.filterAllGrids = function()
{
    $('.questionGrid').each(
        function(){
            var id_attr = $(this).attr('id');
            $( '#' + id_attr ).data( 'kendoGrid').dataSource.filter({});
        }
    )
    ResponseList.getGrid().dataSource.filter({});
}

QuestionAnalysis.addKendoElements = function()
{
    $( "select[class^='option_filter_']").kendoDropDownList();
}


$( document ).ready( function()
{
    QuestionAnalysis.initGrids();
    QuestionAnalysis.addKendoElements();
    QuestionAnalysis.addListeners();
});