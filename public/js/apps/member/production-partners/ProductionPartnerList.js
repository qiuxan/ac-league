var ProductionPartnerList = {};

ProductionPartnerList.initGrid = function(){
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: ProductionPartnerList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'name_en', title: 'Name' },
                { field: 'address', title: 'Address' },
                { field: 'phone', title: 'Phone' }
            ],
            change: function( e )
            {
                ProductionPartnerList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                ProductionPartnerList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        }
    );    
}

ProductionPartnerList.getDataSource = function(){
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/member/getProductionPartners",
                    dataType: 'json',
                    data:
                    {
                        filters: ProductionPartnerList.getFilters()
                    }
                }
            },
            schema:
            {
                model: ProductionPartnerList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

ProductionPartnerList.getModel = function(){
    return kendo.data.Model.define(
        {
            id: 'id'
        });    
}

ProductionPartnerList.getFilters = function(){
    var filters =
    {
        search: function()
        {
            return $( '#searchFilter' ).val();
        }
    }

    return filters;    
}

ProductionPartnerList.filterGrid = function(){
    ProductionPartnerList.getGrid().dataSource.filter({});    
}

ProductionPartnerList.filters = function(){
    var filters = [];
    filters.push( { app: 'production-partners', grid: 'grid', filterName: 'search', filterValue: ProductionPartnerList.getFilters().search() } );
    return filters;    
}

ProductionPartnerList.addListeners = function(){
    $( 'table' ).dblclick( ProductionPartnerList.editProductionPartner );
    $( '#searchFilter' ).keyup( ProductionPartnerList.filterGrid );
    $( '#searchFilter' ).click( ProductionPartnerList.filterGrid );
    $( '#addButton' ).click( ProductionPartnerList.addProductionPartner );
    $( '#editButton' ).click( ProductionPartnerList.editProductionPartner );
    $( '#deleteButton' ).click( ProductionPartnerList.deleteProductionPartners );    
}

ProductionPartnerList.setSelected = function( selectedRows ){
    if( selectedRows.length == 1 )
        {
            $( '#editButton' ).removeClass( 'k-state-disabled' );
        }
        else
        {
            $( '#editButton' ).addClass( 'k-state-disabled' );
        }

        if( selectedRows.length > 0 )
        {
            $( '#deleteButton' ).removeClass( 'k-state-disabled' );
        }
        else
        {
            $( '#deleteButton' ).addClass( 'k-state-disabled' );
        }    
}

ProductionPartnerList.getGrid = function(){
    return $( '#grid' ).data( 'kendoGrid' );    
}

ProductionPartnerList.addProductionPartner = function(){
    _production_partner_id = 0;
    $( '#mainContentDiv' ).load( "/member/getProductionPartnerForm" );    
}

ProductionPartnerList.deleteProductionPartners = function(){
    var ids = [];
    var selected = ProductionPartnerList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( ProductionPartnerList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteProductionPartners", {ids: ids, _token: $('[name="_token"]').val()}, function (response) {
            response = JSON.parse(response);
            if(response.result == 1)
            {
                ProductionPartnerList.filterGrid();
            }
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected production partner(s)?");    
}

ProductionPartnerList.editProductionPartner = function(){
    var uid = ( ProductionPartnerList.getGrid().select().data() ) ? ProductionPartnerList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = ProductionPartnerList.getGrid().dataSource.getByUid( uid );
        _production_partner_id = selected.id;

        $( '#mainContentDiv' ).load( "/member/getProductionPartnerForm" );
    }    
}

$( document ).ready(function(){
    ProductionPartnerList.initGrid();
    ProductionPartnerList.addListeners();
});