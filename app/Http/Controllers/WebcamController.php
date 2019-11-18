<?php

namespace App\Http\Controllers;

use App\Webcam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WebcamController extends Controller
{
    public function index(Request $request)
    {

        if(request()->ajax())
        {
            return datatables()->of(Webcam::latest()->get())
                ->addColumn('action', function($data){
                    $button = '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm" title="Edit Data"><i class="fa fa-edit"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('webcam.index');
    }



    public function create()
    {
        //
    }


    public function store(Request $request)
    {

        $rules = array(
            'name'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $image = $request->image;

        $exploded=explode(',',$image);
        $decoded=base64_decode($exploded[1]);

        if(Str::contains($exploded[0],'jpeg')){
            $extention='jpg';
        }else{
            $extention='png';
        }

        $fileName=Str::random(15).'.'.$extention;
        $path=public_path('images/joy').'/'.$fileName;
        file_put_contents($path,$decoded);


        $form_data = array(
            'name'        =>  $request->name,
            'image'             =>  $fileName,

        );

        Webcam::create($form_data);

        return response()->json(['success' => 'Successfully Created']);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = Webcam::find($id);
            return response()->json(['data' => $data]);
        }
    }

    public function update(Request $request)
    {

        $rules = array(
            'name'    =>  'required',

        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }


        $form_data = array(
            'name'        =>  $request->name,
        );



        Webcam::whereId($request->hidden_id)->update($form_data);
        return response()->json(['success' => 'Successful']);

    }


    public function destroy($id)
    {
        $data = Webcam::findOrFail($id);
        $data->delete();
    }
}
