@extends('layouts.member')

@section('content')
    <!-- page content -->
    <h1>Welcome to OZM Cloud (v.2.2.1)</h1>
    <!-- /page content -->
    <br/>
    <br/>
    <div>
        <h2> OZM Cloud Version History</h2>

        <div class="version-history">
            <h3> 2.2.1 (Current Release) </h3>
            <h5> Release date: 14/12/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Survey Update</li>
                <ul>
                    <li>
                        The NPS (Net Promoter Score) calculation and presentation
                    </li>
                </ul>

            </ul>
        </div>

        <div class="version-history">
            <h3> 2.2.0 </h3>
            <h5> Release date: 01/12/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Survey Module</li>
                <ul>
                    <li> Members can create a survey</li>
                </ul>
                <ul>
                    <li> Members can add/edit/delele quesitons of survey</li>
                </ul>
                <ul>
                    <li> Member can view/export survey response report</li>
                </ul>
                <ul>
                    <li> Member can view survey analysis</li>
                </ul>

            </ul>
        </div>

        <div class="version-history">
            <h3> 2.1.0 </h3>
            <h5> Release date: 13/11/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Media Library</li>
                <ul>
                    <li>
                        Members can upload images/video/document to OZMC
                    </li>
                </ul>
                <ul>
                    <li>Each member has 500MB storage to upload media files</li>
                </ul>
                <ul>
                    <li>If member deletes a media file, that file will be deleted permanently and the space occupied by that file will be released</li>
                </ul>
                <li style="font-weight: bold;">Reports</li>
                <ul>
                    <li> Members can filter/search scan history based on location, product name, batch code, time, status, etc</li>
                </ul>
                <ul>
                    <li> Members can export reports to excel files</li>
                </ul>

            </ul>
        </div>

        <div class="version-history">
            <h3> 2.0.0 </h3>
            <h5> Release date: 01/11/2017 </h5>
            <ul>
                <li style="font-weight: bold;">New Product Template</li>
                <ul>
                    <li> Members can add/edit/delele product attribute dynamically</li>
                </ul>
                <ul>
                    <li> There are 3 types of attribute: text, content (html) and image</li>
                </ul>
                <ul>
                    <li> Members can drag and drop to sort product attributes</li>
                </ul>
                <ul>
                    <li> Members can decide if a product attribute appear in Verification page or Authentication page or both</li>
                </ul>
                <li style="font-weight: bold;">Batch creation</li>
                <ul>
                    <li>
                        Fixed creating batch without product error
                    </li>
                </ul>
                <ul>
                    <li>When member updates disposition of a batch, all codes in that batch will have new disposition</li>
                </ul>

            </ul>
        </div>

        <div class="version-history">
            <h3> 1.2.1 </h3>
            <h5> Release date: 27/10/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Production Partner</li>
                <ul>
                    <li>
                        Assigning production partner to batch. Each label is also assigned a production partner.
                        Members can change product partner of batch or each individual label
                    </li>
                </ul>
                <li style="font-weight: bold;">Roll Code paging</li>
                <ul>
                    <li>
                        Updated paging of codes in roll (500, 1000, 1500, all)
                    </li>
                </ul>
            </ul>
        </div>

        <div class="version-history">
            <h3> 1.2.0 </h3>
            <h5> Release date: 22/09/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Report</li>
                <ul>
                    <li>
                        The system supports scan history report. Members can show all scan history of their product.
                        The scan history includes: Product info, Batch info, label info, scan time, scan longitude/latitude,
                        language, scan status.
                    </li>
                </ul>
            </ul>
        </div>

        <div class="version-history">
            <h3> 1.1.0 </h3>
            <h5> Release date: 11/09/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Traditional Chinese support</li>
                <ul>
                    <li>
                        The system supports the 3rd language: Traditional Chinese
                    </li>
                </ul>
                <li style="font-weight: bold;">Background image</li>
                <ul>
                    <li>
                        Each member can upload a background image for verification page and authentication page.
                    </li>
                </ul>
            </ul>
        </div>

        <div class="version-history">
            <h3> 1.0.2 </h3>
            <h5> Release date: 17/08/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Expiration notes</li>
                <ul>
                    <li>
                        If products don't have expiration date, these products can have expiration notes (e.g. Use within 6 months of opening)
                    </li>
                </ul>
                <ul>
                    <li>
                        Support to show video in product description
                    </li>
                </ul>
            </ul>
        </div>

        <div class="version-history">
            <h3> 1.0.1 </h3>
            <h5> Release date: 17/08/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Export excel</li>
                <ul>
                    <li>
                        Members can export all labels of a roll to excell file
                    </li>
                </ul><li style="font-weight: bold;">Assign roll to batch</li>
                <ul>
                    <li>
                        Fix assigning roll to batch errors, the system automatically find end code if member specify start code and quantity
                    </li>
                </ul>
            </ul>
        </div>

        <div class="version-history">
            <h3> 1.0.0 </h3>
            <h5> Release date: 06/08/2017 </h5>
            <ul>
                <li style="font-weight: bold;">Product Module</li>
                <ul>
                    <li> Members can add/edit/delele product with 2 languages: English and Simplified Chinese</li>
                </ul>
                <ul>
                    <li> Members can add/edit/delele product images, drag and drop to sort images</li>
                </ul>
                <li style="font-weight: bold;">Batch Module</li>
                <ul>
                    <li>
                        Members can add/edit/delete batches. Each batch belongs to one product,
                        members can specify disposition of a batch, production date, expiration date,etc
                    </li>
                </ul>
                <ul>
                    <li>Members can assign roll labels for each batch. One batch can have many rolls, one roll can be used for many batches</li>
                </ul>
                <li style="font-weight: bold;">Roll Module</li>
                <ul>
                    <li>
                        Members can show all their label rolls
                    </li>
                </ul>
                <ul>
                    <li>Member can check/edit all labels in a roll (S/N, roll info, batch info, disposition)</li>
                </ul>
                <li style="font-weight: bold;">Codes Module</li>
                <ul>
                    <li>
                        Members can check all their labels in the system
                    </li>
                </ul>
                <ul>
                    <li>Member can check/edit labels (S/N, roll info, batch info, disposition)</li>
                </ul>
                <li style="font-weight: bold;">User Profile</li>
                <ul>
                    <li>
                        Members can check/edit login information (email, password, avatar)
                    </li>
                </ul>
                <ul>
                    <li>Member can check/edit company information (name, phone, email, website, country, logo)</li>
                </ul>
            </ul>
        </div>
    </div>
@endsection