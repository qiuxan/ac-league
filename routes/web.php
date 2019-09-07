<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Constant;

Route::get('/', 'HomeController@index');
Route::post('/switchLang', 'HomeController@switchLang');
Route::get('/verification', 'HomeController@verification');
Route::post('/verify', 'HomeController@verify');
Route::get('/verify/{code}', ['uses' =>'HomeController@verify']);
Route::post('/authenticate', 'HomeController@authenticate');
Route::get('/qrscan-html', 'HomeController@verify');
Route::get('/pages/qrscan.html', 'HomeController@verify');
Route::post('/setPosition', 'HomeController@setPosition');
Route::post('/setNoPosition', 'HomeController@setNoPosition');
Route::get('/adminTest', 'HomeController@adminTest');
Route::post('survey-response', 'HomeController@surveyResponse');

/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::group([
    'middleware' => 'auth',
], function () {
    Route::resource('files', 'FileController', ['only' => ['store']]);

    /* STAFF */
    Route::group([
        'prefix' => 'staff',
        'namespace' => 'Staff',
        'as' => 'staff.',
        'middleware' => ['role:' . Constant::STAFF]
    ], function () {
        Route::get('/', 'HomeController@index');

        Route::get('getPosts', 'PostController@getPosts');
        Route::get('getPostForm', 'PostController@getPostForm');
        Route::get('getPostList', 'PostController@getPostList');
        Route::get('getPost', 'PostController@getPost');
        Route::post('deletePosts', 'PostController@deletePosts');
        Route::get('posts', 'PostController@index');
        Route::post('savePost', 'PostController@store');
        Route::get('getPostCategories', 'PostController@getPostCategories');

        Route::get('getMenus', 'MenuController@getMenus');
        Route::get('getMenu', 'MenuController@getMenu');
        Route::post('deleteMenu', 'MenuController@deleteMenu');
        Route::get('menus', 'MenuController@index');
        Route::post('saveMenu', 'MenuController@store');
        Route::post('saveMenuTree', 'MenuController@saveMenuTree');

        Route::get('slides', 'SlideController@index');
        Route::get('getSlides', 'SlideController@getSlides');
        Route::get('getSlideForm', 'SlideController@getSlideForm');
        Route::get('getSlideList', 'SlideController@getSlideList');
        Route::get('getSlide', 'SlideController@getSlide');
        Route::get('getLargestSlidePriority', 'SlideController@getLargestSlidePriority');
        Route::post('deleteSlides', 'SlideController@deleteSlides');
        Route::post('saveSlide', 'SlideController@store');
        Route::post('updateSlidePriorities', 'SlideController@updateSlidePriorities');

        Route::get('files', 'FileController@index');
        Route::get('getFiles', 'FileController@getFiles');
        Route::get('getFileForm', 'FileController@getFileForm');
        Route::get('getFileList', 'FileController@getFileList');
        Route::get('getFile', 'FileController@getFile');
        Route::post('deleteFiles', 'FileController@deleteFiles');
        Route::post('saveFile', 'FileController@store');

        Route::get('userRequests', 'UserRequestController@index');
        Route::get('getUserRequest', 'UserRequestController@getUserRequest');
        Route::get('getUserRequests', 'UserRequestController@getUserRequests');
        Route::get('getAllUserRequestStatus', 'UserRequestController@getAllUserRequestStatus');
        Route::post('updateUserRequestStatus', 'UserRequestController@updateUserRequestStatus');

        Route::get('getUser', 'UserController@getUser');
        Route::get('profile', 'UserController@getStaffProfileForm');
        Route::post('saveUser', 'UserController@store');
    });

    /* MEMBER */
    /* can manage their own products */
    Route::group([
        'prefix' => 'member',
        'namespace' => 'Member',
        'as' => 'member.',
        'middleware' => ['role:' . Constant::MEMBER]
    ], function () {
        Route::get('welcome', 'HomeController@welcome');
    });
    Route::group([
        'prefix' => 'member',
        'namespace' => 'Member',
        'as' => 'member.',
        'middleware' => ['role:' . Constant::MEMBER, 'member.active']
    ], function () {
        Route::get('/', 'HomeController@index');

        Route::get('profile', 'MemberController@getMemberForm');
        Route::get('getMember', 'MemberController@getMember');
        Route::post('saveMember', 'MemberController@store');

        Route::get('getProducts', 'ProductController@getProducts');
        Route::get('getProductForm', 'ProductController@getProductForm');
        Route::get('getProductList', 'ProductController@getProductList');
        Route::get('getProduct', 'ProductController@getProduct');
        Route::post('deleteProducts', 'ProductController@deleteProducts');
        Route::get('products', 'ProductController@index');
        Route::post('saveProduct', 'ProductController@store');
        Route::get('getAllProducts', 'ProductController@getAllProducts');

        Route::get('getProductAttributes', 'ProductController@getProductAttributes');
        Route::get('getProductAttributeForm', 'ProductController@getProductAttributeForm');
        Route::get('getProductAttribute', 'ProductController@getProductAttribute');
        Route::post('deleteProductAttributes', 'ProductController@deleteProductAttributes');
        Route::post('saveProductAttribute', 'ProductController@storeProductAttribute');
        Route::post('saveProductAttribute', 'ProductController@storeProductAttribute');
        Route::post('updateProductAttributePriorities', 'ProductController@updateProductAttributePriorities');

        Route::post('addProductImage', 'ProductController@addProductImage');
        Route::get('getProductImages', 'ProductController@getProductImages');
        Route::get('getProductList', 'ProductController@getProductList');
        Route::post('setProductThumbnail', 'ProductController@setProductThumbnail');
        Route::post('setProductImagePriority', 'ProductController@setProductImagePriority');
        Route::post('setProductImageDescription', 'ProductController@setProductImageDescription');
        Route::post('deleteProductImage', 'ProductController@deleteProductImage');

        Route::get('getBatches', 'BatchController@getBatches');
        Route::get('getBatchForm', 'BatchController@getBatchForm');
        Route::get('getBatchList', 'BatchController@getBatchList');
        Route::get('getBatch', 'BatchController@getBatch');
        Route::post('deleteBatches', 'BatchController@deleteBatches');
        Route::get('batches', 'BatchController@index');
        Route::post('saveBatch', 'BatchController@store');

        Route::get('getRolls', 'RollController@getRolls');
        Route::get('getRollForm', 'RollController@getRollForm');
        Route::get('getRollList', 'RollController@getRollList');
        Route::get('getRoll', 'RollController@getRoll');
        Route::get('rolls', 'RollController@index');
        Route::post('deleteRolls', 'RollController@deleteRolls');
        Route::post('saveRoll', 'RollController@store');

        Route::get('getCodes', 'CodeController@getCodes');
        Route::get('getCodeForm', 'CodeController@getCodeForm');
        Route::get('getCodeList', 'CodeController@getCodeList');
        Route::get('getCode', 'CodeController@getCode');
        Route::post('deleteCodes', 'CodeController@deleteCodes');
        Route::get('codes', 'CodeController@index');
        Route::post('saveCode', 'CodeController@store');
        Route::get('getRollCodes', 'CodeController@getRollCodes');

        Route::get('getBatchRolls', 'BatchRollController@getBatchRolls');
        Route::get('getBatchRollForm', 'BatchRollController@getBatchRollForm');
        Route::get('getBatchRollList', 'BatchRollController@getBatchRollList');
        Route::get('getBatchRoll', 'BatchRollController@getBatchRoll');
        Route::post('deleteBatchRolls', 'BatchRollController@deleteBatchRolls');
        Route::get('batchRolls', 'BatchRollController@index');
        Route::post('saveBatchRoll', 'BatchRollController@store');
        Route::get('getAvailableRolls', 'BatchRollController@getAvailableRolls');
        Route::get('getRollInfoForBatch', 'BatchRollController@getRollInfoForBatch');
        Route::get('getCodeQuantity', 'BatchRollController@getCodeQuantity');
        Route::get('getCodeFromQuantity', 'BatchRollController@getCodeFromQuantity');

        Route::get('getHistories', 'HistoryController@getHistories');
        Route::get('reports', 'HistoryController@index');
        Route::get('getHistoriesCoordinates', 'HistoryController@getHistoriesCoordinates');
        Route::get('getScanTimesByDate', 'HistoryController@getScanTimesByDate');
        Route::get('getScanTimesByMonth', 'HistoryController@getScanTimesByMonth');
        Route::get('getScanTimes', 'HistoryController@getScanTimes');

        Route::get('getProductionPartners', 'ProductionPartnerController@getProductionPartners');
        Route::get('getProductionPartnerForm', 'ProductionPartnerController@getProductionPartnerForm');
        Route::get('getProductionPartnerList', 'ProductionPartnerController@getProductionPartnerList');
        Route::get('getProductionPartner', 'ProductionPartnerController@getProductionPartner');
        Route::post('deleteProductionPartners', 'ProductionPartnerController@deleteProductionPartners');
        Route::get('production-partners', 'ProductionPartnerController@index');
        Route::post('saveProductionPartner', 'ProductionPartnerController@store');
        Route::get('getAllProductionPartners', 'ProductionPartnerController@getAllProductionPartners');
        Route::get('getProductionPartnerRoles', 'ProductionPartnerController@getProductionPartnerRoles');
        Route::post('deleteProductionPartnerRoles', 'ProductionPartnerController@deleteProductionPartnerRoles');
        Route::post('addProductionPartnerRoles', 'ProductionPartnerController@addProductionPartnerRoles');
        Route::get('getRoleList', 'ProductionPartnerController@getRoleList');
        Route::get('getProductionPartnerRoleList', 'ProductionPartnerController@getProductionPartnerRoleList');
        Route::get('getContractManufacturers', 'ProductionPartnerController@getContractManufacturers');

        Route::get('files', 'FileController@index');
        Route::get('getFiles', 'FileController@getFiles');
        Route::get('getFileList', 'FileController@getFileList');
        Route::get('getFile', 'FileController@getFile');
        Route::post('deleteFiles', 'FileController@deleteFiles');
        Route::post('saveFile', 'FileController@store');
        Route::post('upload_files', 'FileController@upload_files');
        Route::get('getStorageUsage', 'FileController@getStorageUsage');

        Route::get('getSurvey', 'SurveyController@getSurvey');
        Route::get('survey', 'SurveyController@getSurveyForm');
        Route::get('getSurveyQuestionForm', 'SurveyController@getSurveyQuestionForm');
        Route::get('getQuestionOptionForm', 'SurveyController@getQuestionOptionForm');
        Route::get('getSurveyQuestion', 'SurveyController@getSurveyQuestion');
        Route::get('getSurveyQuestions', 'SurveyController@getSurveyQuestions');
        Route::get('getQuestionOption', 'SurveyController@getQuestionOption');
        Route::get('getQuestionOptions', 'SurveyController@getQuestionOptions');
        Route::post('saveSurvey', 'SurveyController@store');
        Route::post('saveQuestion', 'SurveyController@storeQuestion');
        Route::post('saveQuestionOption', 'SurveyController@storeQuestionOption');
        Route::post('deleteQuestions', 'SurveyController@deleteQuestions');
        Route::post('deleteQuestionOptions', 'SurveyController@deleteQuestionOptions');
        Route::post('updateQuestionPriorities', 'SurveyController@updateQuestionPriorities');
        Route::post('updateOptionPriorities', 'SurveyController@updateOptionPriorities');
        Route::get('getResponses', 'SurveyController@getResponses');
        Route::get('getQuestionAnalysis', 'SurveyController@getQuestionAnalysis');
        Route::get('getNPSAnalysis', 'SurveyController@getNPSAnalysis');
        Route::get('getResponseAnswers/{response_id}', ['uses' =>'SurveyController@getResponseAnswers']);

        Route::get('getMessages', 'MessageController@getMessages');
        Route::get('getMessageForm', 'MessageController@getMessageForm');
        Route::get('getMessageList', 'MessageController@getMessageList');
        Route::get('getMessage', 'MessageController@getMessage');
        Route::post('deleteMessages', 'MessageController@deleteMessages');
        Route::get('messages', 'MessageController@index');
        Route::post('saveMessage', 'MessageController@store');
        Route::post('updateMessageStatus', 'MessageController@updateMessageStatus');
        Route::post('sendMessage', 'MessageController@sendMessage');

        Route::get('getCartons', 'CartonController@getCartons');
        Route::get('getCartonForm', 'CartonController@getCartonForm');
        Route::get('getCartonList', 'CartonController@getCartonList');
        Route::get('getCarton', 'CartonController@getCarton');
        Route::post('deleteCartons', 'CartonController@deleteCartons');
        Route::get('cartons', 'CartonController@index');
        Route::get('getCartonProductList', 'CartonController@getCartonProductList');
        Route::get('getCartonBatchList', 'CartonController@getCartonBatchList');
        Route::get('getCartonAvailableCodesList', 'CartonController@getCartonAvailableCodesList');
        Route::get('getCartonAvailableCodes', 'CartonController@getCartonAvailableCodes');
        Route::post('saveCarton', 'CartonController@store');

        Route::get('getCartonItems', 'CartonItemController@getCartonItems');
        Route::post('saveCartonItem', 'CartonItemController@store');
        Route::post('deleteCartonItems', 'CartonItemController@deleteCartonItems');

        Route::get('pallets', 'PalletController@index');
        Route::get('getPallet', 'PalletController@getPallet');
        Route::get('getPallets', 'PalletController@getPallets');
        Route::get('getPalletForm', 'PalletController@getPalletForm');
        Route::get('getPalletList', 'PalletController@getPalletList');
        Route::get('getPalletProductList', 'PalletController@getPalletProductList');
        Route::get('getPalletBatchList', 'PalletController@getPalletBatchList');        
        Route::get('getPalletAvailableCodesList', 'PalletController@getPalletAvailableCodesList');
        Route::get('getPalletAvailableCodes', 'PalletController@getPalletAvailableCodes');
        Route::get('getPalletAvailableCartonsList', 'PalletController@getPalletAvailableCartonsList');
        Route::get('getPalletAvailableCartons', 'PalletController@getPalletAvailableCartons');        
        Route::post('savePallet', 'PalletController@store');
        Route::post('deletePallets', 'PalletController@deletePallets');
        Route::get('getPalletItems', 'PalletController@getPalletItems');
        Route::post('savePalletItem', 'PalletController@savePalletItem');
        Route::post('deletePalletItems', 'PalletController@deletePalletItems');
        Route::get('getPalletCartons', 'PalletController@getPalletCartons');        
        Route::post('savePalletCarton', 'PalletController@savePalletCarton');
        Route::post('deletePalletCartons', 'PalletController@deletePalletCartons');
    });

    /* ADMIN */
    /* can manage users, products */

    Route::group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'as' => 'admin.',
        'middleware' => ['role:' . Constant::ADMIN]
    ], function () {
        Route::get('/', 'HomeController@index');
        Route::get('getMembers', 'MemberController@getMembers');
        Route::get('getMemberForm', 'MemberController@getMemberForm');
        Route::post('getMemberForm', 'MemberController@getMemberForm');
        Route::get('getMemberList', 'MemberController@getMemberList');
        Route::get('getMember', 'MemberController@getMember');
        Route::post('deleteMembers', 'MemberController@deleteMembers');
        Route::get('members', 'MemberController@index');
        Route::post('saveMember', 'MemberController@store');
        Route::post('saveMemberPermissions', 'MemberController@saveMemberPermissions');

        Route::get('getProducts', 'ProductController@getProducts');
        Route::get('getProductForm', 'ProductController@getProductForm');
        Route::get('getProductList', 'ProductController@getProductList');
        Route::get('getProduct', 'ProductController@getProduct');
        Route::post('deleteProducts', 'ProductController@deleteProducts');
        Route::get('products', 'ProductController@index');
        Route::post('saveProduct', 'ProductController@store');
        Route::get('getMemberProducts', 'ProductController@getMemberProducts');

        Route::get('getProductAttributes', 'ProductController@getProductAttributes');
        Route::get('getProductAttributeForm', 'ProductController@getProductAttributeForm');
        Route::get('getProductAttribute', 'ProductController@getProductAttribute');
        Route::post('deleteProductAttributes', 'ProductController@deleteProductAttributes');
        Route::post('saveProductAttribute', 'ProductController@storeProductAttribute');
        Route::post('saveProductAttribute', 'ProductController@storeProductAttribute');
        Route::post('updateProductAttributePriorities', 'ProductController@updateProductAttributePriorities');

        Route::post('addProductImage', 'ProductController@addProductImage');
        Route::get('getProductImages', 'ProductController@getProductImages');
        Route::get('getProductList', 'ProductController@getProductList');
        Route::post('setProductThumbnail', 'ProductController@setProductThumbnail');
        Route::post('setProductImagePriority', 'ProductController@setProductImagePriority');
        Route::post('setProductImageDescription', 'ProductController@setProductImageDescription');
        Route::post('deleteProductImage', 'ProductController@deleteProductImage');

        Route::get('getBatches', 'BatchController@getBatches');
        Route::get('getBatchForm', 'BatchController@getBatchForm');
        Route::get('getBatchList', 'BatchController@getBatchList');
        Route::get('getBatch', 'BatchController@getBatch');
        Route::post('deleteBatches', 'BatchController@deleteBatches');
        Route::get('batches', 'BatchController@index');
        Route::post('saveBatch', 'BatchController@store');
        Route::get('getMemberBatches', 'BatchController@getMemberBatches');

        Route::get('getRolls', 'RollController@getRolls');
        Route::get('getRollForm', 'RollController@getRollForm');
        Route::get('getRollList', 'RollController@getRollList');
        Route::get('getRoll', 'RollController@getRoll');
        Route::get('rolls', 'RollController@index');
        Route::post('deleteRolls', 'RollController@deleteRolls');
        Route::post('saveRoll', 'RollController@store');
        Route::post('importRoll', 'RollController@importRoll');
        Route::post('importRollFromURLs', 'RollController@importRollFromURLs');

        Route::get('getMessages', 'MessageController@getMessages');
        Route::get('getMessageForm', 'MessageController@getMessageForm');
        Route::get('getMessageList', 'MessageController@getMessageList');
        Route::get('getMessage', 'MessageController@getMessage');
        Route::post('deleteMessages', 'MessageController@deleteMessages');
        Route::get('messages', 'MessageController@index');
        Route::post('saveMessage', 'MessageController@store');

        Route::get('getFactoryBatches', 'FactoryBatchController@getFactoryBatches');
        Route::get('getFactoryBatchForm', 'FactoryBatchController@getFactoryBatchForm');
        Route::get('getFactoryBatchList', 'FactoryBatchController@getFactoryBatchList');
        Route::get('getFactoryBatch', 'FactoryBatchController@getFactoryBatch');
        Route::post('deleteFactoryBatches', 'FactoryBatchController@deleteFactoryBatches');
        Route::get('factoryBatches', 'FactoryBatchController@index');
        Route::post('saveFactoryBatch', 'FactoryBatchController@store');
        Route::get('exportFactoryBatch/{batch_id}', ['uses' =>'FactoryBatchController@exportFactoryBatch']);
        Route::post('importFactoryCodes', 'FactoryBatchController@importFactoryCodes');

        Route::get('getCodes', 'CodeController@getCodes');
        Route::get('getCodeForm', 'CodeController@getCodeForm');
        Route::get('getCodeList', 'CodeController@getCodeList');
        Route::get('getCode', 'CodeController@getCode');
        Route::post('deleteCodes', 'CodeController@deleteCodes');
        Route::get('codes', 'CodeController@index');
        Route::post('saveCode', 'CodeController@store');
        Route::get('getRollCodes', 'CodeController@getRollCodes');

        Route::get('getBatchRolls', 'BatchRollController@getBatchRolls');
        Route::get('getBatchRollForm', 'BatchRollController@getBatchRollForm');
        Route::get('getBatchRollList', 'BatchRollController@getBatchRollList');
        Route::get('getBatchRoll', 'BatchRollController@getBatchRoll');
        Route::post('deleteBatchRolls', 'BatchRollController@deleteBatchRolls');
        Route::get('batchRolls', 'BatchRollController@index');
        Route::post('saveBatchRoll', 'BatchRollController@store');
        Route::get('getMemberAvailableRolls', 'BatchRollController@getMemberAvailableRolls');
        Route::get('getRollInfoForBatch', 'BatchRollController@getRollInfoForBatch');
        Route::get('getCodeQuantity', 'BatchRollController@getCodeQuantity');
        Route::get('getCodeFromQuantity', 'BatchRollController@getCodeFromQuantity');

        Route::get('getUser', 'UserController@getUser');
        Route::get('profile', 'UserController@getAdminProfileForm');
        Route::post('saveUser', 'UserController@store');

        Route::get('getSystemVariables', 'SystemVariableController@getSystemVariables');
        Route::get('getSystemVariableForm', 'SystemVariableController@getSystemVariableForm');
        Route::get('getSystemVariableList', 'SystemVariableController@getSystemVariableList');
        Route::get('getSystemVariable', 'SystemVariableController@getSystemVariable');
        Route::post('deleteSystemVariables', 'SystemVariableController@deleteSystemVariables');
        Route::get('system-variables', 'SystemVariableController@index');
        Route::post('saveSystemVariable', 'SystemVariableController@store');
        Route::get('getTypeSystemVariables', 'SystemVariableController@getTypeSystemVariables');

        Route::get('getMemberConfigurations', 'MemberConfigurationController@getMemberConfigurations');
        Route::get('getMemberConfigurationForm', 'MemberConfigurationController@getMemberConfigurationForm');
        Route::get('getMemberConfigurationList', 'MemberConfigurationController@getMemberConfigurationList');
        Route::get('getMemberConfiguration', 'MemberConfigurationController@getMemberConfiguration');
        Route::post('deleteMemberConfigurations', 'MemberConfigurationController@deleteMemberConfigurations');
        Route::get('member-configurations', 'MemberConfigurationController@index');
        Route::post('saveMemberConfiguration', 'MemberConfigurationController@store');

        Route::get('getProductionPartners', 'ProductionPartnerController@getProductionPartners');
        Route::get('getProductionPartnerForm', 'ProductionPartnerController@getProductionPartnerForm');
        Route::get('getProductionPartnerList', 'ProductionPartnerController@getProductionPartnerList');
        Route::get('getProductionPartner', 'ProductionPartnerController@getProductionPartner');
        Route::get('getMemberProductionPartner', 'ProductionPartnerController@getMemberProductionPartner');
        Route::post('deleteProductionPartners', 'ProductionPartnerController@deleteProductionPartners');
        Route::get('production-partners', 'ProductionPartnerController@index');
        Route::post('saveProductionPartner', 'ProductionPartnerController@store');

        Route::get('getHistories', 'HistoryController@getHistories');
        Route::get('reports', 'HistoryController@index');

        Route::get('getMemberMessages', 'MemberMessageController@getMessages');
        Route::get('getSentMemberMessages', 'MemberMessageController@getSentMessages');
        Route::get('getMemberMessageList', 'MemberMessageController@getMessageList');
        Route::get('getMemberMessage', 'MemberMessageController@getMessage');
        Route::post('deleteMemberMessages', 'MemberMessageController@deleteMessages');
        Route::get('member-messages', 'MemberMessageController@index');
        Route::post('saveMemberMessage', 'MemberMessageController@store');
        Route::post('updateMemberMessageStatus', 'MemberMessageController@updateMessageStatus');
        Route::post('sendMemberMessage', 'MemberMessageController@sendMessage');
        Route::get('getMemberMessageForm', 'MemberMessageController@getMessageForm');

        Route::get('getPermissions', 'PermissionController@getPermissions');
        Route::get('getPermissionForm', 'PermissionController@getPermissionForm');
        Route::get('getPermissionList', 'PermissionController@getPermissionList');
        Route::get('getPermission', 'PermissionController@getPermission');
        Route::post('deletePermissions', 'PermissionController@deletePermissions');
        Route::get('permissions', 'PermissionController@index');
        Route::post('savePermission', 'PermissionController@store');
        Route::post('updatePermissionPriorities', 'PermissionController@updatePermissionPriorities');

        Route::get('getRoles', 'RoleController@getRoles');
        Route::get('getRoleForm', 'RoleController@getRoleForm');
        Route::post('getRoleForm', 'RoleController@getRoleForm');
        Route::get('getRoleList', 'RoleController@getRoleList');
        Route::get('getRole', 'RoleController@getRole');
        Route::post('deleteRoles', 'RoleController@deleteRoles');
        Route::get('roles', 'RoleController@index');
        Route::post('saveRole', 'RoleController@store');
    });

    Route::group([
        'prefix' => 'production-partner',
        'namespace' => 'ProductionPartner',
        'as' => 'production-partner.',
        'middleware' => ['role:' . Constant::CONTRACT_MANUFACTURER . '|' . Constant::INGREDIENT_SUPPLIER . '|' . Constant::DISTRIBUTOR]
    ], function () {
        Route::get('/', 'HomeController@index');

        Route::get('profile', 'ProductionPartnerController@getProductionPartnerForm');
        Route::get('getMember', 'ProductionPartnerController@getProductionPartner');
        Route::post('saveMember', 'ProductionPartnerController@store');


        Route::group(['middleware' => ['permission:access product|manage product']], function () {
            Route::get('getProducts', 'ProductController@getProducts');
            Route::get('getProductForm', 'ProductController@getProductForm');
            Route::get('getProductList', 'ProductController@getProductList');
            Route::get('getProduct', 'ProductController@getProduct');
            Route::get('products', 'ProductController@index');
            Route::get('getAllProducts', 'ProductController@getAllProducts');

            Route::get('getProductAttributes', 'ProductController@getProductAttributes');
            Route::get('getProductAttributeForm', 'ProductController@getProductAttributeForm');
            Route::get('getProductAttribute', 'ProductController@getProductAttribute');

            Route::get('getProductImages', 'ProductController@getProductImages');
            Route::get('getProductList', 'ProductController@getProductList');

            Route::get('getProductIngredientList', 'ProductController@getProductIngredientList');
            Route::get('getIngredientListForProduct', 'ProductController@getIngredientListForProduct');
        });

        Route::group(['middleware' => ['permission:manage product']], function () {
            Route::post('deleteProducts', 'ProductController@deleteProducts');
            Route::post('saveProduct', 'ProductController@store');

            Route::post('deleteProductAttributes', 'ProductController@deleteProductAttributes');
            Route::post('saveProductAttribute', 'ProductController@storeProductAttribute');
            Route::post('updateProductAttributePriorities', 'ProductController@updateProductAttributePriorities');

            Route::post('addProductImage', 'ProductController@addProductImage');
            Route::post('setProductThumbnail', 'ProductController@setProductThumbnail');
            Route::post('setProductImagePriority', 'ProductController@setProductImagePriority');
            Route::post('setProductImageDescription', 'ProductController@setProductImageDescription');
            Route::post('deleteProductImage', 'ProductController@deleteProductImage');

            Route::get('addIngredientsToProduct', 'ProductController@addIngredientsToProduct');
            Route::post('updateProductIngredientPriorities', 'ProductController@updateProductIngredientPriorities');
            Route::post('deleteProductIngredients', 'ProductController@deleteProductIngredients');
        });

        Route::group(['middleware' => ['permission:access batch|manage batch']], function () {
            Route::get('getBatches', 'BatchController@getBatches');
            Route::get('getBatchForm', 'BatchController@getBatchForm');
            Route::get('getBatchList', 'BatchController@getBatchList');
            Route::get('getBatch', 'BatchController@getBatch');
            Route::get('batches', 'BatchController@index');

            Route::get('getBatchRolls', 'BatchRollController@getBatchRolls');
            Route::get('getBatchRollForm', 'BatchRollController@getBatchRollForm');
            Route::get('getBatchRollList', 'BatchRollController@getBatchRollList');
            Route::get('getBatchRoll', 'BatchRollController@getBatchRoll');
            Route::get('getAvailableRolls', 'BatchRollController@getAvailableRolls');
            Route::get('getRollInfoForBatch', 'BatchRollController@getRollInfoForBatch');
            Route::get('getCodeQuantity', 'BatchRollController@getCodeQuantity');
            Route::get('getCodeFromQuantity', 'BatchRollController@getCodeFromQuantity');
            Route::get('batchRolls', 'BatchRollController@index');

            Route::get('getBatchIngredients', 'BatchController@getBatchIngredients');
        });

        Route::group(['middleware' => ['permission:manage batch']], function () {
            Route::post('deleteBatches', 'BatchController@deleteBatches');
            Route::post('saveBatch', 'BatchController@store');

            Route::post('deleteBatchRolls', 'BatchRollController@deleteBatchRolls');
            Route::post('saveBatchRoll', 'BatchRollController@store');

            Route::get('getIngredientLotListForBatch', 'BatchController@getIngredientLotListForBatch');
            Route::get('getIngredientLotsForBatch', 'BatchController@getIngredientLotsForBatch');
            Route::post('addBatchIngredients', 'BatchController@addBatchIngredients');
            Route::post('deleteBatchIngredients', 'BatchController@deleteBatchIngredients');
        });

        Route::group(['middleware' => ['permission:access roll|manage roll']], function () {
            Route::get('getRolls', 'RollController@getRolls');
            Route::get('getRollForm', 'RollController@getRollForm');
            Route::get('getRollList', 'RollController@getRollList');
            Route::get('getRoll', 'RollController@getRoll');
            Route::get('rolls', 'RollController@index');
        });

        Route::group(['middleware' => ['permission:can manage roll']], function () {
            Route::post('deleteRolls', 'RollController@deleteRolls');
            Route::post('saveRoll', 'RollController@store');
        });

        Route::group(['middleware' => ['permission:access code|manage code']], function () {
            Route::get('getCodes', 'CodeController@getCodes');
            Route::get('getCodeForm', 'CodeController@getCodeForm');
            Route::get('getCodeList', 'CodeController@getCodeList');
            Route::get('getCode', 'CodeController@getCode');
            Route::get('codes', 'CodeController@index');
            Route::get('getRollCodes', 'CodeController@getRollCodes');
        });

        Route::group(['middleware' => ['permission:manage code']], function () {
            Route::post('deleteCodes', 'CodeController@deleteCodes');
            Route::post('saveCode', 'CodeController@store');
        });

        Route::group(['middleware' => ['permission:access message|manage message']], function () {
            Route::get('getMessages', 'MessageController@getMessages');
            Route::get('getMessageForm', 'MessageController@getMessageForm');
            Route::get('getMessageList', 'MessageController@getMessageList');
            Route::get('getMessage', 'MessageController@getMessage');
            Route::get('messages', 'MessageController@index');
        });

        Route::group(['middleware' => ['permission:manage message']], function () {
            Route::post('deleteMessages', 'MessageController@deleteMessages');
            Route::post('saveMessage', 'MessageController@store');
            Route::post('updateMessageStatus', 'MessageController@updateMessageStatus');
            Route::post('sendMessage', 'MessageController@sendMessage');
        });

        Route::group(['middleware' => ['permission:access ingredient|manage ingredient']], function () {
            Route::get('getIngredients', 'IngredientController@getIngredients');
            Route::get('getIngredientForm', 'IngredientController@getIngredientForm');
            Route::get('getIngredientList', 'IngredientController@getIngredientList');
            Route::get('getIngredient', 'IngredientController@getIngredient');
            Route::get('ingredients', 'IngredientController@index');
            Route::get('getAllIngredients', 'IngredientController@getAllIngredients');
        });

        Route::group(['middleware' => ['permission:manage ingredient']], function () {
            Route::post('deleteIngredients', 'IngredientController@deleteIngredients');
            Route::post('saveIngredient', 'IngredientController@store');
        });

        Route::group(['middleware' => ['permission:access file|manage file']], function () {
            Route::get('files', 'FileController@index');
            Route::get('getFiles', 'FileController@getFiles');
            Route::get('getFileList', 'FileController@getFileList');
            Route::get('getFile', 'FileController@getFile');
            Route::get('getStorageUsage', 'FileController@getStorageUsage');
        });

        Route::group(['middleware' => ['permission:manage message']], function () {
            Route::post('deleteFiles', 'FileController@deleteFiles');
            Route::post('saveFile', 'FileController@store');
            Route::post('upload_files', 'FileController@upload_files');
        });

        Route::group(['middleware' => ['permission:access ingredient lot|manage ingredient lot']], function () {
            Route::get('getIngredientLots', 'IngredientLotController@getIngredientLots');
            Route::get('getIngredientLotForm', 'IngredientLotController@getIngredientLotForm');
            Route::get('getIngredientLotList', 'IngredientLotController@getIngredientLotList');
            Route::get('getIngredientLot', 'IngredientLotController@getIngredientLot');
            Route::get('ingredient-lots', 'IngredientLotController@index');
            Route::get('getIngredientsForDropdown', 'IngredientController@getIngredientsForDropdown');
        });

        Route::group(['middleware' => ['permission:manage ingredient lot']], function () {
            Route::post('deleteIngredientLots', 'IngredientLotController@deleteIngredientLots');
            Route::post('saveIngredientLot', 'IngredientLotController@store');
        });

        Route::group(['middleware' => ['permission:access ingredient shipping|manage ingredient shipping']], function () {
            Route::get('getIngredientShipments', 'IngredientShipmentController@getIngredientShipments');
            Route::get('getIngredientShipmentForm', 'IngredientShipmentController@getIngredientShipmentForm');
            Route::get('getIngredientShipmentList', 'IngredientShipmentController@getIngredientShipmentList');
            Route::get('getIngredientShipment', 'IngredientShipmentController@getIngredientShipment');
            Route::get('ingredient-shipments', 'IngredientShipmentController@index');
            Route::get('getIngredientLotsForShipping', 'IngredientShipmentController@getIngredientLotsForShipping');
            Route::get('getShipmentIngredientLots', 'IngredientShipmentController@getShipmentIngredientLots');
            Route::get('getMemberContractManufacturers', 'IngredientShipmentController@getMemberContractManufacturers');
            Route::get('getMemberIngredientSuppliers', 'IngredientShipmentController@getMemberIngredientSuppliers');
            Route::get('getIngredientLotListForm', 'IngredientShipmentController@getIngredientLotListForm');
        });

        Route::group(['middleware' => ['permission:manage ingredient shipping']], function () {
            Route::post('deleteIngredientShipments', 'IngredientShipmentController@deleteIngredientShipments');
            Route::post('saveIngredientShipment', 'IngredientShipmentController@store');
        });

        Route::group(['middleware' => ['permission:access ingredient receiving|manage ingredient receiving']], function () {
            Route::get('getIngredientReceipts', 'IngredientReceiptController@getIngredientReceipts');
            Route::get('getIngredientReceiptForm', 'IngredientReceiptController@getIngredientReceiptForm');
            Route::get('getIngredientReceiptList', 'IngredientReceiptController@getIngredientReceiptList');
            Route::get('getIngredientReceipt', 'IngredientReceiptController@getIngredientReceipt');
            Route::get('ingredient-receipts', 'IngredientReceiptController@index');
            Route::get('getReceiptIngredientLots', 'IngredientReceiptController@getReceiptIngredientLots');
        });

        Route::group(['middleware' => ['permission:manage ingredient receiving']], function () {
            Route::post('saveIngredientReceipt', 'IngredientReceiptController@store');
        });

    });
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/news', 'PostController@news');
Route::get('/contact', 'Staff\UserRequestController@showContactForm');
Route::post('/contact', 'Staff\UserRequestController@postContactForm');

Route::get('/search', 'PostController@search');

Route::pattern('postSlug', '[a-z0-9\-]+');
Route::get('/{postSlug}', ['uses' =>'PostController@show']);