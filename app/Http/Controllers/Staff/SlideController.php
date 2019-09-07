<?php

namespace App\Http\Controllers\Staff;
use App\Code;
use App\Disposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Slide;
use App\Utils;

class SlideController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'staff.slides.index' );
    }

    public function getSlideForm() {
        return view( 'staff.slides.form');
    }

    public function getSlideList() {
        return view( 'staff.slides.list' );
    }    

    public function getSlide(Request $request) {
        $id = $request->input('id');
        $post = DB::table('slides')
                ->select('slides.*', 'files.location', 'files.original_name')
                ->join('files', 'slides.file_id', '=', 'files.id')
                ->where(['slides.id' => $id, 'slides.deleted' => 0])
                ->first();

        return response()->json($post);
    }

    public function getLargestSlidePriority() {
        $priority = DB::table('slides')->max('priority') ?? 0;
        return response()->json($priority);
    }    

    public function getSlides(Request $request) {
        $pageSize = $request->input('pageSize');
        $slides = DB::table('slides')->select('slides.*', 'files.location', 'files.original_name')
                  ->leftJoin('files', 'slides.file_id', '=', 'files.id')
                  ->where('slides.deleted', 0)
                  ->orderBy('priority', 'asc')
                  ->paginate( $pageSize );

        return response()->json($slides);
    }

    public function deleteSlides(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $post = Slide::find($id);

                if($post)
                {
                    $post->deleted = 1;
                    $post->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function addSlideImage( Request $request )
    {
        try {
            DB::beginTransaction();
            $slide = new Slide();
            $slide->file_id = $request->input('file_id');

            $slide->save();

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function getSlideImages( Request $request )
    {
        $pageSize = $request->input('pageSize');
        $slide_id = $request->input('slide_id');
        $slides = DB::table('slide_images')->select('slide_images.*', 'files.location', 'files.original_name')
            ->join('files','slide_images.file_id','=','files.id')
            ->where(['slide_images.slide_id' => $slide_id])
            ->orderBy('slide_images.priority', 'asc')->paginate( $pageSize );

        return response()->json($slides);
    }

    public function deleteSlideImage( Request $request )
    {
        try {
            DB::beginTransaction();
            $slide_id = $request->input('slide_id');
            $file_id = $request->input('file_id');

            SlideImage::where(['slide_id'=>$slide_id, 'file_id'=>$file_id])->delete();

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function updateSlidePriorities ( Request $request) {
        try {
            DB::beginTransaction();
            $slide_priorities = $request->input('slide_priorities');
            
            foreach ($slide_priorities as $slide_priority) {
                $slide_id = $slide_priority['id'];
                $slide_priority = $slide_priority['priority'];

                $slide = Slide::find($slide_id);
                $slide->priority = $slide_priority;
                $slide->save();
            }

            DB::commit();
            return json_encode([
                'success' => true,
                'slides' => $slide
            ]);
        } catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'success' => false
            ]);            
        }
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $slide_id = $request->input('id');
            $slide = Slide::find($slide_id);
            
            if (!$slide) {
                $slide = new Slide();
                $slide->created_by = auth()->user()->id;
            } else {
                $slide->updated_by = auth()->user()->id;
            }

            // store new slide into database
            $slide->file_id = $request->input('slide_image_file_id') ?? null;
            $slide->title = $request->input('title') ?? null;
            $slide->priority = $request->input('priority');
            if ($request->input('language')) {
                $slide->language = $request->input('language');
            }
            $slide->published = $request->input('published') ? 1 : 0;
            
            $slide->save();

            DB::commit();
            return json_encode([
                'slide_id' => $slide->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            var_dump($e->getMessage());
            return json_encode([
                'slide_id' => 0
            ]);
        }
    }

}