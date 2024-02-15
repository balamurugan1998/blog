<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Session;
use DB;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $category = Category::withoutTrashed()->orderBy('category','ASC')->get();
            return view('post.index',compact('category'));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $category = Category::withoutTrashed()->orderBy('category','ASC')->get();
            $returnHTML = view('post.create',compact('category'))->render();

            return response()->json(
                [
                    'success' => true,
                    'html_page' => $returnHTML,
                ]
            );
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'    => 'required|unique:posts',
                'content'  => 'required',
                'category' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    'status'=>400,
                    'errors'=>$validator->messages()
                ]);
            }
            else
            {
                date_default_timezone_set("Asia/Calcutta");
                $category = implode(",",$request->input('category'));
                $post = new Post;
                $post->title = $request->input('title');
                $post->content = $request->input('content');
                $post->category_id = $category;
                $post->user_id = Auth::id();
                $post->publication_date = date("d-m-Y h:i:s A");
                $post->save();
                $post->id;

                return response()->json([
                    'status'  => 200,
                    'success' => true,
                    'message' =>'Post Added Successfully.'
                ]);
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = Post::withoutTrashed()->select("posts.*",\DB::raw("GROUP_CONCAT(categories.category) as category"))
                ->leftjoin("categories",\DB::raw("FIND_IN_SET(categories.id,posts.category_id)"),">",\DB::raw("'0'"))
                ->where('posts.id',$id)
                ->groupBy('posts.id')
                ->first();

            if($post)
            {
                $returnHTML = view('post.view',compact('post'))->render();

                return response()->json([
                        'success' => true,
                        'html_page' => $returnHTML,
                    ]
                );
            }
            else
            {
                return response()->json([
                    'status'  => 404,
                    'message' => 'No Post Found.'
                ]);
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $post = Post::find($id);
            $category = Category::withoutTrashed()->orderBy('category','ASC')->get();
            if($post)
            {
                $returnHTML = view('post.edit',compact('post','category'))->render();

                return response()->json([
                        'success' => true,
                        'html_page' => $returnHTML,
                    ]
                );
            }
            else
            {
                return response()->json([
                    'status'  => 404,
                    'message' => 'No Post Found.'
                ]);
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'=> ['required',Rule::unique('posts')->ignore($id)],
                'content'  => 'required',
                'category' => 'required',
            ]); 
    
            if($validator->fails())
            {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages()
                ]);
            }
            else
            {
                $post = Post::find($request->id);
                if($post)
                {
                    $category = implode(",",$request->input('category'));
                    $post->title    = $request->input('title');
                    $post->content  = $request->input('content');
                    $post->category_id = $category;
                    $post->update();
                    $post->id;
                    
                    return response()->json([
                        'status'  => 200,
                        'success' => true,
                        'message' => 'Post Updated.'
                    ]);
                }
                else
                {
                    return response()->json([
                        'status'=> 404,
                        'error'=>'No Post Found.'
                    ]);
                }
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::find($id);
            if($post)
            {
                $post->delete();
                return response()->json([
                    'status'  => 200,
                    'success' => true,
                    'message' => 'Post Deleted'
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'success' => false,
                    'message'=>'No Post Found.'
                ]);
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function dashboard(Request $request){
        try {
            $post_count = Post::where('user_id',Auth::id())->count();
            return view('post.dashboard',compact('post_count'));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function post_datatable(Request $request){
        try {
            if ($request->ajax()) {
                
                $all_datas = Post::where('user_id',Auth::id())->latest();
                if(isset($request->category_val)){
                    $all_datas->whereRaw("find_in_set('" . $request->category_val . "',category_id)");
                }
                $all_datas = $all_datas->get();
        
                return Datatables::of($all_datas)
                    ->addColumn('select_all', function ($all_data) {
                        return '<input class="tabel_checkbox" name="contents[]" type="checkbox" onchange="table_checkbox(this)" id="'.$all_data->id.'">';
                    })
                    ->addColumn('action', function ($all_data) {
                        $edit_route = route("posts.edit",$all_data->id);
                        $view_route = route("posts.show",$all_data->id);

                        return '<div class="">
                            <div class="btn-group mr-2 mb-2 mb-sm-0">
                                <a href="#!" data-url="'.$view_route.'" data-size="xl" data-ajax-popup="true" data-ajax-popup="true"
                                    data-bs-original-title="View Post" class="btn btn-primary waves-light waves-effect">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="#!" data-url="'.$edit_route.'" data-size="xl" data-ajax-popup="true" data-ajax-popup="true"
                                    data-bs-original-title="Edit Post" class="btn btn-primary waves-light waves-effect">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" id="delete_post" data-id="'.$all_data->id.'" class="btn btn-primary waves-light waves-effect">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>';
                    })
                    ->rawColumns(['select_all','action'])
                    ->make(true);
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function checkduplicate(Request $request){
        $formName   = $request->form_name;
        $checktitle = $request->title;
        $id         = $request->id;

        if ($formName == "post") {
            $getCheckVal = Post::whereRaw("LOWER(REPLACE(`title`, ' ' ,''))  = ?", [
                    strtolower(str_replace(" ", "", $checktitle)),
                ]);
            if($id != null){ 
                $getCheckVal->whereNot('id',$id);
            }
            $getCheckVal = $getCheckVal->first();
        }
        else {
            $getCheckVal = "Not Empty";
        }

        if ($getCheckVal == null) {
            echo "true"; // return 1; //Success
        } else {
            echo "false"; // return 0; //Error
        }
    }

    public function post_multi_delete(Request $request) {
        try {
            $all_id = $request->all_id;

            foreach($all_id as $id){
                $post = Post::find($id);
                $post->delete();
            }
            
            return response()->json([
                'status'=> 200,
                'success' => true,
                'message'=> 'Posts Deleted!'
            ]);
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
