@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Pallet Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="palletFormDiv" class="x_content">
            <div id="tabs">
                <ul>
                    <li class="k-state-active">Pallet Info</li>
                    <li data-bind="invisible: isNew">Pallet Cartons</li>
                    <li data-bind="invisible: isNew">Pallet Items</li>
                </ul>        
                <div class="formWrap">
                    <form id="palletForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input data-bind="value: id" name="id" type="hidden"/>
                        <input data-bind="value:batch_id" id="batch_id" name="batch_id" type="hidden"/>
                        <input data-bind="value:product_id" id="product_id" name="product_id" type="hidden"/>
                        <fieldset class="pull-left">
                            <ul>
                                <li>
                                    <label for="sscc3_sn"><strong>SSCC3 SN</strong> :</label>
                                    <input data-bind="value: sscc3_sn" type="text" class="k-textbox" id="sscc3_sn" required validationMessage="This field is required" name="sscc3_sn" placeholder="SSCN2 Serial No."/>
                                </li> 
                                <li>
                                    <label><strong>Product</strong> :</label>
                                    <input readonly data-bind="value: product_name" type="text" class="k-textbox" id="product_name" required validationMessage="This field is required" name="product_name" placeholder="Product Name"/>                            
                                    <button class="k-button" type="button" id="selectProduct">Select</button>
                                </li>                                
                                <li>
                                    <label>Batch :</label>
                                    <input readonly data-bind="value: batch_code" type="text" class="k-textbox" id="batch_code" validationMessage="This field is required" name="batch_code" placeholder="Batch No."/>                            
                                    <button class="k-button" type="button" id="selectBatch">Select</button>
                                    <button class="k-button" type="button" id="clearBatch">Clear</button>
                                </li>                                                                                                     
                            </ul>
                        </fieldset>
                        <div class="clearfix"></div>      
                        <div class="spacer"></div>                
                        <div class="actionBar">
                            <div class="actionBarLeft">
                                <button class="k-button" type="button" id="cancelButton">Cancel</button>
                            </div>
                            <div class="actionBarRight">
                                <span class="status"></span>
                                <button class="k-button" type="button" id="saveButton">Save</button>
                                <button class="k-button" type="button" id="doneButton">Done</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="formWrap">
                    <div class="x_title">
                        <h2>Pallet Cartons</h2>
                        <div class="clearfix"></div>                
                    </div>
                    <div id="pallet_cartons_grid"></div>

                    <div id="availableCartonsListContainer"></div>        
                    <script type="text/x-kendo-template" id="palletCartonToolbarTemplate">
                        <div class="toolbar">
                            <div class="toolbarButtons">
                                <a class="k-button" id="addPalletCartonButton">
                                    <span class="k-icon k-i-add">&nbsp;</span>
                                    <span>Add</span>
                                </a>
                                <a class="k-button k-state-disabled" id="deletePalletCartonButton">
                                    <span class="k-icon k-close">&nbsp;</span>
                                    <span>Delete</span>
                                </a>
                                <div class="pull-right">
                                    <span class="k-textbox k-space-left">
                                        <a class="k-icon k-i-search">&nbsp;</a>
                                        <input type="text" id="searchPalletCarton" placeholder="search"/>
                                    </span>
                                </div>                                
                            </div>
                        </div>
                    </script>
                </div>
                <div class="formWrap">
                    <div class="x_title">
                        <h2>Pallet Items</h2>
                        <div class="clearfix"></div>                
                    </div>
                    <div id="pallet_items_grid"></div>

                    <div id="availableCodesListContainer"></div>
                    <div id="batchListContainer"></div>
                    <div id="productListContainer"></div>                 
                    <script type="text/x-kendo-template" id="palletItemToolbarTemplate">
                        <div class="toolbar">
                            <div class="toolbarButtons">
                                <a class="k-button" id="addPalletItemButton">
                                    <span class="k-icon k-i-add">&nbsp;</span>
                                    <span>Add</span>
                                </a>
                                <a class="k-button k-state-disabled" id="deletePalletItemButton">
                                    <span class="k-icon k-close">&nbsp;</span>
                                    <span>Delete</span>
                                </a>
                                <div class="pull-right">
                                    <span class="k-textbox k-space-left">
                                        <a class="k-icon k-i-search">&nbsp;</a>
                                        <input type="text" id="searchPalletItem" placeholder="search"/>
                                    </span>
                                </div>                                
                            </div>
                        </div>
                    </script>                    
                </div>
            </div>
        </div>
    </div>

    <!-- /page content -->

<!-- page scripts -->
<script src="{{ asset('js/apps/member/pallets/PalletForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection