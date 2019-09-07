var ProductAttributeList = {
};

ProductAttributeList.initGrid = function()
{
    $("#productAttributeFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "650px",
        height: "350px",
        title: "Attribute Detail",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: ProductAttributeList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: false,
            selectable: 'multiple',
            columns: [
                { field: 'name', title: 'Name' },
                { field: 'language', title: 'Language', values: [
                    { text: "English", value: 'en' },
                    { text: "Simplified Chinese", value: 'cn' },
                    { text: "Traditional Chinese", value: 'tr' } ] },
                { field: 'type', title: 'Type', values: [
                    { text: "TextBox", value: 1 },
                    { text: "Content", value: 2 },
                    { text: "Image", value: 3 } ] },
                { field: 'displayed_at', title: 'Displayed At', values: [
                    { text: "Verification + Authentication Pages", value: 1 },
                    { text: "Verification Page", value: 2 },
                    { text: "Authentication Page", value: 3 },
                    { text: "None", value: 4 } ] },
                { field: 'value', title: 'Value', template: '#if(type == 2 && value.length > 50){#<span>Please open for detail</span> #}else{# <span>#=value#</span> #}#' }
            ],
            change: function( e )
            {
                ProductAttributeList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                ProductAttributeList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: [20, 50, 100, "all"],
                buttonCount: 5
            }
        });

        $( '#grid' ).data( 'kendoGrid' ).table.kendoSortable({
            filter: ">tbody >tr",
            hint: $.noop,
            cursor: "move",
            placeholder: function(element) {
                return element.clone().addClass("k-state-hover").css("opacity", 0.65);
            },
            container: "#grid tbody",
            change: function(e) {
                var oldIndex = e.oldIndex,
                    newIndex = e.newIndex,
                    data = $( '#grid' ).data( 'kendoGrid' ).dataSource.data(),
                    dataItem = $( '#grid' ).data( 'kendoGrid' ).dataSource.getByUid(e.item.data("uid"));

                $( '#grid' ).data( 'kendoGrid' ).dataSource.remove(dataItem);
                $( '#grid' ).data( 'kendoGrid' ).dataSource.insert(newIndex, dataItem);

                // update attributes priorities

                dataItems = $( '#grid' ).data( 'kendoGrid' ).dataSource.data();
                var attribute_priorities = [];
                for (i=0; i<dataItems.length; i++) {
                    attribute_priorities.push(dataItems[i]['id']);
                }
                attribute_priorities = {
                    product_id: _product_id,
                    ids: attribute_priorities
                };

                ProductAttributeList.updateAttributePriorities(attribute_priorities );
            }
        });
}

ProductAttributeList.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 50,
            transport:
            {
                read:
                {
                    url: "/member/getProductAttributes",
                    dataType: 'json',
                    data:
                    {
                        product_id: ProductAttributeList.getProductId
                    }
                }
            },
            schema:
            {
                model: ProductAttributeList.getModel(),
                data: 'data',
                total: 'total'
            }
        });
}

ProductAttributeList.getProductId = function()
{
    return _product_id;
}

ProductAttributeList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

ProductAttributeList.filterGrid = function()
{
    ProductAttributeList.getGrid().dataSource.filter({});
}

ProductAttributeList.editProductAttribute = function()
{
    var uid = ( ProductAttributeList.getGrid().select().data() ) ? ProductAttributeList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = ProductAttributeList.getGrid().dataSource.getByUid( uid );
        _product_attribute_id = selected.id;

        ProductAttributeList.showProductAttributeForm();
    }
}

ProductAttributeList.addListeners = function()
{
    $( 'table' ).dblclick( ProductAttributeList.editProductAttribute );
    $( '#addButton' ).click( ProductAttributeList.addProductAttribute );
    $( '#editButton' ).click( ProductAttributeList.editProductAttribute );
    $( '#deleteButton' ).click( ProductAttributeList.deleteProductAttributes );
}

ProductAttributeList.addProductAttribute = function()
{
    _product_attribute_id = 0;
    ProductAttributeList.showProductAttributeForm();
}

ProductAttributeList.setSelected = function( selectedRows )
{
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

ProductAttributeList.deleteProductAttributes = function()
{
    var ids = [];
    var selected = ProductAttributeList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( ProductAttributeList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteProductAttributes", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            ProductAttributeList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected attributes?");
}

ProductAttributeList.updateAttributePriorities = function(attribute_priorities)
{
    attribute_priorities._token = $('[name="_token"]').val();
    $.post("updateProductAttributePriorities", attribute_priorities, function(response){});
}

ProductAttributeList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

ProductAttributeList.showProductAttributeForm = function(){
    $("#productAttributeFormContainer").data("kendoWindow").center();
    $("#productAttributeFormContainer").data("kendoWindow").open();
    $("#productAttributeFormContainer").load( "/member/getProductAttributeForm");
}

ProductAttributeList.refreshProductAttributeList = function() {
    $( "#productAttributeFormContainer" ).data("kendoWindow").close();
    ProductAttributeList.filterGrid();
}

$( document ).ready( function()
{
    ProductAttributeList.initGrid();
    ProductAttributeList.addListeners();
});