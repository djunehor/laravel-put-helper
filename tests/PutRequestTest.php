<?php
namespace Djunehor\PutHelper\Test;

use Djunehor\PutHelper\PutRequestMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PutRequestTest extends TestCase
{
    public function setUp() : void {
        parent::setUp();
        Route::get('/get-request', function () {
            return now()->toString();
        });
        Route::put('/put-request', function () {
            return now()->toString();
        });
        Route::put('put-upload', function () {
            return $this->putRequestHandler();
        });
        Route::put('put-validated', function () {
            return $this->putRequestHandlerValidated();
        });
    }

    public function putRequestHandler()
    {
        $request = request()->all();
        $message = "No file to upload";
        $destinationPath = null;
        if (isset($request['sample_file'])) {
            $file = $request['sample_file'];
            $destinationPath = public_path('uploads');
            $filename = $file->getClientOriginalName();
            $file->move($destinationPath, $file->getClientOriginalName());

           $message = "File $filename uploaded successfully";
        }

        return response([
            'message' => $message,
            'filepath' => $destinationPath,
            'inputs' => $request
        ]);
    }

    public function putRequestHandlerValidated()
    {
        $request = request()->all();
        $validator = validator($request, [
            'abomi' => 'required|put_file'
        ]);

        if($validator->fails()) {
            return response($validator->errors(), 422);
        }
        $message = "No file to upload";
        $destinationPath = null;
        if (isset($request['sample_file'])) {
            $file = $request['sample_file'];
            $destinationPath = public_path('uploads');
            $filename = $file->getClientOriginalName();
            $file->move($destinationPath, $file->getClientOriginalName());

           $message = "File $filename uploaded successfully";
        }

        return response([
            'message' => $message,
            'filepath' => $destinationPath,
            'inputs' => $request
        ]);
    }

    public function testMiddleWareIsCalledOnEveryRequest()
    {
        $this->get('/get-request')->assertSuccessful();
        $this->assertEquals(session()->get(PutRequestMiddleware::class), true);
    }

    public function testMiddleWareIsImplementedOnEveryPutRequest()
    {
        session()->put('_token', $token = Str::random(16));
        $this->put('/put-request')->assertSuccessful();
        $this->assertEquals(session()->get(PutRequestMiddleware::class), $token);
    }

    public function testOnNoFileSent()
    {

       $request = $this->put('/put-upload');
       $request->assertSuccessful()
           ->assertSee("No file to upload");
    }

    public function testFailOnNoRequiredFile()
    {

        $request = $this->put('/put-validated', ['abomi' => 'string']);
        $request->assertStatus(422);
    }

    public function testFileUploadsSuccessfully()
    {
        Storage::fake('avatars');

        $filename = 'avatar.jpg';
        $file = UploadedFile::fake()->image('avatar.jpg');
        $payload = [
            'sample_file' => $file,
            'another' => 'samsmasas',
            'another2' => 'asaskalska'
        ];
        $request = $this->put('/put-upload', $payload);
        $request->assertSuccessful();

        $responseData = $request->getOriginalContent();
        $this->assertStringContainsString("File $filename uploaded successfully", $responseData['message']);
        $this->assertFileExists($responseData['filepath']);

        sort($payload);
        $inputs = $responseData['inputs'];
        sort($inputs);

        $this->assertEquals($payload, $inputs);
    }
}
