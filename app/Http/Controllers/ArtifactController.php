<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Response;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use App\Artifact;

use Storage;
use DataTables;
use URL;
use App\Http\Controllers\OpenAIVisionController;
use App\QmSheetSubParameter;

class ArtifactController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $data = [];
        if (request()->ajax()) {
            $data = Artifact::leftjoin('qm_sheets', 'artifacts.sheet_id', 'qm_sheets.id')
                ->leftjoin('qm_sheet_parameters', 'artifacts.parameter_id', 'qm_sheet_parameters.id')
                ->leftjoin('qm_sheet_sub_parameters', 'artifacts.sub_parameter_id', 'qm_sheet_sub_parameters.id')
                ->select('qm_sheets.name AS qsheet', 'qm_sheet_parameters.parameter', 'qm_sheet_sub_parameters.sub_parameter', 'artifacts.id', 'artifacts.file');

            return DataTables::of($data)->addIndexColumn()->make(true);
        }

        return view('Artifact.list', compact('data'));
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        // $data=Artifact::all();

        // foreach($data as $item){

        //     $files=explode('/app/',$item->file);

        //     Artifact::where('id',$item->id)->update(['file'=>$files[1]]);

        // }

        // dd($data);

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
    {
        //dd($request->all());die;

        $data = [];
        $storedFilePath = null;

        for ($i = 0; $i <= ($request->totalFile); $i++) {

            if ($request->has('file' . $i)) {

                $path1 = $request->file('file' . $i)->store('public');

                if (!$storedFilePath) {
                    $storedFilePath = storage_path('app/' . $path1);
                }

                $artifact = Artifact::create([
                    'sheet_id'         => $request->sheet_id,
                    'parameter_id'     => $request->parameter_id,
                    'sub_parameter_id' => $request->id,
                    'file'             => $path1,
                    'audit_id'         => isset($request->audit_id) ? $request->audit_id : null,
                    'client_id'        => auth()->user()->client_id
                ]);

                $artifact->file = URL::asset('storage/app/' . $path1);

                $data[] = $artifact;
            }
        }

        if (count($data) > 0) {

            $ocrResponse = null;

            $subParameter = \App\QmSheetSubParameter::find($request->id);

            if (
                $subParameter &&
                $subParameter->ocr == 1 &&
                $storedFilePath &&
                file_exists($storedFilePath)
            ) {


                try {


                    $base64 = base64_encode(
                        file_get_contents($storedFilePath)
                    );
                    $response = Http::withHeaders([

                        'Authorization' => $apiKey,

                        'Content-Type' => 'application/json',

                    ])->post(
                        'https://api.openai.com/v1/responses',
                        [

                            "model" => "gpt-4.1-mini",

                            "input" => [[

                                "role" => "user",

                                "content" => [

                                    [
                                        "type" => "input_text",
                                        "text" => $subParameter->ocr_prompt
                                    ],

                                    [
                                        "type" => "input_image",
                                        "image_url" => "data:image/jpeg;base64," . $base64
                                    ]
                                ]
                            ]]
                        ]
                    );

                    $result = $response->json();

                    $ocrResponse =
                        $result['output'][0]['content'][0]['text']
                        ?? null;
                } catch (\Exception $e) {
                    // dd('here catct 1 ', $e);

                    $ocrResponse = 'OCR Error : ' . $e->getMessage();
                }
            }

            $responseData = [
                'status' => true,
                'msg'    => 'artifact save',
                'data'   => $data
            ];

            // Only append OCR response when OCR is enabled
            if (
                $subParameter &&
                $subParameter->ocr == 1
            ) {
                $responseData['ocr_response'] = $ocrResponse;
            }

            return response()->json($responseData);
        } else {
            // dd('here catct 1 ', $data);

            return response()->json([
                'status' => false,
                'msg'    => 'artifact not save',
                'data'   => $data
            ]);
        }
    }



    public function downloadFile($id)
    {

        $data = Artifact::find(Crypt::decrypt($id));

        // dd(URL::asset('storage/app/'.$data->file));

        // return URL::asset('storage/app/'.$data->file);

        return Response::download(storage_path('app/' . $data->file));
    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        //

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $data = Artifact::where('id', $id)->delete();

        if ($data) {

            return response()->json(['status' => true, 'msg' => 'artifact delete']);
        } else {

            return response()->json(['status' => false, 'msg' => 'artifact not delete']);
        }
    }
}
